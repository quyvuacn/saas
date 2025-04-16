<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Machine;
use App\Models\MachineRequestBack;
use App\Models\ProductSaleHistory;
use App\Models\UserHistoryPayment;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\MachineRepositoryInterface;
use Modules\Merchant\Repositories\UserHistoryPaymentRepositoryInterface;
use Yajra\DataTables\DataTables;

class MachineRepository extends BaseRepository implements MachineRepositoryInterface
{
    protected $productSaleHistory;
    protected $userHistoryPayment;
    protected $machineRequestBack;
    public function __construct(Machine $model,
        ProductSaleHistory $productSaleHistory,
        MachineRequestBack $machineRequestBack,
        UserHistoryPayment $userHistoryPayment
    )
    {
        parent::__construct($model);
        $this->productSaleHistory = $productSaleHistory;
        $this->machineRequestBack = $machineRequestBack;
        $this->userHistoryPayment = $userHistoryPayment;
    }

    public function list($request)
    {
        $merchant = auth(MERCHANT)->user();
        $machines = $this->findMachinesOfMerchant($merchant->getMerchantID());
        $datatables = datatables()->eloquent($machines)
            ->addColumn('model', function($row){
                $model = '<a>'.ucfirst($row->name).'</a><p class="small">'.$row->model.'</p>';
                return $model;
            })
            ->addColumn('position', function($row) use ($merchant){
                $position = '<div>'.$row->machine_address.'</div>';
                $change = $merchant->can('machine.edit') ? '<span style="cursor: pointer" class="badge-primary badge change-machine-addr" data-id="'.$row->id.'" data-address="'.$row->machine_address.'">Thay đổi chỗ đặt</span>' : '';
                return $position.$change;
            })
            ->addColumn('spec', function ($row) {
                $attr    = !empty($row->machine_system_info) ? json_decode($row->machine_system_info, true) : [];
                $content = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                if (is_array($attr)) {
                    foreach ($attr as $v) {
                        $content .= '<li class="list-group-item">' . $v['name'] . ' : ' . $v['value'] . '</li>';
                    }
                }
                $content .= '</ul>';
                return $content;
            })
            ->addColumn('start_date', function($row){
                if ($row->date_added) {
                    return sortSearchDate($row->date_added);
                }
                return '---';
            })
            ->addColumn('expire_subscription', function($row){
                $expire_subscription = $row->subscription  ? $row->subscription->date_expiration->format('d/m/Y') : '---';
                // <div class="small"><em>(Còn lại 15 ngày)</em></div>
                return $expire_subscription;
            });
        if ($merchant->can('machine.edit')) {
            $datatables->addColumn('action', function($row){
                $disabled = '';
                if ($row->newRequestBack) {
                    $disabled = 'disabled';
                }
                $action = '<button class="btn btn-danger btn-sm stop-extend" '.$disabled.' data-id='.$row->id.'><i class="fas fa-stop"></i> Ngừng gia hạn thuê bao</span>';
                return $action;
            });
        }
        return $datatables->rawColumns(['spec','model','position','action', 'start_date'])
            ->make(true);
    }

