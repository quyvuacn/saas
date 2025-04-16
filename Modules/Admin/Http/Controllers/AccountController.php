<?php

namespace Modules\Admin\Http\Controllers;

use App\Admin;
use App\Models\LogActionAdmin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Admin\Http\Requests\AccountProfileRequest;
use Modules\Admin\Http\Requests\AccountRequest;
use Modules\Admin\Http\Requests\AccountUpdateRequest;
use Modules\Admin\Repositories\AdminRepositoryInterface;
use Modules\Admin\Repositories\LogActionAdminRepositoryInterface;
use Modules\Admin\Repositories\RoleRepositoryInterface;
use Modules\Admin\Classes\Facades\AdminCan;

class AccountController extends Controller
{
    protected $roleRepository;
    protected $adminRepository;
    protected $logActionAdminRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        AdminRepositoryInterface $adminRepository,
        LogActionAdminRepositoryInterface $logActionAdminRepository
    )
    {
        $this->middleware('auth:admin');
        $this->middleware('change.password.account');

        $this->logActionAdminRepository = $logActionAdminRepository;
        $this->adminRepository = $adminRepository;
        $this->roleRepository = $roleRepository;
    }

    public function create(Request $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return redirect()->route('admin.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        $roles = $this->roleRepository->getRoleAdmin();
        return view('admin::account.create', compact('roles'));
    }

    public function edit($account)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return redirect()->route('admin.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $account = $this->adminRepository->findById($account);

        if(empty($account)){
            abort(404);
        }

        $roles = $this->roleRepository->getRoleAdmin();
        return view('admin::account.create', compact('roles', 'account'));
    }

    public function store(AccountRequest $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return redirect()->route('admin.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $password = $request->pass_make == 1 ? Str::random(8) : $request->password;
        $account = Admin::create([
            'name' => $request->name,
            'account' => $request->email,
            'email' => $request->email,
            'is_required_change_password' => $request->pass_change ?? 0,
            'password' => Hash::make($password),
            'status' => Admin::ACTIVATED,
            'created_by' => auth(ADMIN)->user()->id,
        ]);
        $account->permissions()->attach($request->permissions, ['table' => $request->table]);

        $attribute['content_request'] = [
            'ID' => $account->id,
            'Account Name' => $account->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        // TODO: send password
        $dataMail = [
            'view' => ($request->pass_make == 1) ? 'admin::email.admin-random-password' : 'admin::email.admin-register',
            'to' => $account->email,
            'data' => ($request->pass_make == 1) ? ['password' => $password, 'account' => $account] : ['account' => $account],
            'subject' => '[1giay.vn] Thông báo đăng ký tài khoản admin!'
        ];
        sendMailCustom($dataMail);

        return redirect()->route('admin.account.list')->with('message', __('Tạo tài khoản thành công!'));
    }

    public function update($account, AccountUpdateRequest $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return redirect()->route('admin.account.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        $account = Admin::query()->where('id', $account)->where('is_deleted', '<>', Admin::DELETED)->first();
        if (!$account
            // || !auth(ADMIN)->user()->can('account.change', $account)
        ) {
            return redirect()->route('admin.account.list')->with('error', __('Tài khoản không tồn tại, hoặc Tài khoản không có quyền hạn!'));
        } else {
            $account->name = $request->name;
            if(!empty($request->email)){
                $account->account = $request->email;
                $account->email = $request->email;
            }
            if ($request->password) {
                $account->password = Hash::make($request->password);

                $dataMail = [
                    'view' => 'admin::email.admin-random-password',
                    'to' => $account->email,
                    'data' => ['type' => 'update', 'password' => $request->password, 'account' => $account],
                    'subject' => '[1giay.vn] Thông báo đổi mật khẩu tài khoản admin!'
                ];
                sendMailCustom($dataMail);
            }
            $account->save();
            $account->permissionPivots()->delete();
            $account->permissions()->attach($request->permissions, ['table' => $request->table]);

            $attribute['content_request'] = [
                'ID' => $account->id,
                'Account Name' => $account->name
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);

            // TODO: send password
            return redirect()->route('admin.account.list')->with('message', __('Update tài khoản thành công!'));
        }
    }

    /**
     * @return mixed
     */
    public function list(Request $request)
    {
        if (!AdminCan::do('adm.account.list')) {
            return redirect()->route('admin.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }

        if ($request->ajax()) {
            return $this->adminRepository->list($request);
        }
        return view('admin::account.list');
    }

    public function destroy($account, Request $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return ['status' => false];
        }

        $admin = $this->adminRepository->findById($account);
        if (!$admin) {
            return ['status' => false];
        }

        $admin->delete($account);

        $attribute['content_request'] = [
            'ID' => $admin->id,
            'Account Name' => $admin->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return ['status' => true];
    }

    public function toggleStatus($account, Request $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return ['status' => false];
        }

        $admin = $this->adminRepository->findById($account);
        if (!$admin) {
            return ['status' => false];
        } else {
            $admin->status = $request->status;
            $admin->save();

            $attribute['content_request'] = [
                'ID' => $admin->id,
                'Account Name' => $admin->name
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);

            return ['status' => true];
        }
    }

    /**
     * @return mixed
     */
    public function permission(Request $request)
    {
         if (!AdminCan::do('adm.account.edit')) {
             return redirect()->route('admin.account.list')->with('error', 'Tài khoản không có quyền hạn này!');
         }
        if ($request->ajax()) {
            $email_name = $request->email_name;
            $roles = Role::query()->with([
                'permissions' => function ($q) {
                    $q->where('is_deleted', '<>', Permission::IS_DELETED);
                    $q->where('status', Permission::IS_ACTIVED);
                },
            ])
                ->where('status', Role::IS_ACTIVED)->where('is_deleted', '<>', Role::IS_DELETED)->get()->toArray();
            $roles = collect($roles)->keyBy('alias');
            $merchants = Admin::query()->with([
                'permissions' => function ($q) {
                    $q->where('is_deleted', '<>', Permission::IS_DELETED);
                    $q->where('status', Permission::IS_ACTIVED);
                }])
                ->where('id', '<>', auth(ADMIN)->user()->id)
                ->where('is_deleted', '<>', ADMIN::DELETED)
                ->where('status', '<>', ADMIN::INACTIVATED);
            if ($email_name) {
                $merchants = $merchants->where(function ($q) use ($email_name) {
                    $q->where('email', 'LIKE', '%' . $email_name . '%')->orWhere('name', 'LIKE', '%' . $email_name . '%');
                });
            }
            return datatables()->eloquent($merchants)
                ->addColumn('account', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $dashboard = $roles->get('dashboard-manager');
                    $input = '';
                    
                    if ($dashboard && isset($dashboard['permissions'])) {
                        foreach ($dashboard['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-group small custom-control custom-checkbox mt-3">
                                    <input type="checkbox" class="custom-control-input" id="role_dashboard_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_dashboard_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }
                    return '<a href="#">' . $row->email . '</a><p>' . $row->name . '</p>' . $input;
                })
                ->addColumn('management_account', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $account = $roles->get('account-manager');
                    $input = '';
                    
                    if ($account && isset($account['permissions'])) {
                        foreach ($account['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox" >
                                    <input type="checkbox" class="custom-control-input" id="role_account_manager_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_account_manager_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }
                    return $input;
                })
                ->addColumn('management_merchant', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $merchant = $roles->get('merchant-manager');
                    $input = '';
                    
                    if ($merchant && isset($merchant['permissions'])) {
                        foreach ($merchant['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_merchant_manager_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_merchant_manager_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }
                    return $input;
                })
                ->addColumn('management_machine', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $machine = $roles->get('machine-manager');
                    $input = '';
                    
                    if ($machine && isset($machine['permissions'])) {
                        foreach ($machine['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_machine_manager_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_machine_manager_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }

                    return $input;
                })
                ->addColumn('management_request_machine', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $machine = $roles->get('machine-request-manager');
                    $input = '';
                    
                    if ($machine && isset($machine['permissions'])) {
                        foreach ($machine['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_machine_request_manager_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_machine_request_manager_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }

                    return $input;
                })
                ->addColumn('management_subscription', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $subscription = $roles->get('subscription-manager');
                    $input = '';
                    
                    if ($subscription && isset($subscription['permissions'])) {
                        foreach ($subscription['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_subscription_manager_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_subscription_manager_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }
                    return $input;
                })
                ->addColumn('management_subscription_request', function ($row) use ($roles) {
                    $myPermissions = collect($row->permissions->toArray());
                    $subscription = $roles->get('subscription-request');
                    $input = '';
                    
                    if ($subscription && isset($subscription['permissions'])) {
                        foreach ($subscription['permissions'] as $item) {
                            $check = $myPermissions->contains('permission_name', $item['permission_name']) ? 'checked' : '';
                            $input .= '<div class="form-check small custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="role_subscription_request_' . $item['id'] . $row->id . '" ' . $check . ' value="' . $item['permission_name'] . '" name="permission" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '">
                                    <label class="custom-control-label permission-change-btn" data-permission="' . $item['id'] . '" data-admin="' . $row->id . '" for="role_subscription_request_' . $item['id'] . $row->id . '">' . $item['permission_desc'] . '</label>
                                </div>';
                        }
                    }
                    return $input;
                })
                ->addColumn('created', function ($row) use ($roles) {
                    return '<p class="small mt-3">
                            ' . $row->created_at->format('d/m/Y H:i') . '
                            </p>';
                })
                ->orderColumn('created', 'created_at $1')
                ->rawColumns(['account', 'management_account', 'management_merchant', 'management_machine', 'management_request_machine', 'management_subscription', 'management_subscription_request','created'])
                ->make(true);
        }
        return view('admin::account.permission');
    }

    public function permissionChange($account, Request $request)
    {
        if (!AdminCan::do('adm.account.edit')) {
            return ['status' => false, 'message' => 'Tài khoản không có quyền hạn này!'];
        }
        $account = $this->adminRepository->findById($account);
        if (!$account
            // || !auth(ADMIN)->user()->can('account.change', $account)
        ) {
            return ['status' => false];
        } else {
            if ($request->check == 1) {
                $account->permissions()->attach([$request->permission], ['table' => ADMIN]);
            } else {
                $account->permissionPivots()->where('permission_id', $request->permission)->delete();
            }

            $attribute['content_request'] = [
                'ID' => $account->id,
                'Account Name' => $account->name
            ];
            $this->logActionAdminRepository->createAction($request, $attribute);

            return ['status' => true, 'message' => 'Thay đổi quyền thành công!'];
        }
    }

    public function profile()
    {
        return view('admin::account.profile');
    }

    public function updateProfile(AccountProfileRequest $request)
    {
        $user = Admin::findOrFail(auth(ADMIN)->user()->id);
        $user->name = $request->input('name');
        if ($request->input('new_password')) {
            $user->password = Hash::make($request->input('new_password'));
            $user->is_required_change_password = 0;
        }
        $user->save();

        $attribute['content_request'] = [
            'ID' => $user->id,
            'Account Name' => $user->name
        ];
        $this->logActionAdminRepository->createAction($request, $attribute);

        return redirect()->route('admin.account.profile')->with('message', 'Cập nhật profile thành công!');
    }

    public function history(Request $request)
    {
        if ($request->route()->getName() === 'admin.account.historyAdmin' && !AdminCan::do('adm.account.history')) {
            if($request->id == auth(\ADMIN)->user()->id){
                return redirect()->route('admin.account.history');
            }
            return redirect()->route('admin.account.history')->with('error', __('Tài khoản không có quyền xem lịch sử người dùng khác!'));
        }
        if ($request->ajax()) {
            return $this->logActionAdminRepository->list($request);
        }
        $admin = $request->id ? $this->adminRepository->find($request->id) : '';
        return view('admin::account.history', compact('request', 'admin'));
    }
}
