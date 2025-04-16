<?php

namespace App;

use App\Models\AccountPermission;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    const DELETED     = 1;
    const INACTIVATED = 0;
    const ACTIVATED   = 1;

    protected $guard = 'admin';

    protected $table = 'admin';

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getRoles()
    {
        $roles = collect($this->permissions->toArray());
        return $roles->keyBy('permission_name');
    }

    public function getRole($role)
    {
        $roles = $this->getRoles();
        return $roles->get($role);
    }

    public function isSuperAdmin()
    {
        return $this->getRole('super.admin');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'account_permission', 'account_id', 'permission_id')->where('table', ADMIN)->withPivot(['table'])->withTimestamps();
    }

    public function permissionPivots(){
        return $this->hasMany(AccountPermission::class, 'account_id')->where('table', ADMIN);
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '<>', self::DELETED);
    }
}
