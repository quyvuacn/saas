<?php

namespace App;

use App\Models\AccountPermission;
use App\Models\Machine;
use App\Models\MerchantInfo;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Merchant extends Authenticatable
{
    use Notifiable;

    const DELETED               = 1;
    const REQUEST_NEW           = 0;
    const REQUEST_WAITING       = 1;
    const REQUEST_WAITING_SETUP = 2;
    const REQUEST_SUCCESS       = 3;
    const REQUEST_CANCEL        = 4;

    protected $guard = 'merchant';

    protected $table = 'merchant';

    protected $guarded = [];

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

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
        return $this->getRole('super.admin') || $this->parent_id == 0;
    }

    public function hasAnyMachine() {
        return $this->commonMerchant() && $this->commonMerchant()->machines && $this->commonMerchant()->machines->count();
    }

    public function isApproved()
    {
        if ($this->status == 3) {
            return true;
        }
        return false;
    }

    public function getMerchantID()
    {
        if ($this->parent_id !== 0) {
            return $this->parent_id;
        }
        return $this->id;
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '!=', self::DELETED);
    }

    public function commonMerchant()
    {
        if ($this->parent_id !== 0) {
            return $this->belongsTo(Merchant::class, 'parent_id')->first();
        }
        return $this;
    }

    public function merchantInfo()
    {
        return $this->hasOne(MerchantInfo::class, 'merchant_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'account_permission', 'account_id', 'permission_id')->where('table', MERCHANT)->withPivot(['table'])->withTimestamps();
    }

    public function permissionPivots(){
        return $this->hasMany(AccountPermission::class, 'account_id')->where('table', MERCHANT);
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class, 'merchant_id', 'id');
    }

    public function machineSubscription()
    {
        return $this->hasManyThrough(Machine::class, Subscription::class, 'machine_id', 'id');
    }

    public function machines()
    {
        return $this->hasMany(Machine::class, 'merchant_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Merchant::class, 'parent_id', 'id');
    }
}
