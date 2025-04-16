<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
        return $merchant->getRole('account.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, Merchant $child)
    {
        return $merchant->getRole('account.list') && $child->merchant_code == $merchant->merchant_code && $child->parent_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('account.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Merchant $child)
    {
        return $merchant->getRole('account.edit')
            && $child->merchant_code == $merchant->merchant_code
            && $child->id !== $merchant->id
            && $child->parent_id !== 0
            && $child->parent_id == $merchant->getMerchantID()
            && $merchant->commonMerchant()
            && $child->is_deleted !== Merchant::DELETED;
    }
}
