<?php


namespace Modules\Admin\Repositories;


interface AccountPermissionRepositoryInterface
{
    public function getAccountSuperAdmin();

    public function accountIsSuperAdmin($accountId);
}
