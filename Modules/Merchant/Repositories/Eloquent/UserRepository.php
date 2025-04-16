<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\UserCoinRequest;
use App\User;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\UserRepositoryInterface;
use Yajra\DataTables\DataTables;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $userCoinRequest;

    private $secret = 'A6(2$xSdvR&;4u7j';

    public function __construct(User $model, UserCoinRequest $userCoinRequest)
    {
        parent::__construct($model);
        $this->userCoinRequest = $userCoinRequest;
    }

    public function list($request)
    {
        $merchant = auth(MERCHANT)->user();
        $datatable = Datatables::of($this->model::where('is_deleted','<>',$this->model::DELETED)
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('status', $this->model::USER_ACTIVE)
            ->where('is_credit_account', $this->model::IS_NOT_CREDIT)->get())
            ->addColumn('email', function($row){
                return $row->email ;
            })
            ->addColumn('department', function($row){
                return $row->department ?? '---' ;
            })
            ->addColumn('coin', function($row){
                return sortSearchCoin($row->coin);
            })
            ->addColumn('phone_number', function($row){
                return $row->phone_number ?? '---' ;
            })
            ->addColumn('date', function($row){
                return sortSearchDate($row->created_at);
            });
        if (MerchantCan::do('user.edit')){
            $datatable->addColumn('approve', function($row){
                return '<div class="text-center"><a href="'.route('merchant.user.approve',['approve'=>$row->id]).'"><span class="btn btn-primary btn-sm">Duyệt tín dụng</span></a></div>';
            })->addColumn('action', function($row){
                return '<div class="text-center">
                            <span class="btn btn-danger btn-sm user-delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i> Xóa</span>
                        </div>';
            });
        }
        return $datatable->rawColumns(['email', 'coin', 'approve', 'action', 'date'])
            ->make();
    }

    public function credit($request)
    {
        $merchant = auth(MERCHANT)->user();
        $datatable =  DataTables::of($this->model::where('is_deleted','<>',$this->model::DELETED)
            ->where('is_credit_account',$this->model::IS_CREDIT)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('merchant_id', $merchant->getMerchantID())->get())
            ->addColumn('email', function($row){
                return $row->email;
            })
            ->addColumn('date', function($row){
                return sortSearchDate($row->created_at);
            })
            ->addColumn('coin', function($row){
                return sortSearchCoin($row->coin);
            })
            ->addColumn('quota', function($row){
                return sortSearchCoin($row->credit_quota);
            })
            ->addColumn('phone_number', function($row){
                return $row->phone_number ?? '---';
            });
        if (MerchantCan::do('user.credit.edit')) {
            $datatable->addColumn('approve', function ($row) {
                return '<div class="text-center"><a href="' . route('merchant.user.approve', ['approve' => $row->id]) . '"><span class="btn btn-primary btn-sm">Duyệt hạn mức mới</span></a></div>';
            })->addColumn('action', function ($row) {
                return '<div class="text-center">
                            <span class="btn btn-danger btn-sm user-credit-delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Xóa tín dụng</span>
                        </div>';
            });
        }
        return $datatable->rawColumns(['quota','coin','approve','action', 'date'])
            ->make(true);
    }

    public function recharge($request)
    {
        $merchant = auth(MERCHANT)->user();
        $datatables = Datatables::of($this->userCoinRequest::where('is_deleted', '<>', $this->userCoinRequest::DELETED)
            ->whereHas('user', function ($q) use($merchant){
                $q->where('merchant_id', $merchant->getMerchantID());
                $q->where('status', $this->model::USER_ACTIVE);
                $q->where('is_deleted', '<>', $this->model::DELETED);
            })
            ->with(['user'])->get())
            ->addColumn('transaction', function($row){
                return $row->transaction_id ?? '---';
            })
            ->addColumn('email', function($row){
                return $row->user ? $row->user->email : '---';
            })
            ->addColumn('date', function($row){
                return sortSearchDate($row->created_at);
            })
            ->addColumn('status', function($row){
                $content = '';
                switch ($row->status) {
                    case 0:
                        $content = '<span class="badge-primary badge">Mới</span>';
                        break;
                    case 1:
                        $content = '<span class="badge-success badge">Thành công</span>';
                        break;
                }
                return $content;
            })
            ->addColumn('coin', function($row){
                return sortSearchCoin($row->coin);
            })
            ->addColumn('money', function($row){
                return sortSearchPrice($row->coin * env('COIN_MONEY_RATIO', 1));
            });
        if (MerchantCan::do('user.coin.request.edit')) {
            $datatables->addColumn('optional_approve', function ($row) {
                $content = '';
                switch ($row->status) {
                    case 0:
                        $content = '<div class="text-center"><a href="' . route('merchant.user.approveOption',
                                ['approve' => $row->id]) . '" data-id="' . $row->id . '"><button class="btn btn-primary btn-sm mb-2">Duyệt tùy chỉnh</button></a></div>';
                        break;
                    case 1:
                        $content = '<span class="badge-secondary badge">Đã duyệt</span>';
                        break;
                }
                return $content;

            })->addColumn('quick_approve', function ($row) {
                $content = '';
                switch ($row->status) {
                    case 0:
                        $content = '<div class="text-center"><button class="btn btn-outline-primary btn-sm mb-2 request-quick-approve-btn" data-id="' . $row->id . '">Duyệt nhanh</button></div>';
                        break;
                    case 1:
                        $content = '<span class="badge-secondary badge">Đã duyệt</span>';
                        break;
                }
                return $content;
            })->addColumn('action', function ($row) {
                return '<div class="text-center"><button class="btn btn-danger btn-sm mb-2 request-delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Xóa yêu cầu</button></div>';
            });
        }

        return $datatables->rawColumns(['optional_approve','quick_approve','action', 'date' ,'money' ,'coin', 'status'])
            ->make();
    }

    public function rechargeSearch($request)
    {
        $email = $request->s;
        return $this->userCoinRequest::query()
            ->where('is_deleted','<>', $this->userCoinRequest::DELETED)
            ->where('status','<>', $this->userCoinRequest::APPROVED)
            ->whereHas('user',function ($q) use ($email) {
                $q->where('email', 'LIKE', '%' . $email . '%');
                $q->where('status', $this->model::USER_ACTIVE);
            })
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function approveCredit($approve, $userRequest)
    {
        try {
            $approve->update([
                'credit_quota'      => $userRequest->credit_quota,
                'coin'              => $approve->coin + ($userRequest->credit_quota - $approve->credit_quota),
                'is_credit_account' => $this->model::IS_CREDIT,
                'credit_updated_at' => now(),
                'credit_updated_by' => auth(MERCHANT)->id(),
            ]);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Duyệt tín dụng thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserRepository][approveCredit]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Duyệt tín dụng không thành công'));
        }
        return $this->response;
    }

    private function hashPassword($password, $salt)
    {
        return md5($this->secret . $salt . $password);
    }

    protected function generateSalt($length = 16)
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    private function genereateUUID()
    {
        return Uuid::uuid();
    }

    public function store($request)
    {
        try {
            $account = auth(MERCHANT)->user();
            $password = $request->pass_make == 1 ? $request->password : Str::random(9);
            $salt = $this->model->generateSalt();
            $passwordHash = $this->model->hashPassword($password, $salt);
            $account = $this->model::create([
                'email'       => $request->email,
                'password'    => $passwordHash,
                'salt'        => $salt,
                'uid'         => $this->model->genereateUUID(),
                'status'      => 1,
                'merchant_id' => $account->getMerchantID(),
            ]);
            $data = [
                'password' => $password,
                'account' => $account
            ];
            $this->setData($data);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo tài khoản thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserRepository][store]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo tài khoản không thành công!'));
        }
        return $this->response;
    }

    public function destroy($user)
    {
        try {
            $user->is_deleted = $this->model::DELETED;
            $user->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa User thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserRepository][destroy]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn'));
        }
        return $this->response;
    }

    public function destroyCredit($user)
    {
        try {
            $user->coin = $user->coin - $user->credit_quota;
            $user->credit_quota = $this->model::EMPTY_CREDIT;
            $user->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Tín dụng thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserRepository][destroyCredit]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn'));
        }
        return $this->response;
    }

    public function userSearch($email)
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->where('is_deleted','<>', $this->model::DELETED)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('email', 'LIKE', '%' . $email . '%')
            ->with('merchantUpdateBy')
            ->first();
    }

    public function rechargeStore($request){
        try {
            $model = $this->userCoinRequest->create([
                'user_id'        => $request->user_id,
                'coin'           => $request->user_coin,
                'message'        => $request->user_message,
                'transaction_id' => uniqid(),
            ]);
            $this->setData($model);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo Yêu cầu nạp coin thành công!'));
        } catch (\Exception $e) {
            Log::error('[UserRepository][rechargeStore]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo Yêu cầu nạp coin không thành công!'));
        }
        return $this->response;
    }

    // EXTRA

    public function getDebtUsers(){
        return $this->model::query()
            ->where('is_deleted','<>',$this->model::DELETED)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('status', $this->model::IS_CREDIT)
            ->whereRaw('credit_quota - coin > 0');
    }

    public function findUserToApprove($id, $merchant_id)
    {
        return $this->model::where('id', $id)
            ->where('merchant_id', $merchant_id)
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->with(['merchantUpdateBy'])
            ->where('status', $this->model::USER_ACTIVE)
            ->first();
    }

    public function findUserByID($user_id)
    {
        return $this->model::query()
            ->where('id', $user_id)
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('status', $this->model::USER_ACTIVE)
            ->first();
    }

    public function findAll()
    {
        return $this->model::query()
            ->where('is_deleted', '<>', $this->model::DELETED);
    }

    public function getTotalNewUsers()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted','<>',$this->model::DELETED)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('is_credit_account', $this->model::IS_NOT_CREDIT)
            ->whereDate('created_at','>=', Carbon::now()->subDay(6));
    }

    public function getUserByMerchantId($merchantId)
    {
        $result = $this->model::query()
            ->where('merchant_id', $merchantId)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('is_deleted','<>',$this->model::DELETED)
            ->get()
            ->all();
        return $result;
    }

    public function getAllUserOfMerchant()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('status', $this->model::USER_ACTIVE)
            ->where('merchant_id', $merchant->getMerchantID());
    }
}
