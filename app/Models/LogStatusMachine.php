<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStatusMachine extends Model
{
    protected $table = 'log_status_machine';
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    const ACTIVE = 1;
    const ERROR = 0;

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
