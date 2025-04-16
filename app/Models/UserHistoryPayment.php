<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserHistoryPayment extends Model
{
    const TRANSACTION_TYPE_BUY   = 1;
    const PURCHASE_TYPE_RECHARGE = 5;
    const PURCHASE_TYPE_BANK     = 4;
    const TRANSACTION_COIN_EMPTY = 0;
    const DELETED                = 1;
    const STATUS_SUCCESS         = 'SUCCESS';
    const STATUS_NEW             = 'NEW';
    const STATUS_PROCESSING      = 'PROCESSING';
    const STATUS_ERROR           = 'ERROR';
    const BUY_TRANSACTION        = 2;

    // protected $fillable = [];
    protected $table = 'user_history_payment';
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid', 'id');
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function buyUser()
    {
        return $this->belongsTo(User::class, 'uid', 'uid', 'id');
    }
}
