<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Permission;
use App\Models\Role;
use Modules\Merchant\Repositories\AccountRepositoryInterface;
use Modules\Merchant\Repositories\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function roleWithPermissions()
    {
        return Role::query()->with([
            'permissions' => function ($q) {
                $q->where('is_deleted', '<>', Permission::IS_DELETED);
                $q->where('status', Permission::IS_ACTIVED);
            }])
            ->where('status', Role::IS_ACTIVED)
            ->where(function ($q) {
                $q->where('group', Role::GROUP_MERCHANT);
                $q->orWhere('group', Role::GROUP_ALL);
            })->where('is_deleted', '<>', Role::IS_DELETED)
            ->orderBy('order')
            ->get();
    }

}
