<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\MerchantAds;
use App\Models\MerchantNotifications;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotifyPolicy
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
        return $merchant->getRole('customer.notification.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('customer.notification.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, MerchantNotifications $notify)
    {
        return $merchant->getRole('customer.notification.edit') && $notify->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
