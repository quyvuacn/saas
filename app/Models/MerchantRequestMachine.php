<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantRequestMachine extends Model
{
    protected $table = 'merchant_request_machine';
    protected $guarded = [];

    const REQUEST_NEW           = 0;
    const REQUEST_WAITING_AUDIT = 1;
    const REQUEST_WAITING       = 2;
    const REQUEST_SETUP_SUCCESS = 3;
    const REQUEST_SUCCESS       = 4;
    const REQUEST_CANCEL        = 5;

    const DELETED = 1;

    protected $dates = [
        'machine_date_receive',
        'created_at',
        'updated_at',
        'approved_at',
    ];

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where($this->table . '.is_deleted', '!=', self::DELETED);
    }

    public function merchant()
    {
        return $this->belongsTo('App\Merchant');
    }
}
