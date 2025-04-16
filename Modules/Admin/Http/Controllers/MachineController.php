<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Admin\Http\Requests\MachineRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Http\Requests\MachineRequestBackRequest;
use Modules\Admin\Http\Requests\MerchantRequestMachineRequest;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;
use Modules\Admin\Repositories\MachineAttributeRepositoryInterface;
use Modules\Admin\Repositories\MachineAttributeValueRepositoryInterface;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MachineRequestBackRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\MerchantRequestMachineRepositoryInterface;
use Modules\Admin\Repositories\ProductListRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRepositoryInterface;

class MachineController extends Controller
{
    protected $machineRepository;
    protected $merchantRepository;
    protected $machineAttributeRepository;
    protected $machineAttributeValueRepository;
    protected $subscriptionRepository;
    protected $merchantRequestMachineRepository;
    protected $machineRequestBackRepository;
    protected $productListRepository;
    protected $logActionAdminRepository;

    public function __construct(
        MachineRepositoryInterface $machineRepository,
        MerchantRepositoryInterface $merchantRepository,
        MachineAttributeRepositoryInterface $machineAttributeRepository,
        MachineAttributeValueRepositoryInterface $machineAttributeValueRepository,
        SubscriptionRepositoryInterface $subscriptionRepository,
        MerchantRequestMachineRepositoryInterface $merchantRequestMachineRepository,
        MachineRequestBackRepositoryInterface $machineRequestBackRepository,
        ProductListRepositoryInterface $productListRepository,
        LogActionAdminRepositoryInterface $logActionAdminRepository
    )
    {
        $this->middleware('auth:admin');
        $this->middleware('change.password.account');

        $this->logActionAdminRepository = $logActionAdminRepository;
        $this->machineRepository = $machineRepository;
        $this->merchantRepository = $merchantRepository;
        $this->machineAttributeRepository = $machineAttributeRepository;
        $this->machineAttributeValueRepository = $machineAttributeValueRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->merchantRequestMachineRepository = $merchantRequestMachineRepository;
        $this->machineRequestBackRepository = $machineRequestBackRepository;
        $this->productListRepository = $productListRepository;

    }

