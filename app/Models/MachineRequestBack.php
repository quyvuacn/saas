<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineRequestBack extends Model
{
    protected $table = 'machine_request_back';

    const IS_DELETE = 1;

    const REQUEST_NEW = 0;
    const REQUEST_WAITING_BACK = 1;
    const REQUEST_BACK_SUCCESS = 2;
    const REQUEST_SUCCESS = 3;
    const REQUEST_CANCEL = 4;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_return_machine',
    ];

    public function merchantInfo()
    {
        return $this->belongsTo('App\Merchant', 'merchant_id', 'id');
    }

    public function machineInfo()
    {
        return $this->belongsTo('App\Models\Machine', 'machine_id', 'id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)
            ->where($this->table . '.is_deleted', '!=', self::IS_DELETE);
    }
}
