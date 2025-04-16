<?php


namespace Modules\Merchant\Classes;

class MerchantCan
{
    public function do($role, $object = null)
    {
        if (!auth(MERCHANT)->check()) {
            return false;
        }
        if ($object) {
            return auth(MERCHANT)->user()->can($role, $object);
        } else {
            return auth(MERCHANT)->user()->can($role);
        }
    }
}
