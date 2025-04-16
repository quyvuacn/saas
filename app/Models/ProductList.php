<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    const IS_DELETED = 1;
    const IS_ACTIVATED = 1;
    const IS_INACTIVATED = 0;
    const HAS_PRODUCT = 1;

    protected $table = 'product_list';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function newQuery($excludeDeleted = true, $deleted = self::IS_DELETED)
    {
        return parent::newQuery($excludeDeleted)->where($this->table . '.is_deleted', '<>', $deleted);
    }

}
