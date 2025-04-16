<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    CONST DELETED = 1;

    protected $table = 'product';
    protected $guarded = [];

    public function productList()
    {
        return $this->hasMany(ProductList::class, 'product_id', 'id')
            ->where('is_deleted', '!=', ProductList::IS_DELETED);
            // ->where('status', '!=', ProductList::IS_INACTIVATED); // Disable ray, but maybe has product
    }

    public function sellingProducts()
    {
        return $this->hasMany(ProductList::class, 'product_id', 'id')
            ->where('is_deleted', '!=', ProductList::IS_DELETED)
            ->where('status', '!=',ProductList::IS_INACTIVATED)
            ->where('product_item_current', '>=', ProductList::HAS_PRODUCT);

    }

    public function sellingCountProducts()
    {
        return $this->hasMany(ProductList::class, 'product_id', 'id')
            ->select('machine_id', 'product_id')
            ->selectRaw('SUM(product_item_current) as count')
            ->selectRaw('AVG(product_price) as price') // Not Use
            ->where('is_deleted', '!=', ProductList::IS_DELETED)
            ->where('status', '!=',ProductList::IS_INACTIVATED)
            ->where('product_item_current', '>=', ProductList::HAS_PRODUCT)
            ->groupBy('machine_id', 'product_id');
    }

    public function newQuery($excludeDeleted = true)
    {
        $account = auth(MERCHANT)->user();
        return parent::newQuery($excludeDeleted)->where('is_deleted', '!=', self::DELETED)->where('merchant_id', $account->getMerchantID());
    }
}