    public function list(Request $request)
    {
        if (!AdminCan::do('adm.machine.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $merchant = (int) $request->merchant_id;
        if ($request->ajax()) {
            return $this->machineRepository->list($request);
        }
        return view('admin::machine.list', compact('merchant'));
    }

    public function create()
    {
        if (!AdminCan::do('adm.machine.edit')) {
            return redirect()->route('admin.machine.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $merchants = $this->merchantRepository->findMerchantParent()->get();
        $listAttribute = $this->machineAttributeRepository->all();
        return view('admin::machine.create', compact('listAttribute', 'merchants'));
    }

    public function edit($machine)
    {
        if (!AdminCan::do('adm.machine.edit')) {
            return redirect()->route('admin.machine.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $machine = $this->machineRepository->find($machine);
        if(empty($machine))
            abort(404);
        $merchants = $this->merchantRepository->findMerchantParent()->get();
        $attributeMachine = $this->machineAttributeValueRepository->findAttributeValueByMachine($machine->id);
        $listAttribute = $this->machineAttributeRepository->all();
        $listTrayPack = $this->productListRepository->getListByMachineId($machine->id);

        $tray = [];
        foreach ($listTrayPack as $k => $v){
            $tray[$v['tray_id']][] = $v;
        }
        $totalTray = count($tray);
        $isEdit = $this->subscriptionRepository->isEditMachine($machine);

        return view('admin::machine.edit', compact('machine', 'listAttribute', 'attributeMachine', 'merchants', 'isEdit', 'totalTray', 'tray'));
    }

    public function update(MachineRequest $request, $machine)
    {
        if (!AdminCan::do('adm.machine.edit')) {
            return redirect()->route('admin.machine.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $machine = $this->machineRepository->find($machine);

        $isEdit = $this->subscriptionRepository->isEditMachine($machine);
        if (!$isEdit) {
            abort(404);
        }

        $arrAttr = $arrValue = [];
        if (!empty($request->attribute_name)) {
            list($arrAttr, $arrValue) = $this->buildAttributeMachine($request);
        }

//        try {
            if (!empty($request->merchant) && $request->merchant != $machine->merchant_id) {

                $subscription = $this->subscriptionRepository->findBySubscriptionRequest($request->merchant, $machine->id);

                $attributeSubscription = [
                    'merchant_id' => $request->merchant,
                    'machine_id' => $machine->id,
                ];
                $dateAdded = convertDateFlatpickr($request->date_added);
                $dateExpire = date('Y-m-d', strtotime("{$dateAdded} +{$request->month_subscription} month"));
                $attributeSubscription['date_expire_option'] = $dateExpire;
                if (!empty($subscription)) {
                    $this->subscriptionRepository->updateSubscriptionBySubscriptionRequest($subscription, $attributeSubscription);
                } else {
                    $this->subscriptionRepository->createSubscription($attributeSubscription);
                }

                $merchant = $this->merchantRepository->findMerchantInfoActive($request->merchant);

                $dataMail = [
                    'view' => 'admin::email.merchant-subscription',
                    'to' => $merchant->email,
                    'data' => ['dateExpire' => $dateExpire, 'machine' => $machine],
                    'subject' => '[1giay.vn] Thông báo cấp thêm máy bán hàng!'
                ];
                sendMailCustom($dataMail);
            }

            $this->machineRepository->updateMachine($machine, $request, $arrAttr);

            if (empty($request->merchant) && !empty($machine->merchant_id)) {
                $this->merchantRepository->updateMachineCount($machine->merchant_id);
            }

            if (!empty($request->merchant) && $request->merchant != $machine->merchant_id) {
                $this->merchantRepository->updateMachineCount($request->merchant);
                $this->merchantRepository->updateMachineCount($machine->merchant_id);
            }

            $this->machineAttributeValueRepository->deleteAttributeValueByMachineId($machine->id);

            $this->machineAttributeValueRepository->createAttributeValue($machine, $arrAttr, $arrValue);

            $this->productListRepository->updateProductList($machine, $request);

            $attribute['content_request'] = [
                'ID' => $machine->id,
                'Machine' => $machine->name
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);

            return redirect()->route('admin.machine.list')
                ->with('message', __('Update thông tin máy bán hàng thành công'));

//        } catch (\Exception $e){
//            Log::error('[UpdateMachine][UpdateError]--' . $e->getMessage());
//            return redirect()->route('admin.machine.list')
//                ->with('error', __('Có lỗi xảy ra'));
//        }
    }

    public function createPost(MachineRequest $request)
    {
        if (!AdminCan::do('adm.machine.edit')) {
            return redirect()->route('admin.machine.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $arrAttr = $arrValue = [];

        if (!empty($request->attribute_name)) {
            list($arrAttr, $arrValue) = $this->buildAttributeMachine($request);
        }

        $machineCount = (!empty($request->machine_count) && $request->machine_count > 0) ? (int)$request->machine_count : 1;
        $maxId = $this->machineRepository->getMaxId();
        $strName = $request->name;
        try {
            for ($i = 0; $i < $machineCount; $i++) {
                $request->name = $strName . ' - ' . str_pad($maxId + $i + 1, 5, 0, STR_PAD_LEFT);
                $model = $this->machineRepository->createMachine($request, $arrAttr);
                $this->machineAttributeValueRepository->createAttributeValue($model, $arrAttr, $arrValue);

                if(!empty($request->tray_count) && !empty($request->pack_count) && !empty($request->max_product_pack)){
                    $this->productListRepository->createProductList($model, $request);
                }

                if (!empty($request->merchant)) {
                    $dateAdded = convertDateFlatpickr($request->date_added);
                    $dateExpire = date('Y-m-d', strtotime("{$dateAdded} +{$request->month_subscription} month"));
                    $attributeSubscription = [
                        'merchant_id' => $request->merchant,
                        'machine_id' => $model->id,
                        'date_expire_option' => $dateExpire
                    ];
                    $subscription = $this->subscriptionRepository->createSubscription($attributeSubscription);

                    $merchant = $this->merchantRepository->findMerchantInfoActive($request->merchant);

                    $dataMail = [
                        'view' => 'admin::email.merchant-subscription',
                        'to' => $merchant->email,
                        'data' => ['dateExpire' => $subscription->date_expiration, 'machine' => $model],
                        'subject' => '[1giay.vn] Thông báo cấp thêm máy bán hàng!'
                    ];
                    sendMailCustom($dataMail);
                }

                $attribute['content_request'] = [
                    'ID' => $model->id,
                    'Machine' => $model->name
                ];
                $this->logActionAdminRepository->createAction($request, $attribute);
            }

            if (!empty($request->merchant)) {
                $this->merchantRepository->updateMachineCount($request->merchant);
            }
        } catch (\Exception $e){
            Log::error('[Machine][createMachine][creatError]--' . $e->getMessage());
            return redirect()->route('admin.machine.list')
                ->with('message', __('Thêm mới máy bán hàng thành công'));
        }
        return redirect()->route('admin.machine.list')
            ->with('message', __('Thêm mới máy bán hàng thành công'));
    }

    protected function buildAttributeMachine($request)
    {
        $arrAttr = $arrValue = [];
        $listAttribute = $this->machineAttributeRepository->findByIds($request->attribute_name);
        $listAttribute = array_combine(array_column($listAttribute, 'id'), array_column($listAttribute, 'attribute_name'));
        $arrAttrValue = $request->attribute_value;
        foreach ($request->attribute_name as $k => $v) {
            if (!empty($listAttribute[$v]) && !empty($arrAttrValue[$k])) {
                $arrAttr[$v] = ['name' => $listAttribute[$v], 'value' => $arrAttrValue[$k]];
                $arrValue[$v] = $arrAttrValue[$k];
            }
        }
        return [$arrAttr, $arrValue];
    }

    public function delete($machine, Request $request)
    {
        $status = 0;
        if (!AdminCan::do('adm.machine.edit')) {
            return ['status' => $status, 'msg' => 'Tài khoản không có quyền hạn này!'];
        }
        $machine = $this->machineRepository->find($machine);
        if(empty($machine)){
            return ['status' => $status, 'msg' => 'Machine không hợp lệ'];
        }
        if(!empty($machine->merchant_id)){
            return ['status' => $status, 'msg' => 'Machine đang thuộc sở hữu của merchant không thể xóa'];
        }
        if($this->machineRepository->deleteMachine($machine)){
            $status = 1;
        }

        $attribute['content_request'] = [
            'ID' => $machine->id,
            'Machine' => $machine->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return ['status' => $status, 'message' => 'Success'];
    }

    public function request(Request $request)
    {
        if (!AdminCan::do('adm.machine_request.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        if ($request->ajax()) {
            return $this->merchantRequestMachineRepository->list($request);
        }
        return view('admin::machine.request');
    }

    public function requestDetail($merchantRequest)
    {
        if (!AdminCan::do('adm.machine_request.edit')) {
            return redirect()->route('admin.machine.request')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchantRequest = $this->merchantRequestMachineRepository->findRequest($merchantRequest);
        if(empty($merchantRequest)){
            abort(404);
        }
        return view('admin::machine.request-detail', compact('merchantRequest'));
    }

    public function approveRequest(MerchantRequestMachineRequest $request, $merchantRequest)
    {
        if (!AdminCan::do('adm.machine_request.edit')) {
            return redirect()->route('admin.machine.request')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $merchantRequest = $this->merchantRequestMachineRepository->find($merchantRequest);
        if(empty($merchantRequest))
            abort(404);

        if ($merchantRequest->status == $merchantRequest::REQUEST_NEW || $merchantRequest->status == $merchantRequest::REQUEST_WAITING_AUDIT) {
            $this->merchantRequestMachineRepository->updateMerchantRequestMachine($merchantRequest, $request);
        }

        $attribute['content_request'] = [
            'ID' => $merchantRequest->id,
            'Content Request' => $merchantRequest->title
        ];

        $dataMail = [
            'view' => 'admin::email.merchant-request-machine-status',
            'to' => $merchantRequest->merchant->email,
            'data' => ['merchantRequest' => $merchantRequest],
            'subject' => '[1giay.vn] Thông báo duyệt yêu cầu cấp máy bán hàng!'
        ];

        if($request->merchant_audit != 1 && !empty($merchantRequest->merchant->email)) {
            $dataMail['view'] = 'admin::email.merchant-request-machine-cancel';
            $dataMail['subject'] = '[1giay.vn] Thông báo hủy yêu cầu cấp thêm máy bán hàng!';
        }

        sendMailCustom($dataMail);

        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.machine.request')
            ->with('message', __('Update request thành công'));
    }

    public function finalApproveRequest($merchantRequest, Request $request)
    {
        if (!AdminCan::do('adm.machine_request.edit')) {
            return json_encode(['status' => 0, 'message' => 'Tài khoản không có quyền hạn này']);
        }
        $merchantRequest = $this->merchantRequestMachineRepository->find($merchantRequest);
        if(empty($merchantRequest) || $merchantRequest->status != $merchantRequest::REQUEST_WAITING)
            return json_encode(['status' => 0, 'message' => 'Yêu cầu không hợp lệ']);

        $this->merchantRequestMachineRepository->finalUpdateMerchantRequestMachine($merchantRequest);

        $attribute['content_request'] = [
            'ID' => $merchantRequest->id,
            'Content Request' => $merchantRequest->title
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);
        return json_encode(['status' => 1, 'message' => 'Success']);
    }

    public function finalRequestProcessing($merchantRequest, Request $request)
    {
        if (!AdminCan::do('adm.machine_request.edit')) {
            return json_encode(['status' => 0, 'message' => 'Tài khoản không có quyền hạn này']);
        }
        $merchantRequest = $this->merchantRequestMachineRepository->find($merchantRequest);
        if(empty($merchantRequest) || $merchantRequest->status != $merchantRequest::REQUEST_SETUP_SUCCESS)
            return json_encode(['status' => 0, 'message' => 'Yêu cầu không hợp lệ']);

        $this->merchantRequestMachineRepository->finalMerchantRequestMachineProcessing($merchantRequest);


        $attribute['content_request'] = [
            'ID' => $merchantRequest->id,
            'Content Request' => $merchantRequest->title
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        if(!empty($merchantRequest->merchant->email)) {
            $dataMail = [
                'view' => 'admin::email.merchant-request-machine-approve',
                'to' => $merchantRequest->merchant->emai,
                'data' => ['merchantRequest' => $merchantRequest],
                'subject' => '[1giay.vn] Thông báo hoàn tất duyệt yêu cầu cấp thêm máy bán hàng!'
            ];
            sendMailCustom($dataMail);
        }

        return json_encode(['status' => 1, 'message' => 'Success']);
    }

    public function requestProcessing(Request $request)
    {
        $arrType = [
            'machine_request_back' => 'Trả máy bán hàng',
            'merchant_request_machine' => 'Cấp thêm máy bán hàng'
        ];
        $arrStatus = [
            'machine_request_back' => 'Chờ thu hồi máy',
            'merchant_request_machine' => 'Đang bàn giao máy'
        ];
        $result = $this->machineRequestBackRepository->listProcessing();
        return view('admin::machine.request-processing', compact('result', 'arrType', 'arrStatus'));
    }

    public function requestBack(Request $request)
    {
        if (!AdminCan::do('adm.machine_request_back.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        if ($request->ajax()) {
            return $this->machineRequestBackRepository->list($request);
        }
        return view('admin::machine.request-back');
    }

    public function requestBackDetail($machineRequest)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return redirect()->route('admin.machine.requestBack')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $machineRequest = $this->machineRequestBackRepository->find($machineRequest);
        if(empty($machineRequest) || $machineRequest->status != $machineRequest::REQUEST_NEW)
            abort(404);

        $machineRequest->with('merchantInfo');
        return view('admin::machine.request-back-detail', compact('machineRequest'));
    }

    public function approveRequestBack(MachineRequestBackRequest $request, $machineRequest)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return redirect()->route('admin.machine.requestBack')->with('error', 'Tài khoản không có quyền hạn này!');
        }
        $machineRequest = $this->machineRequestBackRepository->find($machineRequest);
        if(empty($machineRequest) || $machineRequest->status != $machineRequest::REQUEST_NEW)
            abort(404);

        $machine = $this->machineRepository->find($machineRequest->machine_id);

        if (empty($machine))
            return redirect()->route('admin.machine.requestBack')->with('message', __('Machine không tồn tại'));

        $this->machineRequestBackRepository->approveRequestBack($machineRequest, $request);

        if(!empty($machineRequest->merchantInfo->email)) {
            $dataMail = [
                'view' => 'admin::email.machine-request-back-approve',
                'to' => $machineRequest->merchantInfo->email,
                'data' => ['machineRequest' => $machineRequest],
                'subject' => '[1giay.vn] Thông báo duyệt yêu cầu trả máy bán hàng!'
            ];
            sendMailCustom($dataMail);
        }

        $attribute['content_request'] = [
            'ID' => $machineRequest->id,
            'Content Request' => $machineRequest->request_content
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.machine.requestBack')->with('message', __('Duyệt thành công'));
    }

    public function finalApproveRequestBack($machineRequest, Request $request)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return ['status' => 0, 'message' => 'Tài khoản không có quyền hạn này'];
        }

        $machineRequest = $this->machineRequestBackRepository->find($machineRequest);
        if(empty($machineRequest) || $machineRequest->status != $machineRequest::REQUEST_WAITING_BACK)
            return ['status' => 0, 'message' => 'Yêu cầu không hợp lệ'];

        $this->machineRequestBackRepository->finalApproveRequestBack($machineRequest);

        $attribute['content_request'] = [
            'ID' => $machineRequest->id,
            'Content Request' => $machineRequest->request_content
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return ['status' => 1, 'message' => 'Success'];
    }


    public function finalRequestBackProcessing($machineRequest, Request $request)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return ['status' => 0, 'message' => 'Tài khoản không có quyền hạn này'];
        }

        $machineRequest = $this->machineRequestBackRepository->find($machineRequest);
        if(empty($machineRequest) || $machineRequest->status != $machineRequest::REQUEST_BACK_SUCCESS)
            return ['status' => 0, 'message' => 'Yêu cầu không hợp lệ'];

        $this->machineRequestBackRepository->finalRequestBackProcessing($machineRequest);

        $attribute['content_request'] = [
            'ID' => $machineRequest->id,
            'Content Request' => $machineRequest->request_content
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        if(!empty($machineRequest->merchantInfo->email)) {
            $dataMail = [
                'view' => 'admin::email.machine-request-back-approve',
                'to' => $machineRequest->merchantInfo->email,
                'data' => ['machineRequest' => $machineRequest],
                'subject' => '[1giay.vn] Thông báo duyệt yêu cầu trả máy bán hàng!'
            ];
            sendMailCustom($dataMail);
        }

        return ['status' => 1, 'message' => 'Success'];
    }

    public function createAttributes()
    {
        $attributes = $this->machineAttributeRepository->all();
        return view('admin::machine.create-attribute', compact('attributes'));
    }

    public function createAttributesPost(Request $request)
    {
        $attributesName = $request->key;
        $attributesValue = $request->value;
        foreach ($attributesName as $key => $name) {
            if (empty(trim($name)) || empty(trim($attributesValue[$key])))
                continue;
            $this->machineAttributeRepository->createAttribute($name, $attributesValue[$key]);
        }

        $attribute['content_request'] = array_combine($attributesName, $attributesValue);
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.machine.createAttributes')
            ->with('message', __('Update Attribute thành công'));
    }

    public function getAttributes()
    {
        $listAttribute = $this->machineAttributeRepository->all();
        $listAttribute->toArray();
        return $listAttribute;
    }

    public function cancelRequestBack($machineRequest, Request $request)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return ['status' => 0, 'message' => 'Tài khoản không có quyền hạn này'];
        }

        $machineRequest = $this->machineRequestBackRepository->find($machineRequest);
        if(empty($machineRequest) || $machineRequest->status == $machineRequest::REQUEST_SUCCESS)
            return ['status' => 0, 'message' => 'Yêu cầu không hợp lệ'];

        $this->machineRequestBackRepository->cancelRequestBack($machineRequest);

        $attribute['content_request'] = [
            'ID' => $machineRequest->id
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        if(!empty($machineRequest->merchantInfo->email)) {

            $dataMail = [
                'view' => 'admin::email.machine-request-back-cancel',
                'to' => $machineRequest->merchantInfo->email,
                'data' => ['machineRequest' => $machineRequest],
                'subject' => '[1giay.vn] Thông báo hủy yêu cầu trả máy bán hàng!'
            ];
            sendMailCustom($dataMail);
        }

        return ['status' => 1, 'message' => 'Hủy yêu cầu thành công'];
    }


    public function changeDevice($machineId, Request $request)
    {
        if (!AdminCan::do('adm.machine_request_back.edit')) {
            return ['status' => false, 'message' => 'Tài khoản không có quyền hạn này'];
        }

        $machine = $this->machineRepository->find($machineId);

        if(empty($machine))
            return ['status' => false, 'message' => 'Yêu cầu không hợp lệ'];

        if(empty($request->device_id))
            return ['status' => false, 'message' => 'Device ID không được để trống'];

        if($request->device_id === $machine->device_id)
            return ['status' => false, 'message' => 'Device ID mới phải khác Device ID hiện tại!'];

        $checkExitsDeviceId = $this->machineRepository->checkExitsDeviceId($request->device_id, $machine->id);

        if($checkExitsDeviceId)
            return ['status' => false, 'message' => 'Device ID đã được sử dụng. Vui lòng kiểm tra lại!'];

        $response = $this->machineRepository->changeDeviceID($machine, $request);

        return ['status' => $response['status'], 'message' => $response['message']];
    }
}
