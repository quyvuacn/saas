<?php


namespace Modules\Admin\Classes\Facades;
use Illuminate\Support\Facades\Facade;

class AdminCan extends Facade
{
    protected static function getFacadeAccessor()
    { // Must have
        return 'admin-can'; // binding Alias, using in Provider
    }
}
