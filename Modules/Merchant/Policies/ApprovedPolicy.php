<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovedPolicy
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

    public function isApproved(Merchant $merchant)
    {
        return $merchant->isApproved() && $merchant->commonMerchant() && $merchant->commonMerchant()->isApproved();
    }
}
