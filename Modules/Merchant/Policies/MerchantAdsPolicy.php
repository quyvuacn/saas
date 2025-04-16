<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\MerchantAds;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantAdsPolicy
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
        return $merchant->getRole('ads.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, MerchantAds $ads)
    {
        return $merchant->getRole('ads.list') && $ads->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('ads.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, MerchantAds $ads)
    {
        return $merchant->getRole('ads.edit') && $ads->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
