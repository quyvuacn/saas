<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Admin\Http\Requests\SubscriptionStoreRequest;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionRequestRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionHistoryRepositoryInterface;
use Modules\Admin\Http\Requests\SubscriptionRequest as SubscriptionRequestValidation;
use Modules\Admin\Http\Requests\SubscriptionRequestRequest;

class SubscriptionController extends Controller
{
    protected $subscriptionRepository;
    protected $subscriptionRequestRepository;
    protected $merchantRepository;
    protected $machineRepository;
    protected $subscriptionHistoryRepository;
    protected $logActionAdminRepository;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionRequestRepositoryInterface $subscriptionRequestRepository,
        MerchantRepositoryInterface $merchantRepository,
        MachineRepositoryInterface $machineRepository,
        SubscriptionHistoryRepositoryInterface $subscriptionHistoryRepository,
        LogActionAdminRepositoryInterface $logActionAdminRepository
    )
    {
        $this->middleware('auth:admin');
        $this->middleware('change.password.account');

        $this->logActionAdminRepository = $logActionAdminRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionRequestRepository = $subscriptionRequestRepository;
        $this->merchantRepository = $merchantRepository;
        $this->machineRepository = $machineRepository;
        $this->subscriptionHistoryRepository = $subscriptionHistoryRepository;
    }

    public function list(Request $request)
    {
        if (!AdminCan::do('adm.subscription.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        if ($request->ajax()) {
            return $this->subscriptionRepository->list($request);
        }
        return view('admin::subscription.list');
    }

    public function extend(Request $request)
    {
        if (!AdminCan::do('adm.subscription_request.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        if ($request->ajax()) {
            return $this->subscriptionRequestRepository->list($request);
        }
        list($statusRequestNew, $statusRequestSuccess, $statusRequestCancel) = $this->subscriptionRequestRepository->getStatusRequest();
        return view('admin::subscription.extend', compact('statusRequestNew', 'statusRequestCancel', 'statusRequestSuccess'));
    }

    public function create()
    {
        if (!AdminCan::do('adm.subscription_request.edit')) {
            return redirect()->route('admin.subscription.extend')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $merchants = $this->merchantRepository->findMerchantParent()->select('id', 'name')->get()->toArray();
        $machines = $this->machineRepository->findMachineActive()->select('id', 'name', 'merchant_id')->get()->toArray();
        return view('admin::subscription.create', compact('merchants', 'machines'));
    }

    public function store(SubscriptionStoreRequest $request)
    {
        if (!AdminCan::do('adm.subscription_request.edit')) {
            return redirect()->route('admin.subscription.extend')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $response = $this->subscriptionRequestRepository->createSubscriptionRequest($request);

        if($response['status']) {
            $attribute['content_request'] = [
                'ID' => $response['data']->id,
                'Merchant ID' => $response['data']->merchant_id,
                'Machine ID' => $response['data']->machine_id
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);
        }

        return redirect()->route('admin.subscription.extend')
            ->with($response['alert'], __($response['message']));
    }

    public function edit(Subscription $subscription)
    {
        if (!AdminCan::do('adm.subscription.edit')) {
            return redirect()->route('admin.subscription.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $subscription->with(['merchantSubscription', 'machineSubscription']);
        return view('admin::subscription.edit', compact('subscription'));
    }

    public function update($subscription, SubscriptionRequestValidation $request)
    {
        if (!AdminCan::do('adm.subscription.edit')) {
            return redirect()->route('admin.subscription.list')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $subscription = $this->subscriptionRepository->find($subscription);
        if (empty($subscription)) {
            return redirect()->route('admin.subscription.list');
        }
        $updateSubscription = $this->subscriptionRepository->updateSubscription($subscription, $request);

        $attribute['content_request'] = [
            'ID' => $subscription->id,
            'Date Expire' => $request->date_expire
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.subscription.list')
            ->with($updateSubscription['alert'], __($updateSubscription['message']));
    }

    public function approve($subscriptionRequest)
    {
        if (!AdminCan::do('adm.subscription_request.edit')) {
            return redirect()->route('admin.subscription.extend')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $subscriptionRequest = $this->subscriptionRequestRepository->findSubscriptionApprove($subscriptionRequest);

        if (empty($subscriptionRequest)) {
            return redirect()->route('admin.subscription.extend')
                ->with('message', __('Thuê bao không hợp lệ'));
        }
        if (!in_array($subscriptionRequest->status, [
            $subscriptionRequest::REQUEST_NEW,
            $subscriptionRequest::REQUEST_WAITING_CONTRACT,
        ])) {
            return redirect()->route('admin.subscription.extend')
                ->with('message', __('Thuê bao không hợp lệ'));
        }

        $arrInfoStatus = [
            SubscriptionRequest::REQUEST_NEW => [
                'Chờ ký hợp đồng',
                'Giao dịch thất bại hoặc quá thời hạn thực hiện, hủy yêu cầu.'
            ],
            SubscriptionRequest::REQUEST_WAITING_CONTRACT => [
                'Ký hợp đồng thành công, chờ thanh toán',
                'Giao dịch thất bại hoặc quá thời hạn thực hiện, hủy yêu cầu.'
            ],
        ];

        $dateExpireOption = $subscriptionRequest->date_expire_option;

        $subscriptionRequest->with('merchant');
        $subscripton = $this->subscriptionRepository->findBySubscriptionRequest($subscriptionRequest->merchant_id, $subscriptionRequest->machine_id);

        if(empty($dateExpireOption)){
            $dateExpireOption = !empty($subscripton) ? date('Y-m-d', strtotime($subscripton->date_expiration . ' +' . $subscriptionRequest->request_month . ' month')) : date('Y-m-d', strtotime('+' . $subscriptionRequest->request_month . ' month'));
        }

        $paymentMethod = config('admin.payment_method');
        return view('admin::subscription.approve', compact('subscriptionRequest', 'subscripton', 'arrInfoStatus', 'paymentMethod', 'dateExpireOption'));
    }

    public function approveRequest(SubscriptionRequestRequest $subscriptionRequestRequest, $subscriptionRequest)
    {
        if (!AdminCan::do('adm.subscription_request.edit')) {
            return redirect()->route('admin.subscription.extend')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $subscriptionRequest = $this->subscriptionRequestRepository->findSubscriptionApprove($subscriptionRequest);
        if (empty($subscriptionRequest)) {
            return redirect()->route('admin.subscription.extend')
                ->with('message', __('Thuê bao không hợp lệ'));
        }

        if ($subscriptionRequestRequest->merchant_audit == 1) {
            $this->subscriptionRequestRepository->updateStatusRequestCancel($subscriptionRequest, $subscriptionRequestRequest);

            $dataMail = [
                'view' => 'admin::email.subscription-request-cancel',
                'to' => $subscriptionRequest->merchant->email,
                'data' => ['subscriptionRequest' => $subscriptionRequest],
                'subject' => '[1giay.vn] Thông báo yêu cầu gia hạn máy bán hàng thất bại'
            ];
            sendMailCustom($dataMail);
        }

        if ($subscriptionRequestRequest->merchant_audit == 0) {
            $this->subscriptionRequestRepository->approveSubscriptionRequest($subscriptionRequest, $subscriptionRequestRequest);
        }

        $attribute['content_request'] = [
            'ID' => $subscriptionRequest->id,
        ];
        $this->logActionAdminRepository->createAction($subscriptionRequestRequest, $attribute);

        return redirect()->route('admin.subscription.extend')
            ->with('message', __('Update trạng thái thuê bao thành công'));
    }

    public function finalApproveRequest(Request $request, $subscriptionRequest)
    {
        $status = 0;
        if (!AdminCan::do('adm.subscription_request.edit')) {
            return ['status' => $status, 'msg' => 'Tài khoản không có quyền hạn này!'];
        }

        $subscriptionRequest = $this->subscriptionRequestRepository->findSubscriptionFinalApprove($subscriptionRequest);

        if (empty($subscriptionRequest)) {
            return ['status' => $status, 'msg' => 'Yêu cầu gia hạn thuê bao không hợp lệ'];
        }
        if(!in_array($request->status, $this->subscriptionRequestRepository->getStatusRequest())){
            return ['status' => $status, 'msg' => 'Trạng thái không hợp lệ'];
        }

        $subscription = $this->subscriptionRepository->findBySubscriptionRequest($subscriptionRequest->merchant_id, $subscriptionRequest->machine_id);

        $this->subscriptionRequestRepository->finalUpdateStatusRequest($subscriptionRequest, $request->status);

        if($request->status == $this->subscriptionRequestRepository->getStatusRequestSuccess()) {
            $subscriptionRequest->with('machine');
            $arrAttribute = [
                'id' => $subscriptionRequest->id,
                'merchant_id' => $subscriptionRequest->merchant_id,
                'machine_id' => $subscriptionRequest->machine_id,
                'date_expire_option' => $subscriptionRequest->date_expire_option,
                'request_month' => $subscriptionRequest->request_month,
                'machine_address' => $subscriptionRequest->machine->machine_address ?? '',
            ];
            if (empty($subscription)) {
                $this->subscriptionRepository->createSubscription($arrAttribute);
            } else {
                $this->subscriptionRepository->updateSubscriptionBySubscriptionRequest($subscription, $arrAttribute);
            }

            $dataMail = [
                'view' => 'admin::email.subscription-request-approve',
                'to' => $subscriptionRequest->merchant->email,
                'data' => ['subscriptionRequest' => $subscriptionRequest],
                'subject' => '[1giay.vn] Thông báo yêu cầu gia hạn máy bán hàng thành công!'
            ];
            sendMailCustom($dataMail);
        }

        $status = 1;

        $attribute['content_request'] = [
            'ID' => $subscriptionRequest->id,
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

//        if($request->status == $subscriptionRequest::REQUEST_CANCEL && !empty($subscriptionRequest->merchant->email)) {
//
//        }

        return ['status' => $status, 'msg' => 'Update trạng thái yêu cầu thành công'];
    }

    public function history(Request $request, $merchantId)
    {
        if (!AdminCan::do('adm.subscription.list')) {
            return redirect()->route('admin.dashboard')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        if ($request->ajax()) {
            return $this->subscriptionHistoryRepository->list($request, $merchantId);
        }
        return view('admin::subscription.history', compact('merchantId'));
    }

    public function viewRequest(Request $request, $subscriptionRequest)
    {
        if (!AdminCan::do('adm.subscription_request.list')) {
            return redirect()->route('admin.subscription.extend')->with('error', 'Tài khoản không có quyền hạn này!');
        }

        $subscriptionRequest = $this->subscriptionRequestRepository->find($subscriptionRequest);

        if (empty($subscriptionRequest)) {
            return redirect()->route('admin.subscription.extend')
                ->with('message', __('Thuê bao không hợp lệ'));
        }

        $dateExpireOption = $subscriptionRequest->date_expire_option;

        $subscriptionRequest->with('merchant');
        $subscripton = $this->subscriptionRepository->findBySubscriptionRequest($subscriptionRequest->merchant_id, $subscriptionRequest->machine_id);

        if(empty($dateExpireOption)){
            $dateExpireOption = !empty($subscripton) ? date('Y-m-d', strtotime($subscripton->date_expiration . ' +' . $subscriptionRequest->request_month . ' month')) : date('Y-m-d', strtotime('+' . $subscriptionRequest->request_month . ' month'));
        }

        $paymentMethod = config('admin.payment_method');
        return view('admin::subscription.view-request', compact('subscriptionRequest', 'subscripton', 'paymentMethod', 'dateExpireOption'));
    }
}
