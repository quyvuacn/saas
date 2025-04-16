<?php


namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Permission;
use App\Models\Role;
use Modules\Admin\Repositories\PermissionRepositoryInterface;
use Modules\Admin\Repositories\RoleRepositoryInterface;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    protected $roleRepository;

    public function __construct(Permission $model, RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
        parent::__construct($model);
    }

    public function superAdmin()
    {
        return $this->model::query()
            ->whereHas('roles',function ($q) {
                $q->where('status', Role::IS_ACTIVED);
                $q->where('is_deleted', '<>', Role::IS_DELETED);
            })
            ->where('permission_name', 'super.admin')
            ->where('status', Permission::IS_ACTIVED)
            ->first();
    }
}
