<?php


namespace Modules\Merchant\Classes\Facades;
use Illuminate\Support\Facades\Facade;

class MerchantCan extends Facade
{
    protected static function getFacadeAccessor()
    { // Must have
        return 'merchant-can'; // binding Alias, using in Provider
    }
}
