<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;use function Symfony\Component\String\u;

class UserCreditPolicy
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
        return $merchant->getRole('customer.credit.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('customer.credit.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, User $userCredit)
    {
        return $merchant->getRole('customer.credit.edit') && $userCredit && $userCredit->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
