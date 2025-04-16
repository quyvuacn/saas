<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use function Symfony\Component\String\u;

class PermissionPolicy
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
        return $merchant->getRole('permission.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('permission.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Merchant $child)
    {
        return $merchant->getRole('permission.edit')
            && $child->merchant_code == $merchant->merchant_code
            && $child->id !== $merchant->id
            && $child->parent_id !== 0
            && $child->parent_id == $merchant->getMerchantID()
            && $merchant->commonMerchant()
            && $child->is_deleted !== Merchant::DELETED;
    }
}
