<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountPermission extends Model
{
    protected $table = 'account_permission';
    protected $guarded = [];
}
