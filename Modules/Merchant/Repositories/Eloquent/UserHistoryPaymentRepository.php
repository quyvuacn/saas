<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\UserHistoryPayment;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\UserHistoryPaymentRepositoryInterface;

class UserHistoryPaymentRepository extends BaseRepository implements UserHistoryPaymentRepositoryInterface
{
    public function __construct(UserHistoryPayment $model)
    {
        parent::__construct($model);
    }

    public function createHistoryPayment($coin, $request)
    {
        try {
            $this->create([
                'user_id'                => $coin->user_id,
                'uid'                    => $coin->user->uid,
                'purchase_type'          => $this->model::PURCHASE_TYPE_RECHARGE,
                'transaction_type'       => $this->model::TRANSACTION_TYPE_BUY,
                'transaction_coin'       => $coin->coin,
                'transaction_device'     => 'PC',
                'transaction_id'         => $coin->transaction_id,
                'transaction_ip_address' => $request->ip(),
                'status'                 => $this->model::STATUS_SUCCESS,
                'checksum'               => md5($coin->user_id . $coin->user->email . $coin->coin),
            ]);

            $notify = [
                'title'          => 'Thông báo nạp coin thành công!',
                'message'        => 'Hệ thống đã nạp thành công số coin là ' . number_format($coin->user->coin) . '. Số dư khả dụng hiện tại là ' . number_format($coin->user->coin) . ' coin. Cảm ơn bạn đã sử dụng hệ thống 1Giay.vn',
                'token'          => $coin->user->firebase_token,
                'transaction_id' => $coin->transaction_id,
            ];

            if ($coin->user->firebase_token) {
                callApiNotifyFirebase($notify['title'], $notify['message'], $notify['token']);
            }

            $notify_data = [
                'title'          => $notify['title'],
                'brief'          => $notify['message'],
                'content'        => $notify['message'],
                'transaction_id' => $coin->transaction_id,
                'uid'            => $coin->user->uid,
                'merchant_id'    => $coin->user->merchant_id,
                'status'         => Notification::STATUS_NEW,
                'published_date' => Carbon::now(),
            ];

            Notification::create($notify_data);

        } catch (\Exception $e) {
            Log::error('[UserHistoryPaymentRepository][createHistoryPayment]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Nạp coin không thành công'));
        }
        return $this->response;
    }

    public function latestSellingTransactions($limit = 10)
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->with(['machine', 'machine.merchant', 'buyUser'])
            ->whereHas('machine.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->whereHas('machine')
            ->take($limit)
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('status', '=', $this->model::STATUS_SUCCESS)
            // ->where('transaction_type', '=', $this->model::BUY_TRANSACTION)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');
    }

    public function getLatestWeekRevenue()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->with(['machine', 'machine.merchant'])
            ->whereHas('machine.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('status', '=', $this->model::STATUS_SUCCESS)
            // ->where('transaction_type', '=', $this->model::BUY_TRANSACTION)
            ->whereDate('created_at', '<=', Carbon::now()->format('Y-m-d').' 23:59:59')
            ->whereDate('created_at', '>=', Carbon::now()->subDay(6)->format('Y-m-d').' 00:00:00')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC');
    }

    public function getTotalTodayRevenue()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->with(['machine','machine.merchant'])
            ->whereHas('machine.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('status', '=', $this->model::STATUS_SUCCESS)
            // ->where('transaction_type', '=', $this->model::BUY_TRANSACTION)
            ->whereDate('created_at', '<=', Carbon::now()->format('Y-m-d').' 23:59:59')
            ->whereDate('created_at', '>=', Carbon::now()->format('Y-m-d').' 00:00:00')
            ->sum('transaction_coin');
    }

}
