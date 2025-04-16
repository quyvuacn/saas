<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellingHistoryPolicy
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
        return $merchant->getRole('selling.history.list') && $merchant->commonMerchant();
    }
}
