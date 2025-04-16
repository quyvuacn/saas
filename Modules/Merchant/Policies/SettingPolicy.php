<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
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

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('setting.merchant.edit') && $merchant->commonMerchant();
    }
}
