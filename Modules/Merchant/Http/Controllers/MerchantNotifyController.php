<?php

namespace Modules\Merchant\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\MerchantAdsRequest;
use Modules\Merchant\Http\Requests\NotifyRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\MerchantAdsRepositoryInterface;
use Modules\Merchant\Repositories\MerchantNotificationsRepositoryInterface;
use Psy\Util\Json;
use Yajra\DataTables\DataTables;

class MerchantNotifyController extends Controller
{
    protected $merchantNotifyRepository;

    protected $logActionMerchantRepository;

    public function __construct(
        MerchantNotificationsRepositoryInterface $merchantNotificationsRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->merchantNotifyRepository = $merchantNotificationsRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('notify.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn!'));
        }
        if($request->ajax()){
            return $this->merchantNotifyRepository->list($request);
        }
        return view('merchant::notify.list');
    }

    public function create()
    {
        if (!MerchantCan::do('notify.edit')) {
            return redirect()->route('merchant.notify.list')->with('error', __('Tài khoản không có quyền hạn!'));
        }
        return view('merchant::notify.create');
    }

    public function store(NotifyRequest $request)
    {
        if (!MerchantCan::do('notify.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        } else {
            $response = $this->merchantNotifyRepository->storeNotify($request);

            if($response['status']){
                $attribute['content_request'] = [
                    'ID' => $response['data']
                ];

                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    // Delete Notify
    public function destroy(Request $request, $notifyId)
    {
        if (!MerchantCan::do('notify.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        } else {
            $notify = $this->merchantNotifyRepository->findById($notifyId);
            if (!$notify) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Thông báo không tồn tại!')];
            } elseif (!MerchantCan::do('notify.change', $notify)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn với thông báo này!')];
            } else {
                $response = $this->merchantNotifyRepository->destroy($notify);

                if ($response['status']) {
                    $attribute['content_request'] = [
                        'ID' => $notify->id,
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    // Edit Notify
    public function edit($notifyId)
    {
        if (!MerchantCan::do('notify.edit')) {
            return redirect()->route('merchant.notify.list')->with('error', __('Tài khoản không có quyền hạn!'));
        }
        $notify = $this->merchantNotifyRepository->findById($notifyId);

        if(!$notify){
            return redirect()->route('merchant.notify.list')->with('error', __('Thông báo không tồn tại!'));
        }

        return view('merchant::notify.edit', compact('notify'));
    }


    // Update Notify
    public function update(NotifyRequest $request, $notifyId)
    {
        $notify = $this->merchantNotifyRepository->findById($notifyId);
        if (!$notify) {
            $message = __('Tài khoản không tồn tại');
        } elseif (!MerchantCan::do('notify.change', $notify)) {
            $message = __('Tài khoản không có quyền hạn với User này');
        }
        if (!$notify || !MerchantCan::do('notify.change', $notify)) {
            return ['status' => false, 'alert' => 'error', 'message' => $message];
        } else {
            $response = $this->merchantNotifyRepository->updateNotify($notify, $request);
            if ($response['status']) {
                $attribute['content_request'] = [
                    'ID' => $notify->id,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }
            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }
}
