<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\UserCoinRequest;
use App\Models\UserDebt;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\UserDebtRepositoryInterface;
use Yajra\DataTables\DataTables;

class UserDebtRepository extends BaseRepository implements UserDebtRepositoryInterface
{
    protected $userCoinRequest;

    public function __construct(UserDebt $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $userDebts = $this->findUserDebts();
        $datatable = Datatables::of($userDebts)->addColumn('email', function ($row) {
            return $row->user->email ?? '---';
        })->addColumn('code', function ($row) {
            return $row->user->staff->employee_code ?? '---';
        })->addColumn('department', function ($row) {
            return $row->user->staff->employee_department ?? '---';
        })->addColumn('quote', function ($row) {
            if ($row->user->credit_quota) {
                return sortSearchCoin($row->user->credit_quota);
            }
        })->addColumn('debt', function ($row) {
            if ($row->debt) {
                return sortSearchCoin($row->debt);
            }
        });
        if (MerchantCan::do('user.debt.edit')) {
            $datatable->addColumn('action', function ($row) {
                return '<div class="text-center">
                            <span class="btn btn-primary btn-sm user-debt-receive-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Xác nhận đã thu hồi nợ</span>
                        </div>';
            });
        }
        return $datatable->rawColumns(['email', 'code', 'quote', 'action', 'debt'])->make();
    }

    public function receivedDebt($debUser)
    {
        try {
            // Save debt
            $deposit_coin = $debUser->debt;
            // Clear Debt
            $debUser->update([
                'debt'       => $this->model::DEBT_CLEAR,
                'status'     => $this->model::DEBT_DONE,
                'is_locked'  => $this->model::IS_LOCKED,
                'updated_at' => now(),
            ]);
            // Increase User coin
            $debUser->user->update([
                'coin'              => $debUser->user->coin + $deposit_coin,
                'credit_updated_at' => now(),
                'credit_updated_by' => auth(MERCHANT)->id(),
            ]);

            $notify = [
                'title'   => 'Thông báo thu hồi công nợ thành công!',
                'message' => 'Chúng tôi đã tiến hành thu hồi công nợ của bạn, số tiền thu nợ là ' . number_format($deposit_coin) . ' Việt Nam đồng. số dư khả dụng hiện tại là ' . number_format($debUser->user->coin) . ' coin. Cảm ơn bạn đã sử dụng hệ thống 1Giay.vn',
                'token'   => $debUser->user->firebase_token,
            ];

            if ($debUser->user->firebase_token) {
                callApiNotifyFirebase($notify['title'], $notify['message'], $notify['token']);
            }

            $notify_data = [
                'title'          => $notify['title'],
                'brief'          => $notify['message'],
                'content'        => $notify['message'],
                'uid'            => $debUser->user->uid,
                'merchant_id'    => $debUser->user->merchant_id,
                'status'         => Notification::STATUS_NEW,
                'published_date' => Carbon::now(),
            ];

            Notification::create($notify_data);

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xác nhận thu hồi nợ thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserDebtRepository][receivedDebt]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Xác nhận thu hồi nợ không thành công!'));
        }
        return $this->response;
    }

    public function decreaseUserDebt($debUser, $coin)
    {
        try {
            Log::info($debUser->is_locked);
            if ($debUser->is_locked == $this->model::IS_UNLOCKED) {
                // Decrease Debt
                $debUser->update([
                    'debt'       => $coin >= $debUser->debt ? $this->model::DEBT_CLEAR : $debUser->debt - $coin,
                    'status'     => $coin >= $debUser->debt ? $this->model::DEBT_DONE : $this->model::DEBT_NEW,
                    'is_locked'  => $coin >= $debUser->debt ? $this->model::IS_LOCKED : $this->model::IS_UNLOCKED,
                    'updated_at' => now(),
                ]);
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Nạp tiền xóa nợ thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserDebtRepository][decreaseUserDebt]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Nạp tiền xóa nợ không thành công!'));
        }
        return $this->response;
    }

    public function createDebtReport($user)
    {
        try {
            $hasDebt = $this->model->query()
                ->where('user_id', $user->id)
                ->where('status', $this->model::DEBT_NEW)
                ->where('debt', '>', $this->model::DEBT_CLEAR)
                ->where('is_deleted', $this->model::IS_NOT_DELETED)
                ->first();
            if (is_null($hasDebt)) {
                $this->model::create([
                    'user_id'    => $user->id,
                    'debt'       => $user->credit_quota - $user->coin,
                    'status'     => $this->model::DEBT_NEW,
                    'is_deleted' => $this->model::IS_NOT_DELETED,
                    'is_locked'  => $this->model::IS_UNLOCKED,
                ]);
            } else {
                $hasDebt->update([
                    'debt'       => $user->credit_quota - $user->coin,
                ]);
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo User debt report thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserDebtRepository][createDebtReport]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo User debt report không thành công!'));
        }
        return $this->response;
    }

    public function debtCollectionActivation()
    {
        try {
            $debtIds = $this->findUserDebts()->pluck('id');
            if ($debtIds->count()) {
                $this->model::query()->whereIn('id', $debtIds->toArray())->update(['is_locked' => $this->model::IS_LOCKED]);
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Kích hoạt chế độ thu hồi nợ thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserDebtRepository][debtCollectionActivation]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Kích hoạt chế độ thu hồi nợ thất bại!'));
        }
        return $this->response;
    }

    public function debtCollectionDisable()
    {
        try {
            $debtIds = $this->findUserDebts()->pluck('id');
            if ($debtIds->count()) {
                $this->model::query()->whereIn('id', $debtIds->toArray())->update(['is_locked' => $this->model::IS_UNLOCKED]);
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Hủy chế độ thu hồi nợ thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserDebtRepository][debtCollectionDisable]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Hủy chế độ thu hồi nợ thất bại!'));
        }
        return $this->response;
    }

    public function findDebtUserByID($debUser)
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['user', 'user.merchant', 'user.staff'])->where('is_deleted', '<>', $this->model::IS_DELETED)->where('debt', '>', $this->model::DEBT_CLEAR)->where('status',
                $this->model::DEBT_NEW)->where('id', $debUser)->whereHas('user.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })->whereHas('user', function ($q) use ($merchant) {
                $q->where('is_deleted', '<>', User::DELETED);
            })->first();
    }

    public function findDebByUserID($userId)
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['user', 'user.merchant', 'user.staff'])->where('is_deleted', '<>', $this->model::IS_DELETED)->where('debt', '>', $this->model::DEBT_CLEAR)->where('status',
                $this->model::DEBT_NEW)->where('user_id', $userId)->whereHas('user.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })->whereHas('user', function ($q) use ($merchant) {
                $q->where('is_deleted', '<>', User::DELETED);
            })->first();
    }

    public function findUserDebts()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['user', 'user.merchant', 'user.staff'])
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('debt', '>', $this->model::DEBT_CLEAR)
            ->where('status',
                $this->model::DEBT_NEW)->whereHas('user.merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })->whereHas('user', function ($q) use ($merchant) {
                $q->where('is_deleted', '<>', User::DELETED);
            })->get();
    }

    public function isUserDebtLocked()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['user', 'user.merchant', 'user.staff'])
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('debt', '>', $this->model::DEBT_CLEAR)->where('status', $this->model::DEBT_NEW)
            ->where('is_locked', $this->model::IS_LOCKED)
            ->whereHas('user.merchant', function ($q) use ($merchant) {
            $q->where('id', $merchant->getMerchantID());
        })->whereHas('user', function ($q) use ($merchant) {
            $q->where('is_deleted', '<>', User::DELETED);
        })->first();
    }
}
