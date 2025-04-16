<?php

namespace App;

use App\Models\MerchantSettingStaff;
use Faker\Provider\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    const DELETED       = 1;
    const IS_CREDIT     = 1;
    const IS_NOT_CREDIT = 0;
    const EMPTY_CREDIT  = 0;
    const NEW_USER      = 0;
    const USER_ACTIVE   = 1;

    use Notifiable;
    protected $table = 'user';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'credit_updated_at',
    ];

    private $secret = 'A6(2$xSdvR&;4u7j';

    public function staff()
    {
        return $this->belongsTo(MerchantSettingStaff::class, 'email', 'employee_email', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function merchantUpdateBy()
    {
        return $this->belongsTo(Merchant::class, 'credit_updated_by', 'id');
    }

    public function hashPassword($password, $salt)
    {
        return md5($this->secret . $salt . $password);
    }

    public function generateSalt($length = 16)
    {
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function genereateUUID()
    {
        return Uuid::uuid();
    }
}
