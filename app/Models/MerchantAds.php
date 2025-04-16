<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantAds extends Model
{
    CONST IS_DELETED = 1;

    protected $table = 'merchant_ads';
    protected $guarded = [];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_ads_relation', 'ads_id', 'machine_id')
            ->where('status', Machine::MACHINE_WAS_GRANTED)
            ->where('is_deleted', '<>', Machine::IS_DELETED)->withTimestamps();
    }
}