    public function history($request)
    {
        $merchant = auth(MERCHANT)->user();
        $code   = $request->code;
        $from = $request->from;
        $to = $request->to;
        $historySales = $this->userHistoryPayment::query()->with(['machine','user', 'user.staff', 'machine.merchant'])
            ->whereHas('machine.merchant', function ($q) use ($merchant){
                $q->where('id', $merchant->getMerchantID());
            })
            ->whereHas('user')
            ->where('transaction_type', '>', 0)
            ->where(function($query) {
                $query->orWhere('status', $this->userHistoryPayment::STATUS_PROCESSING)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_SUCCESS)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_NEW)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_ERROR);
            });
        if ($code){
            $historySales = $historySales->whereHas('machine', function ($q) use ($code) {
                $q->where('model', 'like', '%' . likeEscape($code) . '%');
            });
        }
        if ($from){
            $historySales = $historySales->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $from)->toDateTime());
        }
        if ($to){
            $historySales = $historySales->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $to)->toDateTime());
        }
        $historySales = $historySales->get();
        return Datatables::of($historySales)
            ->addColumn('code', function($row){
                return $row->transaction_id ?? '---';
            })
            ->addColumn('machine_model', function($row){
                return ($row->machine->model ?? '---') . ' / ' . ($row->machine->name ?? '---');
            })
            ->addColumn('machine_address', function($row){
                return $row->machine->machine_address ?? '---';
            })
            ->addColumn('product_name', function($row){
                $products = json_decode($row->products);
                return isset($products[0]->name) ? $products[0]->quantity . ' x ' . $products[0]->name : '---';
            })
            ->addColumn('price', function($row){
                $products = json_decode($row->products);
                return '<span class="d-none">' . ($products[0]->price ?? 0) . '</span><span>' . $products[0]->quantity . ' x ' . number_format($products[0]->price ?? 0) . ' <sup>đ</sup></span>';
            })
            ->addColumn('sale_time', function($row){
                return sortSearchDate($row->created_at, true);
            })
            ->addColumn('user', function($row){
                return sortSearchText($row->user->email ?? '---');
            })
            ->addColumn('status', function($row){
                $content = '';
                switch ($row->status) {
                    case 'NEW':
                        $content = '<span class="badge-primary badge">Mới</span>';
                        break;
                    case 'SUCCESS':
                        $content = '<span class="badge-success badge">Thành công</span>';
                        break;
                    case 'PROCESSING':
                        $content = '<span class="badge-info badge">Đang xử lý</span>';
                        break;
                    case 'ERROR':
                        $content = '<span class="badge-danger badge">Lỗi</span>';
                        break;
                }
                return $content;
            })
            ->rawColumns(['status','price', 'sale_time', 'user'])
            ->make(true);
    }

    public function sync()
    {
        $merchant = auth(MERCHANT)->user();
        $machines = $this->findMachinesOfMerchant($merchant->getMerchantID())->get();
        $datatables = Datatables::of($machines)
            ->addColumn('name', function($row){
                $name = '<p>'.$row->name.'</p><p class="small">'.$row->model.'</p>';
                return $name;
            })
            ->addColumn('attribute', function ($row) {
                $attribute = json_decode(json_encode($row), true);
                $text      = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                foreach ($attribute['attribute_values'] as $attr) {
                    if (empty($attr['attribute'])) {
                        continue;
                    }
                    $text .= '<li class="list-group-item">' . $attr['attribute']['attribute_name'] . ' : ' . $attr['attribute_value'] . '
                                       </li>';
                }
                $text .= '</ul>';
                return $text;
            })
            ->addColumn('start_date', function($row){
                return sortSearchDate($row->date_added);
            });
            if (MerchantCan::do('product.sync.edit')) {
                $datatables = $datatables->addColumn('action', function ($row) {
                    $action = '<div class="text-center"><a href="' . route('merchant.product.syncDetail', ['machine' => $row->id]) . '" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Sửa thông tin</a></div>';
                    return $action;
                });
            }
            return $datatables->rawColumns(['name', 'attribute', 'action', 'start_date'])
            ->make(true);
    }

    public function requestBack($machine, $request)
    {
        try {
            $account = auth(MERCHANT)->user();
            $model = $this->machineRequestBack::create([
                'machine_id'          => $machine->id,
                'address'             => $machine->machine_address,
                'request_content'     => $request->request_content,
                'date_return_machine' => Carbon::createFromFormat('d/m/Y', $request->date_return_machine)->toDateTime(),
                'created_by'          => $account->id,
                'merchant_id'         => $account->getMerchantID(),
            ]);
            $this->setData($model->id);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Yêu cầu ngừng gia hạn thuê bao thành công!'));
        } catch (\Exception $e) {
            Log::error('[MachineRepository][requestBack]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi, hoặc Tài khoản không có quyền hạn này!'));
        }
        return $this->response;
    }

    /***
     * @param $machine
     * @param $request
     * @return mixed
     */
    public function changeAddress($machine, $request)
    {
        try {
            $machine->machine_address = $request->address;
            $machine->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Thay đổi địa chỉ thành công!');
        } catch (\Exception $e) {
            Log::error('[MachineRepository][changeAddress]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Đã xảy ra lỗi, hoặc Tài khoản không có quyền hạn này!');
        }
        return $this->response;
    }

    public function findMachineOfMerchantByID($machine_id, $merchant_id)
    {
        return $this->model::query()
            ->where('id', $machine_id)
            ->with(['merchant', 'newRequestBack'])
            ->whereHas('merchant', function ($q) use ($merchant_id) {
                $q->where('merchant_id', $merchant_id);
            })
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('status', '<>', $this->model::IS_INACTIVATED)
            ->first();
    }

    public function findAllHistories()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->userHistoryPayment::query()->with(['machine','user', 'user.staff', 'machine.merchant'])
            ->whereHas('machine.merchant', function ($q) use ($merchant){
                $q->where('id', $merchant->getMerchantID());
            })
            ->whereHas('user')
            ->where('transaction_type', '>', 0)
            ->where(function($query) {
                $query->orWhere('status', $this->userHistoryPayment::STATUS_PROCESSING)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_SUCCESS)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_NEW)
                    ->orWhere('status', $this->userHistoryPayment::STATUS_ERROR);
            })->orderBy('created_at', 'DESC')->get();
    }

    public function findMachinesOfMerchant($merchant_id)
    {
        return $this->model::query()
            ->where('is_deleted','<>', $this->model::IS_DELETED)
            ->where('status','!=', $this->model::IS_INACTIVATED)
            ->with(['attributeValues', 'attributeValues.attribute', 'subscription', 'newRequestBack'])
            ->whereHas('merchant',function ($q) use ($merchant_id) {
                $q->where('merchant_id', $merchant_id);
            });
    }

    public function listActiveMachines()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->with(['merchant'])
            ->whereHas('merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('status','!=', $this->model::IS_INACTIVATED);
    }

    public function getProductOnMachines()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->with(['merchant', 'productLists'])
            ->whereHas('merchant', function ($q) use ($merchant) {
                $q->where('id', $merchant->getMerchantID());
            })
            ->where('merchant_id', $merchant->getMerchantID())
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('status','!=', $this->model::IS_INACTIVATED);
    }
}
