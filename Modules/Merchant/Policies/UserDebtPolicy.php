<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;use function Symfony\Component\String\u;

class UserDebtPolicy
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
        return $merchant->getRole('customer.debt.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('customer.debt.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, User $userDebt)
    {
        return $merchant->getRole('customer.debt.edit') && $userDebt && $userDebt->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
