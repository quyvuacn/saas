<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
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

    public function before(Merchant $merchant)
    {
        if ($merchant->isSuperAdmin()) {
            return true;
        }
    }

    public function viewDashboard(Merchant $merchant)
    {
        return $merchant->getRole('dashboard.list') && $merchant->commonMerchant();
    }
}
