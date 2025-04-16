<?php

namespace Modules\Merchant\Http\Controllers;

use App\Models\SubscriptionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Merchant\Http\Requests\SubscriptionExtendRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\SubscriptionRepositoryInterface;
use Modules\Merchant\Classes\Facades\MerchantCan;

class SubscriptionController extends Controller
{
    protected $subscriptionRepository;

    protected $logActionMerchantRepository;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('subscription.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->subscriptionRepository->list($request);
        }
        return view('merchant::subscription.list');
    }

    public function history(Request $request)
    {
        if (!MerchantCan::do('subscription.history.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->subscriptionRepository->history($request);
        }
        return view('merchant::subscription.history');
    }

    public function extend(SubscriptionExtendRequest $request)
    {
        $subscription = $this->subscriptionRepository->findSubscriptionByID($request->subscription_id);
        if (!$subscription || !MerchantCan::do('subscription.change', $subscription)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Thuê bao không tồn tại, hoặc Tài khoản không có quyền hạn')];
        } else {
            if ($subscription->machine && $subscription->machine->newSubscriptionRequest) {
                return ['status' => false, 'alert' => 'error', 'message' => 'Thuê bao đang chờ gian hạn!'];
            } else {
                $response = $this->subscriptionRepository->extend($subscription, $request);

                if($response['status']){

                    $attribute['content_request'] = [
                        'ID' => $response['data'],
                        'Request month' => $request->month
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);

                    $dataMail = [
                        'view' => 'merchant::email.subscription-request',
                        'to' => config('mail.list_mail.subscription_request'),
                        'data' => ['requestId' => $response['data'], 'merchant' => auth(MERCHANT)->user(), 'subscription' => $subscription, 'request' => $request],
                        'subject' => '[1giay.vn] Yêu cầu gia hạn thuê bao!'
                    ];
                    sendMailCustom($dataMail);
                }
                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }
}
