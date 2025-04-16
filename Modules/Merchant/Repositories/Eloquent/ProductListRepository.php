<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\ProductList;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\ProductListRepositoryInterface;
use Yajra\DataTables\DataTables;

class ProductListRepository extends BaseRepository implements ProductListRepositoryInterface
{
    public function __construct(ProductList $model)
    {
        parent::__construct($model);
    }

    public function findPacksOfTrays($machine_id, $tray_ids)
    {
        return $this->model::query()
            ->whereIn('tray_id', $tray_ids)
            ->where('machine_id', $machine_id)
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->orderBy('product_order', 'ASC')->get();
    }

    public function findPackOfMerchantByID($pack_id)
    {
        return $this->model::query()
            ->where('id', $pack_id)
            ->with('machine')
            ->whereHas('machine')
            ->where('is_deleted', '<>', $this->model::IS_DELETED)->first();
    }

    public function togglePack($pack)
    {
        try {
            $pack->status = !$pack->status;
            $pack->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Thay đổi Pack thành công!');
        } catch (\Exception $e) {
            Log::error('[ProductListRepository][togglePack]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Đã xảy ra lỗi, hoặc Tài khoản không có quyền hạn này!');
        }
        return $this->response;
    }

    public function updateMachineProducts($machine, $request, $merchantProducts)
    {
        $product_ids    = $request->product_select_ ?? [];
        $product_prices = $request->product_price_ ?? [];
        $product_qtys   = $request->product_qty_ ?? [];

        $numberTray = $machine->number_tray && $machine->number_tray > 0 ? $machine->number_tray : 0;
        $arrayTray = array();
        for ($i = 0; $i < $numberTray ; $i++) {
            $arrayTray[] = $i;
        }
        $maxProductInPack = $this->findPacksOfTrays($machine->id, $arrayTray)->pluck('product_item_number', 'id');

        $productList    = [];

        foreach ($product_ids as $pack => $product_id) {
            $tmp = array_shift($product_id);
            $productList[$pack]['product_id'] = $tmp;
        }
        foreach ($product_prices as $pack => $product_price) {
            $tmp = array_shift($product_price);
            $productList[$pack]['product_price'] = $tmp;
        }
        foreach ($product_qtys as $pack => $product_qty) {
            $tmp = array_shift($product_qty);
            $productList[$pack]['product_item_current'] = $tmp;
        }

        try {

            foreach ($productList as $id => $pack) {
                if ($pack['product_id'] && in_array($pack['product_id'], $merchantProducts)
                    && $pack['product_price'] <= 100000 && $pack['product_price'] >= 0
                    && $pack['product_item_current'] <= $maxProductInPack[$id]) {
                    ProductList::where('id', $id)->where('is_deleted', '<>', $this->model::IS_DELETED)->where('status', $this->model::IS_ACTIVATED)->update([
                        'product_id'           => $pack['product_id'],
                        'product_price'        => $pack['product_price'],
                        'product_item_current' => $pack['product_item_current'],
                        'updated_by'           => auth(MERCHANT)->id(),
                    ]);
                }
                if (!$pack['product_id'] || $pack['product_id'] <= 0 || !in_array($pack['product_id'], $merchantProducts)) {
                    ProductList::where('id', $id)->where('is_deleted', '<>', $this->model::IS_DELETED)->where('status', $this->model::IS_ACTIVATED)->update([
                        'product_id'           => 0,
                        'product_price'        => 0,
                        'product_item_current' => 0,
                        'updated_by'           => auth(MERCHANT)->id(),
                    ]);
                }
            }

            if(!empty($request->sync_machine) && !empty($machine->mqtt_topic) && !$this->callApiSyncProductMachine($machine->mqtt_topic)) {
                $this->setStatus(false);
                $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                $this->setAlert('error');
                $this->setMessage('Đồng bộ với máy bán hàng thất bại!');
                return $this->response;
            }

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Update Máy bán hàng thành công!');
        } catch (\Exception $e) {
            Log::error('[ProductListRepository][updateMachineProducts]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Đã xảy ra lỗi, hoặc Tài khoản không có quyền hạn này!');
        }
        return $this->response;
    }

    protected function callApiSyncProductMachine($mqttTopic){
        try{

            $curl = curl_init();
            $data = [
                'topic' => $mqttTopic,
                'message' => "{\"cmdType\"=\"MACHINE_SYNC_PRODUCT_TRIGGER\"}"
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('api.base_url') . config('api.url_api.sync_product_machine'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json"
                ),
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(!isset($response['code']) || $response['code'] !== 'ES200'){
                Log::info("[SyncProductList][SyncFailed] - mqtt_topic({$mqttTopic}) - response: " . json_encode($response));
                return false;
            }
            return true;
        } catch (\Exception $e){
            Log::error('[SyncProductList][SyncError]--' . $e->getMessage());
        }
    }

    public function getListByMachineId($machineId)
    {
        return $this->model::query()
            ->distinct()
            ->select('tray_id')
            ->where('machine_id', $machineId)
            ->orderBy('tray_id', 'ASC')
            ->pluck('tray_id');
    }
}
