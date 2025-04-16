<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    // protected $fillable = [];
    protected $table = 'subscription_request';
    protected $guarded = [];

    const IS_DELETED = 1;

    const REQUEST_NEW = 0;
    const REQUEST_WAITING_CONTRACT = 1;
    const REQUEST_WAITING_PAYMENT = 2;
    const REQUEST_SUCCESS = 3;
    const REQUEST_CANCEL = 4;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class,'merchant_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class,'machine_id', 'id');
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)
            ->where($this->table . '.is_deleted', '!=', self::IS_DELETED);
    }
}
