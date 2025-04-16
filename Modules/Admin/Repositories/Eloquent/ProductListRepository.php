<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\ProductList;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\ProductListRepositoryInterface;

class ProductListRepository extends BaseRepository implements ProductListRepositoryInterface
{
    public function __construct(ProductList $model)
    {
        parent::__construct($model);
    }

    public function createProductList($machine, $request)
    {
        $maxProductPack = $request->max_product_pack;
        if(empty($maxProductPack)){
            return true;
        }
        $i = 0;
        $j = 1;
        foreach ($maxProductPack as $maxProduct){
            foreach ($maxProduct as $max) {
                $position = $i . $j;
                if($j == 10){
                    $position = ($i + 1) . '0';
                }
                $attribute = [
                    'merchant_id' => !empty($request->merchant) ? $request->merchant : 0,
                    'machine_id' => $machine->id,
                    'product_id' => 0,
                    'tray_id' => $i,
                    'position_id' => str_pad($position, 3, '0', STR_PAD_LEFT),
                    'product_item_number' => $max,
                    'product_item_current' => 0,
                    'product_order' => $j,
                    'product_price' => 0,
                    'status' => $this->model::IS_ACTIVATED,
                    'created_by' => auth(ADMIN)->user()->id
                ];
                $this->model->create($attribute);
                $j++;
            }
            $i++;
            $j=1;
        }
        return true;
    }

    public function updateProductList($machine, $request)
    {
        $productListCurrent = $this->getAllByMachineId($machine->id);

        if(empty($productListCurrent)){
            return $this->createProductList($machine, $request);
        }
        $arrListCurrent = array_combine(array_column($productListCurrent, 'position_id'), $productListCurrent);

        $maxProductPack = $request->max_product_pack;
        $i = 0;
        $j = 1;
        $arrStrPosition = [];
        foreach ($maxProductPack as $maxProduct){
            foreach ($maxProduct as $max) {
                $position = $i . $j;
                if($j == 10){
                    $position = ($i + 1) . '0';
                }
                $strPosition = str_pad($position, 3, '0', STR_PAD_LEFT);
                $arrStrPosition[] = $strPosition;

                if(!empty($arrListCurrent[$strPosition]['id'])){
                    $this->updatePack($arrListCurrent[$strPosition]['id'], [
                        'product_item_number' => $max,
                        'merchant_id' => !empty($request->merchant) ? $request->merchant : 0,
                    ]);
                } else {
                    $attribute = [
                        'merchant_id' => !empty($request->merchant) ? $request->merchant : 0,
                        'machine_id' => $machine->id,
                        'product_id' => 0,
                        'tray_id' => $i,
                        'position_id' => $strPosition,
                        'product_item_number' => $max,
                        'product_item_current' => 0,
                        'product_order' => $j,
                        'product_price' => 0,
                        'status' => $this->model::IS_ACTIVATED,
                        'created_by' => auth(ADMIN)->user()->id
                    ];
                    $this->createPack($attribute);
                };
                $j++;
            }
            $i++;
            $j=1;
        }

        $diff = array_diff(array_column($productListCurrent, 'position_id'), $arrStrPosition);
        if(!empty($diff)){
            $this->model::query()
                ->where('machine_id', $machine->id)
                ->whereIn('position_id', $diff)->update([
                    'is_deleted' => $this->model::IS_DELETED
                ]);
        }
    }

    public function createPack($attribute)
    {
        $this->model->create($attribute);
    }

    public function updatePack($productListId, $attribute)
    {
        $this->model::query()
            ->where(['id' => $productListId])
            ->update($attribute);
    }

    public function getAllByMachineId($machineId)
    {
        $result = $this->model::query()
            ->where('machine_id', $machineId)
            ->orderBy('position_id', 'ASC')
            ->get()
            ->toArray();
        return $result;
    }

    public function getListByMachineId($machineId)
    {
        $result = $this->model::query()
            ->where('machine_id', $machineId)
            ->orderBy('position_id', 'ASC')
            ->get()
            ->toArray();
        return $result;
    }
}
