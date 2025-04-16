<?php

namespace App\Models;

use App\Admin;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $table = 'app_version';
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
