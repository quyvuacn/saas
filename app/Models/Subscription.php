<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    // protected $fillable = [];
    protected $table = 'subscription';
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_expiration',
    ];

    public function machineSubscription()
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'id');
    }

    public function merchantSubscription()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function createCheckSum(){
        $privateKey = 'VTI_CHECKSUM';
        $checksum = implode('_', [
            $privateKey,
            $this->id,
            $this->machine_id,
            $this->merchant_id,
            $this->updated_by,
            $this->updated_at,
            $this->date_expiration
        ]);
        return md5($checksum);
    }

    public function compareCheckSum($checksum, $subscriptionId)
    {
        $subscription = self::find($subscriptionId);
        if(empty($subscription))
            return false;
        $compare = $this->createCheckSum();
        return $compare === $checksum;
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
