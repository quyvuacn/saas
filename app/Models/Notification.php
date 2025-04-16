<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    const STATUS_NEW = 2;

    // protected $fillable = [];
    protected $table = 'notification';
    protected $guarded = ['updated_at', 'created_at'];

    public $timestamps = false;
}
