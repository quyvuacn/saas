<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    // protected $fillable = [];
    protected $table = 'subscription_history';
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_expiration_begin',
        'date_expiration_end',
    ];

    const IS_ACTIVE = 1;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function subscriptionRequest()
    {
        return $this->belongsTo(SubscriptionRequest::class, 'subscription_request_id')->where('status', '<>',  SubscriptionRequest::IS_DELETED);
    }

    public function generateCode($requestId)
    {
        $number = (strlen($requestId) > 10) ? $requestId : str_pad($requestId, 10, '0', STR_PAD_LEFT);
        $code = 'VTI-' . $number;
        return $code;
    }

    public function generateChecksum()
    {
        $privateKey = 'VTI_SUBSCRIPTION_HISTORY';
        $checksum = implode('_', [
            $privateKey,
            $this->id,
            $this->code,
            $this->date_expiration_begin,
            $this->date_expiration_end,
            $this->created_by,
            $this->created_at
        ]);
        return md5($checksum);
    }



}
