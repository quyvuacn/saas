<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class MerchantNotifications extends Model
{
    protected $table = 'merchant_notifications';
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'time_begin_show',
        'time_end_show',
    ];

    const UNREADED = 0;
    const READED = 1;

    const IS_DELETED = 1;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '<>', self::IS_DELETED);
    }
}
