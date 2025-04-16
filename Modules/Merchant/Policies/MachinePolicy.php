<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use App\Models\Machine;
use App\Models\MerchantRequestMachine;use Illuminate\Auth\Access\HandlesAuthorization;

class MachinePolicy
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
        return $merchant->getRole('machine.list') && $merchant->commonMerchant();
    }

    public function show(Merchant $merchant, Machine $machine)
    {
        if (!$machine->merchant){
            return false;
        }
        return $merchant->getRole('machine.list') && $machine->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function edit(Merchant $merchant)
    {
        return $merchant->getRole('machine.edit') && $merchant->commonMerchant();
    }

    public function change(Merchant $merchant, Machine $machine)
    {
        return $merchant->getRole('machine.edit') && $machine->merchant && $machine->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }

    public function changeRequest(Merchant $merchant, MerchantRequestMachine $request)
    {
        return $merchant->getRole('machine.edit') && $request->merchant && $request->status == MerchantRequestMachine::REQUEST_NEW  && $request->merchant_id == $merchant->getMerchantID() && $merchant->commonMerchant();
    }
}
