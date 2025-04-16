<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantInfo extends Model
{
    const CREATED_AT = 'merchant_request_date';
    const UPDATED_AT = 'merchant_updated_at';

    protected $table = 'merchant_info';
    protected $guarded = [];
    protected $primaryKey = 'merchant_id';
}
