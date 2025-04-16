<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\AccountPermission;
use Modules\Admin\Repositories\AccountPermissionRepositoryInterface;
use datatables;

class AccountPermissionRepository extends BaseRepository implements AccountPermissionRepositoryInterface
{

    public function __construct(AccountPermission $model)
    {
        parent::__construct($model);
    }

    public function getAccountSuperAdmin()
    {

    }

    public function accountIsSuperAdmin($accountId)
    {

    }
}
