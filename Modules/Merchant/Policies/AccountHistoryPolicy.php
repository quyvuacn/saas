<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountHistoryPolicy
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

    public function list(Merchant $merchant)
    {
        return $merchant->getRole('account.history') && $merchant->commonMerchant();
    }
}
