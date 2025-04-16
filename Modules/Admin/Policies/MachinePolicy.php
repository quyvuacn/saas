<?php

namespace Modules\Admin\Policies;

use App\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class MachinePolicy
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
        return $admin->getRole('machine.list');
    }

    public function show(Admin $admin)
    {
        return $admin->getRole('machine.list');
    }

    public function edit(Admin $admin)
    {
        return $admin->getRole('machine.edit');
    }

    public function change(Admin $admin)
    {
        return $admin->getRole('machine.edit');
    }

    public function request(Admin $admin)
    {
        return $admin->getRole('machine_request.list');
    }

    public function approveRequest(Admin $admin)
    {
        return $admin->getRole('machine_request.edit');
    }

    public function requestBack(Admin $admin)
    {
        return $admin->getRole('machine_request_back.list');
    }

    public function approveRequestBack(Admin $admin)
    {
        return $admin->getRole('machine_request_back.edit');
    }

    public function processing(Admin $admin)
    {
        return $admin->getRole('machine.processing');
    }

    public function appVersionList(Admin $admin)
    {
        return $admin->getRole('machine.app_version_list');
    }

    public function appVersionEdit(Admin $admin)
    {
        return $admin->getRole('machine.app_version_edit');
    }
}
