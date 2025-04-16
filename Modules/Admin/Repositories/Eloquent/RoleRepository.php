<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Permission;
use App\Models\Role;
use Modules\Admin\Repositories\PermissionRepositoryInterface;
use Modules\Admin\Repositories\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{

    protected $permissionRepository;

    public function __construct(Role $model) {
        parent::__construct($model);
    }

    public function getRoleAdmin()
    {
        $result = $this->model::query()->with([
            'permissions' => function ($q) {
                $q->where('is_deleted', '<>', Permission::IS_DELETED);
                $q->where('status', Permission::IS_ACTIVED);
            }])
            ->where('status', $this->model::IS_ACTIVED)
            ->where(function ($query) {
                $query->where('group', $this->model::GROUP_ALL)
                    ->orWhere('group', $this->model::GROUP_ADMIN);
            })->get();
        return $result;
    }
}
