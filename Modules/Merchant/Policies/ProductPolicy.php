<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Product;use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
        return $merchant->getRole('product.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, Product $product)
    {
        return $merchant->getRole('product.list') && $product->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('product.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Product $product)
    {
        return $merchant->getRole('product.edit') && $product->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
