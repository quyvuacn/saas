<?php

namespace Modules\Merchant\Policies;

use App\Merchant;
use Illuminate\Auth\Access\HandlesAuthorization;

class HasMachinePolicy
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

    public function hasAnyMachine(Merchant $merchant)
    {
        return $merchant->hasAnyMachine();
    }
}
