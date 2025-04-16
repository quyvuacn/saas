<?php

namespace Modules\Admin\Policies;

use App\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
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

    public function viewDashboard(Admin $admin)
    {
        return $admin->getRole('dashboard.list');
    }
}
