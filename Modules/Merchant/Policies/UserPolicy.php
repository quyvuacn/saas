<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;use function Symfony\Component\String\u;

class UserPolicy
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
        return $merchant->getRole('customer.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, User $user)
    {
        return $merchant->getRole('customer.list') && $user && $user->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('customer.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, User $user)
    {
        return $merchant->getRole('customer.edit') && $user && $user->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
