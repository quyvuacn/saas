<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
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
        return $merchant->getRole('subscription.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, Subscription $subscription)
    {
        if (!$subscription->merchant){
            return false;
        }
        return $merchant->getRole('subscription.list') && $subscription->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('subscription.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Subscription $subscription)
    {
        if (!$subscription->merchant){
            return false;
        }
        return $merchant->getRole('subscription.edit') && $subscription->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
