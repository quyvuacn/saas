<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\MachineRequest;
use App\Models\MerchantRequestMachine;
use App\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class MachineRequestPolicy
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
        return $merchant->getRole('machine_request.list') && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('machine_request.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, MerchantRequestMachine $request)
    {
        return $merchant->getRole('machine_request.edit') && $request->merchant && $request->status == MerchantRequestMachine::REQUEST_NEW  && $request->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
