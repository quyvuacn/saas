<?php

namespace Modules\Admin\Policies;

use App\Admin;
use http\Env\Request;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(Admin $admin)
    {
        if ($admin->isSuperAdmin()) {
            return true;
        }
    }

    public function list(Admin $admin)
    {
        $roles = $this->getRoles($admin);
        return $roles->get('account.list');
    }

    public function show(Admin $admin, Admin $child)
    {
        $roles = $this->getRoles($admin);
        return $roles->get('account.list') && $child->is_deleted !== 1;
    }

    public function edit(Admin $admin)
    {
        $roles = $this->getRoles($admin);
        return $roles->get('account.edit');
    }

    public function change(Admin $admin, Admin $child)
    {
        $roles = $this->getRoles($admin);
        return $roles->get('account.edit') && $child->id !== $admin->id && $child->is_deleted !== Admin::DELETED;
    }

    public function getRoles($admin)
    {
        $roles = collect($admin->permissions->toArray());
        return $roles->keyBy('permission_name');
    }

    public function isSuperAdmin($admin){
        if ($admin->isSuperAdmin()) {
            return true;
        }
        return false;
    }

    public function history(Admin $admin)
    {
        $roles = $this->getRoles($admin);
        return $roles->get('account.history');
    }
}
