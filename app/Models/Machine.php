<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Machine extends Model
{
    const IS_DELETED   = 1;
    const IS_ACTIVATED = 1;
    const IS_INACTIVATED = 0;

    const MACHINE_OTHER_PROBLEM = 0;
    const MACHINE_AVAIABLE = 1;
    const MACHINE_WAS_GRANTED = 2;

    const CONNECT_SUCCESS = 1;
    const CONNECT_FAILED = 0;

    const STATUS_NAME = [
        self::MACHINE_OTHER_PROBLEM => 'Vấn đề khác',
        self::MACHINE_AVAIABLE => 'Sẵn trong kho',
        self::MACHINE_WAS_GRANTED => 'Đã cho thuê'
    ];

    const DELETED = 1;

    protected $table = 'machine';
    protected $guarded = [];

    protected $dates = [
        'date_added',
        'created_at',
        'updated_at'
    ];

    public function attributeValues()
    {
        return $this->hasMany('App\Models\MachineAttributeValue');
    }

    public function attributes()
    {
        return $this->hasManyThrough('App\Models\MachineAttribute', 'App\Models\MachineAttributeValue', 'machine_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function subscription()
    {
        $account = auth(MERCHANT)->user();
        return $this->hasOne(Subscription::class, 'machine_id')->where('merchant_id', $account->getMerchantID());
    }

    public function newSubscriptionRequest()
    {
        $account = auth(MERCHANT)->user();
        return $this->hasOne(SubscriptionRequest::class, 'machine_id')->where('merchant_id', $account->getMerchantID())->where('status', SubscriptionRequest::REQUEST_NEW);
    }

    public function newRequestBack() {
        $account = auth(MERCHANT)->user();
        return $this->hasOne(MachineRequestBack::class, 'machine_id')->where('merchant_id', $account->getMerchantID())->where('status', MachineRequestBack::REQUEST_NEW);
    }

    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)
            ->where('is_deleted', '<>', self::IS_DELETED);
    }

    public function settingTray()
    {
        return $this->hasMany(ProductList::class);
    }

    public static function getCountByStatus()
    {
        $result = self::query()
            ->select(DB::raw('count(id) as cid, status'))
            ->groupBy('status')
            ->get()
            ->toArray();
        return $result;
    }

    public function productLists()
    {
        return $this->hasMany(ProductList::class)
            ->select('machine_id')
            ->selectRaw('SUM(product_item_number) as total_products')
            ->selectRaw('SUM(product_item_current) as remain_products')
            ->groupBy('machine_id')
            ->orderBy('remain_products');
    }
}
