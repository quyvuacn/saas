<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatusMachine extends Model
{

    protected $table = 'report_status_machine';
    protected $guarded = ['updated_at'];

    public $timestamps = false;

    protected $dates = [
        'created_at'
    ];

    const MACHINE_WAS_GRANTED = 2;

}
