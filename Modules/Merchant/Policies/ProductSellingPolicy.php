<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Product;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use function Symfony\Component\String\u;

class ProductSellingPolicy
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
        return $merchant->getRole('product.selling.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('product.selling.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Product $product)
    {
        return $merchant->getRole('product.selling.edit') && $product && $product->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
