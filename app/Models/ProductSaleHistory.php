<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ProductSaleHistory extends Model
{
    const NEW     = 0;
    const DONE    = 1;
    const FAILED  = 1;
    const UNKNOWN = 2;

    protected $table = 'product_sale_history';
    protected $guarded = [];
    protected $dates = [
        'created_at',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
