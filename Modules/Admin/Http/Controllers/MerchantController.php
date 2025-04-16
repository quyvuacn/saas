<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Admin\Http\Requests\MerchantRequest;
use Modules\Admin\Http\Requests\MerchantStoreRequest;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\PermissionRepositoryInterface;

class MerchantController extends Controller
{
    protected $merchantRepository;
    protected $permissionRepository;
    protected $logActionAdminRepository;

    public function __construct(
        MerchantRepositoryInterface $merchantRepository,
        PermissionRepositoryInterface $permissionRepository,
        LogActionAdminRepositoryInterface $logActionAdminRepository
    )
    {
        $this->middleware('auth:admin');
        $this->middleware('change.password.account');

        $this->logActionAdminRepository = $logActionAdminRepository;
        $this->merchantRepository = $merchantRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function list()
    {
        if (!AdminCan::do('adm.merchant.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchants = $this->merchantRepository->findMerchantActive();
        $merchants = $merchants->with('merchantInfo')->get();
        return view('admin::merchant.list', compact('merchants'));
    }

    public function edit($merchantId)
    {
        if (!AdminCan::do('adm.merchant.edit')) {
            return redirect()->route('admin.merchant.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchant = $this->merchantRepository->findMerchantInfoActive($merchantId);
        if(empty($merchant)){
            return redirect()->route('admin.merchant.list')
                ->with('error', __('Merchant không tồn tại'));
        }
        return view('admin::merchant.edit', compact('merchant'));
    }

    public function store(MerchantStoreRequest $request, $merchantId)
    {
        if (!AdminCan::do('adm.merchant.edit')) {
            return redirect()->route('admin.merchant.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchant = $this->merchantRepository->findMerchantInfoActive($merchantId);
        if(empty($merchant)){
            return redirect()->route('admin.merchant.request')
                ->with('error', __('Merchant không tồn tại'));
        }
        $this->merchantRepository->updateMerchantInfo($merchant, $request);

        $attribute['content_request'] = [
            'ID' => $merchant->id,
            'Merchant Name' => $merchant->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.merchant.list')
            ->with('message', __('Update thông tin Merchant thành công'));
    }

    public function request()
    {
        if (!AdminCan::do('adm.merchant_request.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchants = $this->merchantRepository->all();
        return view('admin::merchant.request', compact('merchants'));
    }

    public function approveRequest(MerchantRequest $request, $merchantRequest)
    {
        if (!AdminCan::do('adm.merchant_request.edit')) {
            return redirect()->route('admin.merchant.request')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchantRequest = $this->merchantRepository->find($merchantRequest);
        if (
            $merchantRequest->status != $merchantRequest::REQUEST_NEW &&
            $merchantRequest->status != $merchantRequest::REQUEST_WAITING
        ) {
            abort(404);
        }

        $this->merchantRepository->approveMerchant($merchantRequest, $request);

        if($request->merchant_audit != 1){
            $dataMail = [
                'view' => 'admin::email.merchant-register',
                'to' => $merchantRequest->email,
                'data' => ['merchant' => $merchantRequest],
                'subject' => '[1giay.vn] Thông báo hủy duyệt tài khoản Merchant!'
            ];
            sendMailCustom($dataMail);
        }

        $attribute['content_request'] = [
            'ID' => $merchantRequest->id,
            'Merchant Name' => $merchantRequest->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.merchant.request')
            ->with('message', __('Update request thành công'));
    }

    public function finalApproveRequest($merchantRequest, Request $request)
    {
        $status = 0;

        if (!AdminCan::do('adm.merchant_request.edit')) {
            return json_encode(['status' => $status]);
        }

        $merchantRequest = $this->merchantRepository->find($merchantRequest);
        if (empty($merchantRequest) || $merchantRequest->status != $merchantRequest::REQUEST_WAITING_SETUP) {
            return json_encode(['status' => $status]);
        }
        if ($this->merchantRepository->finalApproveMerchant($merchantRequest)) {
            $status     = 1;
            $superAdmin = $this->permissionRepository->superAdmin();
            if ($superAdmin) {
                $merchantRequest->permissionPivots()->delete();
                $merchantRequest->permissions()->attach($superAdmin->id, ['table' => MERCHANT]);
            }
        }

        $attribute['content_request'] = [
            'ID' => $merchantRequest->id,
            'Merchant Name' => $merchantRequest->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        $dataMail = [
            'view' => 'admin::email.merchant-register',
            'to' => $merchantRequest->email,
            'data' => ['merchantRequest' => $merchantRequest],
            'subject' => '[1giay.vn] Thông báo duyệt tài khoản Merchant!'
        ];
        sendMailCustom($dataMail);

        return json_encode(['status' => $status]);
    }

    public function requestDetail($merchantRequest)
    {
        if (!AdminCan::do('adm.merchant_request.edit')) {
            return redirect()->route('admin.merchant.request')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchantRequest = $this->merchantRepository->find($merchantRequest);
        if(empty($merchantRequest)){
            abort(404);
        }
        if (
            $merchantRequest->status != $merchantRequest::REQUEST_NEW &&
            $merchantRequest->status != $merchantRequest::REQUEST_WAITING
        )
            return redirect()->route('admin.merchant.request');
        $dateActive = $merchantRequest->merchantInfo->merchant_active_date ? date('d/m/Y', strtotime($merchantRequest->merchantInfo->merchant_active_date)) : '';
        return view('admin::merchant.request-detail', compact('merchantRequest', 'dateActive'));
    }

    public function delete($merchant, Request $request)
    {
        $status = 0;
        if (!AdminCan::do('adm.merchant_request.edit')) {
            return json_encode(['status' => $status]);
        }

        $merchant = $this->merchantRepository->find($merchant);
        if (empty($merchant) || $merchant->machine_count > 0) {
            return json_encode(['status' => $status]);
        }
        if($this->merchantRepository->delete($merchant->id)){
            $status = 1;
        }

        $attribute['content_request'] = [
            'ID' => $merchant->id,
            'Merchant Name' => $merchant->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return json_encode(['status' => $status]);
    }
}
