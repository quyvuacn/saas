<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Admin;
use App\Models\AccountPermission;
use App\Models\Permission;
use Modules\Admin\Repositories\AdminRepositoryInterface;
use datatables;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{

    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $admins = $this->model::query()
            ->where('id', '<>', auth(ADMIN)->user()->id)
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->with([
                'permissions' => function ($q) {
                    $q->where('status', Permission::IS_ACTIVED);
                }
            ]);

        if(isset($request->status) && $request->status != -1){
            $admins->where('status', $request->status);
        }
        $listSuperAdmin = AccountPermission::query()
            ->select('account_permission.account_id')
            ->join('permissions', 'permissions.id', 'account_permission.permission_id')
            ->where('permissions.permission_name', 'super.admin')
            ->where('table', 'admin')
            ->get()
            ->toArray();

        $listSuperAdmin = array_column($listSuperAdmin, 'account_id');
        return \Yajra\DataTables\DataTables::of($admins)
            ->addColumn('account', function ($row) {
                return '<div data-search="'.$row->name.'"><p>' . $row->name . '</p><div class="small">
                                        Email: <strong>' . $row->email . '</strong>
                                    </div>
                                    <div class="small mt-1">
                                        <a href="' . route('admin.account.historyAdmin', ['id' => $row->id]) . '">[Xem lịch sử hoạt động]</a>
                                    </div>
                        </div>';
            })
            ->addColumn('account_name', function ($row) {
                return '<div data-search="'.$row->name.'"><p>' . $row->name . '</p><div class="small">
                                        Email: <strong>' . $row->email . '</strong>
                                    </div>
                                    <div class="small mt-1">
                                        <a href="' . route('admin.account.historyAdmin', ['id' => $row->id]) . '">[Xem lịch sử hoạt động]</a>
                                    </div>
                        </div>';
            })
            ->addColumn('permission', function ($row) {
                $roles = $row->permissions;
                $html = '';
                if (count($roles)) {
                    $html .= '<ul class="small">';
                    foreach ($roles as $role) {
                        $html .= '<li>' . $role->permission_desc . '</li>';
                    }
                    $html .= '</ul>';
                }
                return $html;
            })
            ->addColumn('date_create', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('last_login', function ($row) {
                //TODO Last_login
                return !empty($row->last_login) ? date('d/m/Y H:i:s', strtotime($row->last_login)) : '';
            })
            ->addColumn('status', function ($row) use ($listSuperAdmin){
                switch ($row->status) {
                    case Admin::ACTIVATED:
                        $class = in_array($row->id, $listSuperAdmin) ? 'disabled' : 'account-inactive-btn';
                        $status = '<span class="btn btn-success btn-sm mt-2 '.$class.'" data-id="' . $row->id . '"><i class="fas fa-check"></i>Enabled (click to disable )</span>';
                        break;
                    case Admin::INACTIVATED:
                        $class = in_array($row->id, $listSuperAdmin) ? 'disabled' : 'account-active-btn';
                        $status = '<span class="btn btn-danger btn-sm mt-2 '.$class.'" data-id="' . $row->id . '"><i class="fas fa-check"></i>Disabled (click to enable )</span>';
                        break;
                    default:
                        $status = '---';
                        break;
                }
                $status = '<div class="text-center">' . $status . '</div>';
                return $status;
            })
            ->addColumn('action', function ($row) use ($listSuperAdmin) {
                $class = in_array($row->id, $listSuperAdmin) ? 'disabled' : '';
                return '<div class="text-center">
                                    <a href="' . route('admin.account.edit', ['account' => $row->id]) . '" class="'.$class.'"><span class="btn btn-primary btn-sm mt-2"><i class="fas fa-edit"></i> Sửa</span></a>
                                    <span class="btn btn-danger btn-sm mt-2 account-delete-btn '.$class.'" data-id="' . $row->id . '"><i class="fas fa-trash"></i> Xóa</span>
                                </div>';
            })
            ->orderColumn('date_create', 'created_at $1')
            ->rawColumns(['account', 'permission', 'date_create', 'action', 'status'])
            ->make(true);
    }

    public function findById($adminId)
    {
        $result = $this->model->where('id', $adminId)->first();
        return $result;
    }
}
