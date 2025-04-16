<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductSaleHistory;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\ProductRepositoryInterface;
use Yajra\DataTables\DataTables;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $productSaleHistory;

    public function __construct(Product $model, ProductSaleHistory $productSaleHistory)
    {
        parent::__construct($model);
        $this->productSaleHistory = $productSaleHistory;
    }

    public function list()
    {
        $products = $this->getProductsOfMerchant()->get();
        return DataTables::of($products)
            ->addColumn('name', function($row){
                $name = $row->name;
                return $name;
            })
            ->addColumn('brief', function($row){
                $brief = $row->brief;
                return $brief;
            })
            ->addColumn('image', function($row){
                $url = '<div class="text-center"><img width="100px" height="75px" style="object-fit: cover;" src="'.makeImageProduct($row->image).'"/></div>';
                return $url;
            })
            ->addColumn('price_default', function($row){
                return sortSearchPrice($row->price_default);
            })
            ->addColumn('created_at', function($row){
                return sortSearchDate($row->created_at);
            })
            ->addColumn('action', function($row){
                $action = '<a href="'.route("merchant.product.edit", ["product" => $row->id]).'" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                                <a onclick="deleteProduct('.$row->id.');return false;" href="javascript:;" class="btn btn-danger btn-sm btn-delete-product"><i class="fas fa-trash"></i> Xóa</a>';
                return $action;
            })
            ->rawColumns(['image','action','price_default', 'created_at'])
            ->make(true);

    }

    public function selling()
    {
        $products = $this->getProductsOfMerchant(true)->get();
        return DataTables::of($products)
            ->addColumn('name', function($row){
                return $row->name;
            })
            ->addColumn('brief', function($row){
                return $row->brief;
            })
            ->addColumn('image', function($row){
                return '<div class="text-center"><img width="100px" height="75px" style="object-fit: cover;" src="'.makeImageProduct($row->image).'"/></div>';
            })
            ->addColumn('price', function($row){
                if ($row->sellingProducts && $row->sellingProducts->count()) {
                    $content = '<ul class="list-group list-group-flush p-0 m-0">';
                    if ($row->sellingCountProducts && $row->sellingCountProducts->count()) {
                        foreach ($row->sellingCountProducts as $count) {
                            if ($count->product_id = $row->id) {
                                $priceArray = [];
                                $priceLabelArray = [];
                                foreach ($row->sellingProducts as $machine) {
                                    if ($machine->product_id = $row->id && $machine->machine_id == $count->machine_id) {
                                        $priceArray[] = '<span style="display: none">'.$machine->product_price.'</span>'.number_format($machine->product_price, 0);
                                        $priceLabelArray[] = number_format($machine->product_price, 0);
                                    }
                                }
                                $price = implode(' - ',array_unique($priceArray));
                                $priceLabel = implode(' - ',array_unique($priceLabelArray));
                                $content .= '<li class="list-group-item pt-1 pb-1"><strong style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden" title="'.$priceLabel.'">' . $price . ' <sup>đ</sup></strong></li>';
                                unset($priceArray);
                            }
                        }
                    }

                    $content .= '</ul>';
                    return $content;
                } else {
                    return '---';
                }
            })
            ->addColumn('created_at', function($row){
                return sortSearchDate($row->created_at);
            })
            ->addColumn('machines', function($row){
                $machine_in = [];
                if ($row->sellingProducts && $row->sellingProducts->count()) {
                    $content = '<ul class="list-group list-group-flush p-0 m-0">';
                    foreach ($row->sellingProducts as $productList) {
                        if (!in_array($productList->machine->id, $machine_in)) {
                            $c = 0;
                            if ($row->sellingCountProducts && $row->sellingCountProducts->count()) {
                                foreach ($row->sellingCountProducts as $count) {
                                    if ($productList->machine->id == $count->machine_id) {
                                        $c = $count->count;
                                    }
                                }
                            }
                            $title = $productList->machine ? ucfirst($productList->machine->name) : '---';
                            $content .= '<li class="list-group-item pt-1 pb-1"><a href="'.route("merchant.product.syncDetail", ["machine" => $productList->machine->id]).'"><small style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden" title="'.$title.'">' . $title . '</small></a></li>';
                            $machine_in[] = $productList->machine->id;
                        }
                    }
                    $content .= '</ul>';
                    return $content;
                } else {
                    return '---';
                }
            })
            ->addColumn('count', function($row){
                $machine_in = [];
                if ($row->sellingProducts && $row->sellingProducts->count()) {
                    $content = '<ul class="list-group list-group-flush p-0 m-0">';
                    foreach ($row->sellingProducts as $productList) {
                        if (!in_array($productList->machine->id, $machine_in)) {
                            $c = 0;
                            if ($row->sellingCountProducts && $row->sellingCountProducts->count()) {
                                foreach ($row->sellingCountProducts as $count) {
                                    if ($productList->machine->id == $count->machine_id) {
                                        $c = $count->count;
                                    }
                                }
                            }
                            $content .= '<li class="list-group-item pt-1 pb-1"><strong>' . $c.'</strong> </li>';
                            $machine_in[] = $productList->machine->id;
                        }
                    }
                    $content .= '</ul>';
                    return $content;
                } else {
                    return 0;
                }
            })
            ->addColumn('action', function($row){
                return '<a href="'.route("merchant.product.edit", ["product" => $row->id]).'" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Thay đổi thông tin</a>';
            })
            ->rawColumns(['image','action','price', 'created_at', 'machines', 'count'])
            ->make(true);

    }

    public function getProductsOfMerchant($with_selling = false)
    {
        $merchant = auth(MERCHANT)->user();
        $products = $this->model::query();
        if ($with_selling) {
            $products = $products->whereHas('sellingProducts')->with([
                'sellingProducts', 'sellingCountProducts'
            ]);
        }
        return $products->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted', '<>', $this->model::DELETED);
    }

    public function findProductByID($id)
    {
        return $this->model::query()->where('id', $id)->where('is_deleted', '<>', $this->model::DELETED)->first();
    }

    public function destroy($product)
    {
        try {
            $product->is_deleted = $this->model::DELETED;
            $product->updated_by = auth(MERCHANT)->id();
            $product->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Sản phẩm thành công!'));
        } catch (\Exception $e) {
            Log::error('[ProductRepository][destroy]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Xóa Sản phẩm không thành công!'));
        }
        return $this->response;
    }

    public function updateProduct($product, $request)
    {
        try {
            $product->name          = $request->name;
            $product->price_default = $request->price_default;
            $product->brief         = $request->brief;
            $product->updated_by    = auth(MERCHANT)->id();
            if (!empty($request->file)) {
                $date      = date('yy/m/d', time());
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/' . $date, $imageName);
                $image          = 'images/' . $date . '/' . $imageName;
                $product->image = $image;
            }
            $product->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Cập nhật Sản phẩm thành công!'));
        } catch (\Exception $e) {
            Log::error('[ProductRepository][updateProduct]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Cập nhật Sản phẩm không thành công!'));
        }
        return $this->response;
    }

    public function storeProduct($request)
    {
        try {
            $account = auth(MERCHANT)->user();
            $date      = date('yy/m/d', time());
            $imageName = '';
            if (!empty($request->file)) {
                $imageName = time() . '-' . $request->file->getClientOriginalName();
                $request->file->move(env('DIRECTORY_STORAGE') . 'images/' . $date, $imageName);
            }
            $model = $this->model::create([
                'name'          => $request->name,
                'price_default' => $request->price_default,
                'brief'         => $request->brief,
                'merchant_id'   => $account->getMerchantID(),
                'created_by'    => $account->id,
                'image'         => $imageName ? 'images/' . $date . '/' . $imageName : public_path('/images/noimage.jpg'),
            ]);
            $this->setData($model);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Tạo Sản phẩm thành công!'));
        } catch (\Exception $e) {
            Log::error('[ProductRepository][storeProduct]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo Sản phẩm không thành công!'));
        }
        return $this->response;
    }

    public function getTotalProducts()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted','<>',$this->model::DELETED);
    }
}
