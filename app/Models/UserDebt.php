<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserDebt extends Model
{
    const IS_NOT_DELETED  = 0;
    const IS_DELETED      = 1;
    const DEBT_NEW        = 0;
    const DEBT_PROCESSING = 1;
    const DEBT_DONE       = 2;
    const DEBT_CLEAR      = 0;
    const IS_LOCKED       = 1;
    const IS_UNLOCKED     = 0;

    // protected $fillable = [];
    protected $table = 'user_debt';
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
