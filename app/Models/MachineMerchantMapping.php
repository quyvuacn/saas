<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class MachineMerchantMapping extends Model
{
    protected $table = 'machine_merchant_mapping';
    protected $guarded = [];

    const DELETED = 1;

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '!=', self::DELETED);
    }

    public function merchantInfoMapping()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
