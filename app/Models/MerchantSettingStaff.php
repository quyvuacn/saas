<?php

namespace App\Models;

use App\Merchant;
use App\User;
use Illuminate\Database\Eloquent\Model;

class MerchantSettingStaff extends Model
{
    const IS_DELETED = 1;

    protected $table = 'merchant_setting_staff';
    protected $guarded = [];
    protected $primaryKey = 'id';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
