<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    const IS_DELETED = 1;
    const IS_ACTIVED = 1;

    protected $table = 'permissions';
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '!=', self::IS_DELETED);
    }
}
