<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const IS_DELETED = 1;
    const IS_ACTIVED = 1;

    const GROUP_ADMIN = 'admin';
    const GROUP_MERCHANT = 'merchant';
    const GROUP_ALL = 'all';

    protected $table = 'roles';
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '!=', self::IS_DELETED);
    }
}
