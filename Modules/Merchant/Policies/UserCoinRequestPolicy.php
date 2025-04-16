<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;use function Symfony\Component\String\u;

class UserCoinRequestPolicy
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
        return $merchant->getRole('customer.coin.request.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('customer.coin.request.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, User $userDebt)
    {
        return $merchant->getRole('customer.coin.request.edit') && $userDebt && $userDebt->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
