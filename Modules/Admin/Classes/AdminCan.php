<?php


namespace Modules\Admin\Classes;

class AdminCan
{
    public function do($role, $object = null)
    {
        if (!auth(ADMIN)->check()) {
            return false;
        }
        if ($object) {
            return auth(ADMIN)->user()->can($role, $object);
        } else {
            return auth(ADMIN)->user()->can($role);
        }
    }
}
