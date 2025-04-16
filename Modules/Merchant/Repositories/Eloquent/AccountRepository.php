<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Merchant;
use App\Models\MerchantInfo;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Merchant\Repositories\AccountRepositoryInterface;
use phpDocumentor\Reflection\Types\This;
use Yajra\DataTables\DataTables;use function MongoDB\BSON\toJSON;
use Illuminate\Support\Facades\DB;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    public function __construct(Merchant $model)
    {
        parent::__construct($model);
    }

    public function list()
    {
        $merchants = $this->findChildrenAccounts()->get();
        $datatables = Datatables::of($merchants)
            ->addColumn('account', function(Merchant $row){
                $content = '<h5>' . $row->name . '</h5><h6>' . $row->account . '</h6><div class="small">
                                    Email: <strong>' . $row->email . '</strong>
                                </div>';
                if ($row->getMerchantID() === auth(MERCHANT)->id()) {
                    $content .= '<small class="history">[<a href="' . route('merchant.account.historyMerchant', ['id' => $row->id]) . '">Xem lịch sử hoạt động</a>]</small>';
                }
                return $content;
            })
            ->addColumn('permission', function(Merchant $row){
                $roles = $row->permissions;
                $html = '';
                if (count($roles)){
                    $html .= '<ul class="small">';
                    foreach ($roles as $role) {
                        $html.= '<li>'.$role->permission_desc.'</li>';
                    }
                    $html .= '</ul>';
                }
                return $html;
            })
            ->addColumn('date', function(Merchant $row){
                return '<div>Ngày tạo:<br>
                                    <span data-sort="'.strtotime($row->created_at).'">'.$row->created_at->format('d/m/Y').'</span><span data-search="'.$row->created_at->format('d-m-Y').'"></span>
                                </div>
                                <div class="mt-2">
                                    Cập nhật lần cuối:<br><span data-sort="'.strtotime($row->updated_at).'">'.$row->updated_at->format('d/m/Y').'</span><span data-search="'.$row->updated_at->format('d-m-Y').'"></span>
                                </div>';
            })
            ->addColumn('action', function(Merchant $row){
                return '<div class="text-center">
                                <a href="'.route('merchant.account.edit', ['account'=>$row->id]).'"><span class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-user-edit"></i> Sửa thông tin</span></a>
                                <span class="btn btn-danger btn-sm mt-2 account-delete-btn" data-id="'.$row->id.'"><i class="fas fa-trash"></i> Xóa tài khoản</span>
                            </div>';
            })
            ->rawColumns(['account','permission','date','action'])
            ->make(true);
        return $datatables;
    }

    public function permission($request){
        $email_name   = $request->email_name;
        $merchant = auth(MERCHANT)->user();
        
        $roles = Role::query()->with([
            'permissions' => function ($q) {
                $q->where('is_deleted', '<>', Permission::IS_DELETED);
                $q->where('status', Permission::IS_ACTIVED);
            }])
            ->where('status', Role::IS_ACTIVED)
            ->where(function ($q) {
                $q->where('group', Role::GROUP_MERCHANT);
                $q->orWhere('group', Role::GROUP_ALL);
            })
            ->where('is_deleted','<>',Role::IS_DELETED)
            ->get()
            ->toArray();
            
        $roles = collect($roles)->keyBy('alias');

        $merchants = $this->model::query()->with([
            'permissions' => function ($q) {
                $q->where('is_deleted', '<>', Permission::IS_DELETED);
                $q->where('status', Permission::IS_ACTIVED);
            }])
            ->where('id','<>', $merchant->id)
            ->where('id','<>', $merchant->getMerchantID())
            ->where('parent_id', $merchant->getMerchantID())
            ->where('merchant_code', $merchant->merchant_code)
            ->where('is_deleted','<>', $this->model::DELETED)
            ->where('status','<>', $this->model::REQUEST_NEW);

        if ($email_name) {
            $merchants = $merchants->where(function ($q) use ($email_name) {
                $q->where('email', 'LIKE', '%' . $email_name . '%')
                  ->orWhere('name', 'LIKE', '%' . $email_name . '%');
            });
        }

        return Datatables::of($merchants)
            // My Account
            ->addColumn('account', function($row) use($roles){
                $myPermissions = collect($row->permissions->toArray());
                $dashboard = $roles->get('dashboard-manager');
                $input = '';
                
                if ($dashboard && isset($dashboard['permissions']) && is_array($dashboard['permissions'])) {
                    foreach ($dashboard['permissions'] as $item) {
                        if (!isset($item['permission_name']) || !isset($item['id'])) {
                            continue;
                        }
                        
                        $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                        $input .= '<div class="form-group small custom-control custom-checkbox mt-3">
                                    <input type="checkbox" class="custom-control-input" id="role_dashboard_'.$item['id'].$row->id.'" '.$check.' value="'.$item['permission_name'].'" name="permission" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'">
                                    <label class="custom-control-label permission-change-btn" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'" for="role_dashboard_'.$item['id'].$row->id.'">'.($item['permission_desc'] ?? $item['permission_name']).'</label>
                                </div>';
                    }
                }
                return '<a href="#">'.$row->email.'</a><p>'.$row->name.'</p>'.$input;
            })
            // Customer
            ->addColumn('management_customer', function($row) use($roles) {
                $myPermissions = collect($row->permissions->toArray());
                $customer = $roles->get('customer-manager');
                $input = '';
                
                if ($customer && isset($customer['permissions']) && is_array($customer['permissions'])) {
                    foreach ($customer['permissions'] as $item) {
                        if (!isset($item['permission_name']) || !isset($item['id'])) {
                            continue;
                        }
                        
                        $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                        $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_customer_manager_'.$item['id'].$row->id.'" '.$check.' value="'.$item['permission_name'].'" name="permission" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'">
                                    <label class="custom-control-label permission-change-btn" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'" for="role_customer_manager_'.$item['id'].$row->id.'">'.($item['permission_desc'] ?? $item['permission_name']).'</label>
                                </div>';
                    }
                }
                return $input;
            })
            // Account
            ->addColumn('management_account', function($row) use($roles) {
                $myPermissions = collect($row->permissions->toArray());
                $account = $roles->get('account-manager');
                $input = '';
                
                if ($account && isset($account['permissions']) && is_array($account['permissions'])) {
                    foreach ($account['permissions'] as $item) {
                        if (!isset($item['permission_name']) || !isset($item['id'])) {
                            continue;
                        }
                        
                        $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                        $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_account_manager_'.$item['id'].$row->id.'" '.$check.' value="'.$item['permission_name'].'" name="permission" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'">
                                    <label class="custom-control-label permission-change-btn" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'" for="role_account_manager_'.$item['id'].$row->id.'">'.($item['permission_desc'] ?? $item['permission_name']).'</label>
                                </div>';
                    }
                }
                return $input;
            })
            // Machine
            ->addColumn('management_machine', function($row) use ($roles){
                $myPermissions = collect($row->permissions->toArray());
                $machine = $roles->get('machine-manager');
                $input = '';
                
                if ($machine && isset($machine['permissions']) && is_array($machine['permissions'])) {
                    foreach ($machine['permissions'] as $item) {
                        if (!isset($item['permission_name']) || !isset($item['id'])) {
                            continue;
                        }
                        
                        $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                        $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_machine_manager_'.$item['id'].$row->id.'" '.$check.' value="'.$item['permission_name'].'" name="permission" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'">
                                    <label class="custom-control-label permission-change-btn" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'" for="role_machine_manager_'.$item['id'].$row->id.'">'.($item['permission_desc'] ?? $item['permission_name']).'</label>
                                </div>';
                    }
                }
                return $input;
            })
            // Selling
            ->addColumn('management_selling', function($row) use($roles) {
                $myPermissions = collect($row->permissions->toArray());
                $account = $roles->get('accountant-manager');
                $input = '';
                
                if ($account && isset($account['permissions']) && is_array($account['permissions'])) {
                    foreach ($account['permissions'] as $item) {
                        if (!isset($item['permission_name']) || !isset($item['id'])) {
                            continue;
                        }
                        
                        $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                        $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_accountant_manager_'.$item['id'].$row->id.'" '.$check.' value="'.$item['permission_name'].'" name="permission" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'">
                                    <label class="custom-control-label permission-change-btn" data-permission="'.$item['id'].'" data-merchant="'.$row->id.'" for="role_accountant_manager_'.$item['id'].$row->id.'">'.($item['permission_desc'] ?? $item['permission_name']).'</label>
                                </div>';
                    }
                }
                return $input;
            })
            ->addColumn('created', function($row) use ($roles){
                return '<p class="small mt-3">' . sortSearchDate($row->created_at) . '</p>';
            })
            ->rawColumns(['account','management_account','management_customer','management_machine','management_selling','created'])
            ->make(true);
    }

    public function permissionChange($account, $request){
        try {
            if ($request->check == 1) {
                $account->permissions()->attach([$request->permission], ['table' => MERCHANT]);
            } else {
                $account->permissionPivots()->where('permission_id', $request->permission)->delete();
            }
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Thay đổi quyền thành công!'));
        } catch (\Exception $e) {
            Log::error('[AccountRepository][permissionChange]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Thay đổi quyền không thành công!'));
        }
        return $this->response;
    }

    public function destroy($merchant)
    {
        try {
            $merchant->is_deleted = $this->model::DELETED;
            $merchant->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Xóa Tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error('[AccountRepository][destroy]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Xóa Tài khoản không thành công!');
        }
        return $this->response;
    }

    public function updateProfile($merchant, $request)
    {
        try {
            $merchant->name = $request->name;
            if ($request->email && $merchant->isSuperAdmin()) {
                $merchant->email = $request->email;
            }
            if ($request->new_password) {
                $merchant->password = Hash::make($request->new_password);
            }
            $merchant->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Cập nhật profile thành công!');
        } catch (\Exception $e) {
            Log::error('[AccountRepository][updateProfile]--'.$e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Cập nhật profile không thành công!');
        }
        return $this->response;
    }

    public function updateSetting($account, $merchant, $request)
    {
        try {
            $merchant->name = strip_tags($request->name);
            $merchant->save();

            $bank_name    = array_reverse($request->bank_name);
            $benefit_name = array_reverse($request->benefit_name);
            $bank_number  = array_reverse($request->bank_number);

            $bank_info = [];
            for ($i = 0; $i < $request->bank_account_number; $i++) {
                if ($i < config('merchant.max_bank')) {
                    $bank_info[$i]['bank_name']    = strip_tags($bank_name[$i]);
                    $bank_info[$i]['benefit_name'] = strip_tags($benefit_name[$i]);
                    $bank_info[$i]['bank_number']  = strip_tags($bank_number[$i]);
                }
            }

            $info = [
                'merchant_name'                 => strip_tags($request->name),
                'merchant_company'              => strip_tags($request->company),
                'merchant_address'              => strip_tags($request->company_address),
                'merchant_dept_collection_date' => strip_tags($request->dept_collection_date),
                'website'                       => strip_tags($request->website),
                'phone'                         => strip_tags($request->phone),
                'merchant_bank_info'            => json_encode($bank_info, JSON_UNESCAPED_UNICODE),
                'updated_by'                    => $account->id,
            ];
            if ($request->checkout_address) {
                $info['merchant_other_address'] = strip_tags($request->other_address_input);
            } else {
                $info['merchant_other_address'] = '';
            }

            if (empty($merchant->merchantInfo())) {
                $info['merchant_id'] = $merchant->id;
                $merchant->merchantInfo()->create($info);
            } else {
                $merchant->merchantInfo()->update($info);
            }

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('[AccountRepository][updateSetting]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Cập nhật Thông tin không thành công!');
        }
        return $this->response;
    }

    public function updateAccount($account, $request)
    {
        try {
            $account->name    = $request->name;
            $account->account = $request->email;
            $account->email   = $request->email;
            if ($request->password) {
                $account->password = Hash::make($request->password);
            }
            $account->save();
            $account->permissionPivots()->delete();
            $account->permissions()->attach($request->permissions, ['table' => $request->table]);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Cập nhật Tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error('[AccountRepository][updateAccount]--'.$e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Cập nhật Tài khoản không thành công!');
        }
        return $this->response;
    }

    public function storeAccount($request)
    {
        try {
            $currentUser = auth(MERCHANT)->user();
            if (!$currentUser) {
                throw new \Exception('Unauthorized access');
            }

            $password = $request->pass_make == 1 ? $request->password : Str::random(8);
            
            $newAccount = [
                'name'          => $request->name,
                'account'       => $request->email,
                'email'         => $request->email,
                'status'        => 3,
                'password'      => Hash::make($password),
                'created_by'    => $currentUser->id,
                'parent_id'     => $currentUser->getMerchantID(),
                'merchant_code' => $currentUser->merchant_code,
            ];
            
            DB::beginTransaction();
            try {
                $account = $this->model::create($newAccount);
                
                if ($account && $request->permissions) {
                    $account->permissions()->attach($request->permissions, ['table' => $request->table]);
                }
                
                DB::commit();
                
                $this->setData([$account, $password]);
                $this->setStatus(true);
                $this->setAlert('message');
                $this->setMessage('Tạo Tài khoản thành công!');
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('[AccountRepository][storeAccount]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Tạo Tài khoản không thành công!');
        }
        return $this->response;
    }

    // GET

    public function findAccountByID($id)
    {
        return $this->model::query()->where('id', $id)->where('is_deleted', '<>', $this->model::DELETED)->first();
    }

    public function findChildrenAccounts()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->where('id','<>', $merchant->id)
            ->where('id','<>', $merchant->getMerchantID())
            ->where('parent_id', $merchant->getMerchantID())
            ->where('merchant_code', $merchant->merchant_code)
            ->where('is_deleted','<>', $this->model::DELETED)
            ->where('status','<>', $this->model::REQUEST_NEW)
            ->with([
                'permissions' => function($q) {
                    $q->where('is_deleted', '<>', Permission::IS_DELETED);
                    $q->where('status', Permission::IS_ACTIVED);
                }
            ]);
    }
}

