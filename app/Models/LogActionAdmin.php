<?php

namespace App\Models;

use App\Admin;
use Illuminate\Database\Eloquent\Model;

class LogActionAdmin extends Model
{
    protected $table = 'log_action_admin';
    protected $guarded = ['updated_at'];

    public $timestamps = false;

    protected $dates = [
        'created_at'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'account_id');
    }
}
