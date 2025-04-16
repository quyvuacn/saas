<?php

namespace Modules\Admin\Policies;

use App\Admin;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        return $admin->getRole('customer.list');
    }

    public function show(Admin $admin, User $user)
    {
        return $admin->getRole('customer.list') && $user->merchant_id == $admin->id;
    }

    public function edit(Admin $admin)
    {
        return $admin->getRole('customer.edit');
    }

    public function change(Admin $admin, User $user)
    {
        return $admin->getRole('customer.edit') && $user->merchant_id == $admin->id;
    }
}
