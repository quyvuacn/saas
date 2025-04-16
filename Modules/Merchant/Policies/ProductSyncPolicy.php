<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Machine;
use App\Models\Product;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use function Symfony\Component\String\u;

class ProductSyncPolicy
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
        return $merchant->getRole('product.sync.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('product.sync.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Machine $machine)
    {
        return $merchant->getRole('product.sync.edit') && $machine && $machine->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
