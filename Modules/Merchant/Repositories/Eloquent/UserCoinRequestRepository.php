<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\UserCoinRequest;
use App\Models\UserHistoryPayment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\UserCoinRequestRepositoryInterface;

class UserCoinRequestRepository extends BaseRepository implements UserCoinRequestRepositoryInterface
{
    protected $userHistoryPayment;

    public function __construct(UserCoinRequest $model, UserHistoryPayment $userHistoryPayment)
    {
        parent::__construct($model);
        $this->userHistoryPayment = $userHistoryPayment;
    }

    public function findCoinRequestByID($id)
    {
        return $this->model::query()->where('id', $id)->where('is_deleted', '<>', $this->model::DELETED)->with(['user', 'user.merchantUpdateBy'])->first();
    }

    public function quickApprove($coin)
    {
        try {
            $coin->update([
                'status' => $this->model::APPROVED,
            ]);

            if ($coin->user) {
                $coin->user->update([
                    'coin'              => $coin->coin + $coin->user->coin,
                    'credit_updated_at' => now(),
                    'credit_updated_by' => auth(MERCHANT)->id(),
                ]);
            }

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Nạp coin thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserCoinRequestRepository][quickApprove]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Nạp coin không thành công'));
        }
        return $this->response;
    }

    public function approveOptionStore($coin, $request)
    {
        try {
            $coin->update([
                'status' => $this->model::APPROVED,
                'coin'   => $request->user_coin,
            ]);
            if ($coin->user) {
                $coin->user->update([
                    'coin'              => $request->user_coin + $coin->user->coin,
                    'credit_updated_at' => now(),
                    'credit_updated_by' => auth(MERCHANT)->id(),
                ]);
                $this->userHistoryPayment::create([
                    'user_id'                => $coin->user_id,
                    'uid'                    => $coin->user->uid,
                    'purchase_type'          => UserHistoryPayment::PURCHASE_TYPE_RECHARGE,
                    'status'                 => UserHistoryPayment::STATUS_SUCCESS,
                    'transaction_type'       => UserHistoryPayment::TRANSACTION_TYPE_BUY,
                    'transaction_coin'       => $request->user_coin,
                    'transaction_device'     => 'PC',
                    'transaction_id'         => $coin->transaction_id,
                    'transaction_ip_address' => $request->ip(),
                    'checksum'               => md5($coin->user_id . $coin->user->email . $request->user_coin),
                ]);

                $notify = [
                    'title'          => 'Thông báo nạp coin thành công!',
                    'message'        => 'Hệ thống đã nạp thành công số coin ' . number_format($coin->user->coin) . '. Số dư khả dụng hiện tại là ' . number_format($coin->user->coin) . ' coin. Cảm ơn bạn đã sử dụng hệ thống 1Giay.vn',
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
            }

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Duyệt yêu cầu thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserCoinRequestRepository][approveOptionStore]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Duyệt yêu cầu không thành công'));
        }
        return $this->response;
    }

    public function destroyCoinRequest($coin)
    {
        try {
            $coin->update([
                'is_deleted' => UserCoinRequest::DELETED,
                'status'     => UserCoinRequest::NOT_APPROVED,
            ]);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa yêu cầu thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserCoinRequestRepository][destroyCoinRequest]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Xóa yêu cầu không thành công!'));
        }
        return $this->response;
    }

    public function getUnApproveCoinRequests()
    {
        return $this->model::query()->where('is_deleted', '<>', $this->model::DELETED)->where('status', '<>', $this->model::APPROVED)->where('created_at', '<=',
                Carbon::now()->subDay())->orderByDesc('created_at');
    }

    public function findAllCoinRequests()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['user', 'user.merchant', 'user.staff'])
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->whereHas('user.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })->whereHas('user', function ($q) use ($merchant) {
                $q->where('is_deleted', '<>', User::DELETED);
            })->get();
    }

    public function clearUnApproveCoinRequests($coinRequests)
    {
        try {
            $this->model::query()->whereIn('id', $coinRequests)->update([
                'is_deleted' => UserCoinRequest::DELETED,
                'status'     => UserCoinRequest::NOT_APPROVED,
            ]);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Yêu cầu nạp tiền thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserCoinRequestRepository][clearUnApproveCoinRequests]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Xóa Yêu cầu nạp tiền không thành công!'));
        }
        return $this->response;
    }
}
