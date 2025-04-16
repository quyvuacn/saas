<?php

namespace App\Models;

use App\Admin;
use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class LogActionMerchant extends Model
{
    protected $table = 'log_action_merchant';
    protected $guarded = ['updated_at'];

    public $timestamps = false;

    protected $dates = [
        'created_at'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'account_id');
    }
}
