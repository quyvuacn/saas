<?php

namespace Modules\Merchant\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineAttributeValue;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\ProductRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\MachineRepositoryInterface;use Modules\Merchant\Repositories\ProductListRepositoryInterface;use Modules\Merchant\Repositories\ProductRepositoryInterface;
use Psy\Util\Json;
use Yajra\DataTables\DataTables;use function GuzzleHttp\Psr7\_parse_request_uri;

class ProductController extends Controller
{
    protected $productRepository;

    protected $machineRepository;

    protected $productListRepository;

    protected $logActionMerchantRepository;

    public function __construct(ProductRepositoryInterface $productRepository,
        MachineRepositoryInterface $machineRepository,
        ProductListRepositoryInterface $productListRepository,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    )
    {
        $this->middleware('auth:merchant');
        $this->productRepository = $productRepository;
        $this->machineRepository = $machineRepository;
        $this->productListRepository = $productListRepository;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('product.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->productRepository->list();
        }
        return view('merchant::product.list');
    }

    public function create()
    {
        if (!MerchantCan::do('product.edit')) {
            return redirect()->route('merchant.product.list')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        return view('merchant::product.create');
    }

    public function edit($product)
    {
        $product = $this->productRepository->findProductByID($product);
        if (!$product || !MerchantCan::do('product.change', $product)) {
            return redirect()->route('merchant.product.list')->with('error', __('Sản phẩm không tồn tại, hoặc Tài khoản không có quyền hạn!'));
        }
        $product->image = makeImageProduct($product->image);
        return view('merchant::product.edit', compact('product'));
    }

    public function update(ProductRequest $request, $product)
    {
        $product = $this->productRepository->findProductByID($product);
        if (!$product || !MerchantCan::do('product.change', $product)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Sản phẩm không tồn tại, hoặc Tài khoản không có quyền hạn!')];
        } else {
            $response = $this->productRepository->updateProduct($product, $request);

            if($response['status']) {
                $attribute['content_request'] = [
                    'Product ID' => $product->id,
                    'Name' => $product->name,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function store(ProductRequest $request)
    {
        if (!MerchantCan::do('product.edit')) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn này!')];
        } else {
            $response = $this->productRepository->storeProduct($request);

            if($response['status']) {
                $product = $response['data'];
                $attribute['content_request'] = [
                    'Product ID' => $product->id,
                    'Name' => $product->name,
                ];
                $this->logActionMerchantRepository->createAction($request, $attribute);
            }

            return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
        }
    }

    public function destroy($product, Request $request){
        $product = $this->productRepository->findProductByID($product);
        if (!$product) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Sản phẩm không tồn tại')];
        } else {
            if ($product->productList->count() > 0) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Không thể xóa sản phẩm đang bán hàng')];
            } elseif (!MerchantCan::do('product.change', $product)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn')];
            } else {
                $response = $this->productRepository->destroy($product);

                if($response['status']) {
                    $attribute['content_request'] = [
                        'Product ID' => $product->id,
                        'Name' => $product->name,
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function sync(Request $request)
    {
        if (!MerchantCan::do('product.sync.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if($request->ajax()){
            return $this->machineRepository->sync();
        }
        return view('merchant::product.sync');
    }

    public function syncDetail($machine)
    {
        $merchant = auth(MERCHANT)->user();
        $machine = $this->machineRepository->findMachineOfMerchantByID($machine, $merchant->getMerchantID());
        if (!$machine) {
            return redirect()->route('merchant.product.sync')->with('error', __('Không tồn tại Máy bàn hàng này!'));
        }
        if (!MerchantCan::do('product.sync.change', $machine)) {
            return redirect()->route('merchant.product.sync')->with('error', __('Tài khoản không có quyền hạn!'));
        }
        $arrayTray = $this->productListRepository->getListByMachineId($machine->id);
        $packsOfTrays = $this->productListRepository->findPacksOfTrays($machine->id, $arrayTray);
        $merchantProducts = $this->productRepository->getProductsOfMerchant()->orderBy('name', 'ASC')->get();
        $merchantProductsJS = json_encode($merchantProducts->pluck('price_default', 'id')->toArray());
        return view('merchant::product.sync-detail', compact('machine', 'arrayTray', 'packsOfTrays', 'merchantProducts', 'merchantProductsJS'));
    }

    public function togglePack($pack, Request $request)
    {
        $pack = $this->productListRepository->findPackOfMerchantByID($pack);
        if (!$pack) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Pack không tồn tại!')];
        } else {
            if (!$pack->machine || ($pack->machine && !MerchantCan::do('product.sync.change', $pack->machine))) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn')];
            } else {
                $response = $this->productListRepository->togglePack($pack);

                if($response['status']) {
                    $attribute['content_request'] = [
                        'ID' => $pack->id,
                        'Product ID' => $pack->product_id
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function updateMachineProducts($machine, Request $request)
    {
        $merchant = auth(MERCHANT)->user();
        $machine = $this->machineRepository->findMachineOfMerchantByID($machine, $merchant->getMerchantID());
        if (!$machine) {
            return redirect()->route('merchant.product.list')->with('error', __('Không tồn tại Máy bàn hàng này!'));
        }
        if (!MerchantCan::do('product.sync.change', $machine)) {
            return redirect()->route('merchant.product.list')->with('error', __('Tài khoản không có quyền hạn!'));
        }
        $merchantProducts = $this->productRepository->getProductsOfMerchant()->get()->modelKeys();
        $response = $this->productListRepository->updateMachineProducts($machine, $request, $merchantProducts);

        if($response['status']) {
            $attribute['content_request'] = [
                'Machine ID' => $machine->id
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);
        }

        return redirect()->route('merchant.product.syncDetail', compact('machine'))->with($response['alert'], $response['message']);
    }

    public function selling(Request $request)
    {
        if (!MerchantCan::do('product.selling.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->productRepository->selling();
        }
        return view('merchant::product.selling');
    }

    // UPLOAD IMAGE
    // src/Illuminate/Http/UploadedFile.php
    // $file->getPathname(),
    // $file->getClientOriginalName(),
    // $file->getClientMimeType(),
    // $file->getError(),

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $imgName = $request->file->getClientOriginalName();
            return $request->file->storeAs('public', $imgName); // storeAs, not use store
        }
        dd($request->file);
    }
}
