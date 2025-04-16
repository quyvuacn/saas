<?php

namespace Modules\Admin\Policies;

use App\Admin;
use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
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
        return $admin->getRole('merchant.list');
    }

    public function show(Admin $admin)
    {
        return $admin->getRole('merchant.list');
    }

    public function edit(Admin $admin)
    {
        return $admin->getRole('merchant.edit');
    }

    public function change(Admin $admin)
    {
        return $admin->getRole('merchant.edit');
    }

    public function request(Admin $admin)
    {
        return $admin->getRole('merchant_request.list');
    }

    public function approve(Admin $admin)
    {
        return $admin->getRole('merchant_request.edit');
    }
}
