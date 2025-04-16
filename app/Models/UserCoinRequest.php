<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserCoinRequest extends Model
{
    const DELETED      = 1;
    const APPROVED     = 1;
    const NOT_APPROVED = 0;

    // protected $fillable = [];
    protected $table = 'user_coin_request';
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
