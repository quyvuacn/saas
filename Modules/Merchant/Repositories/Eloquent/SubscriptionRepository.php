<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\SubscriptionRequest;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\SubscriptionRepositoryInterface;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Yajra\DataTables\DataTables;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    protected $subscriptionHistory;
    protected $subscriptionRequest;

    public function __construct(Subscription $model, SubscriptionHistory $subscriptionHistory, SubscriptionRequest $subscriptionRequest)
    {
        parent::__construct($model);
        $this->subscriptionHistory = $subscriptionHistory;
        $this->subscriptionRequest = $subscriptionRequest;
    }

    public function list($request)
    {
        $subscriptions = $this->findSubscriptionsOfMerchant()->get();
        $dateToExpire = config('admin.date_about_to_expire');
        $datatables = DataTables::of($subscriptions)
            ->addColumn('machine_model', function($row){
                $name_model = ucfirst($row->machine->name ?? '---');
                $name_model .= ' <br>Model: '.$row->machine->model ?? '---';
                return $name_model;
            })
            ->addColumn('machine_address', function($row){
                return $row->machine->machine_address ?? '---';
            })
            ->addColumn('spec', function($row){
                $attr    = !empty($row->machine && $row->machine->machine_system_info) ? json_decode($row->machine->machine_system_info, true) : [];
                $content = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                if (is_array($attr)) {
                    foreach ($attr as $v) {
                        if (is_array($v) && isset($v['name']) && isset($v['value'])) {
                            $content .= '<li class="list-group-item">' . $v['name'] . ' : ' . $v['value'] . '</li>';
                        }
                    }
                }
                $content .= '</ul>';
                return $content;
            })
            ->addColumn('created_at', function($row){
                if (isset($row->machine->date_added)) {
                    return sortSearchDate($row->machine->date_added);
                }
                return '---';
            })
            ->addColumn('date_expiration', function($row){
                if ($row->date_expiration) {
                    return sortSearchDate($row->date_expiration);
                }
                return '---';
            })
            ->addColumn('expire_status', function($row) use($dateToExpire){
                $content = '';
                $dateExpire = strtotime($row->date_expiration);
                $strTime = $dateExpire - time();
                if ($strTime > 0) {
                    if ($strTime > $dateToExpire * 24 * 60 * 60)
                        $content .= '<p class="text-center"><span class="badge badge-pill text-primary">Đang hoạt động</span></p>';
                    else
                        $content .= '<p class="text-center"><span class="badge badge-pill text-warning">Sắp hết hạn</span></p>';
                } else {
                    $content .= '<p class="text-center"><span class="badge badge-pill text-danger">Đã hết hạn</span></p>';
                }
                if ($row->machine && $row->machine->newSubscriptionRequest){
                    $content.= '<p class="text-center"><span class="badge-info badge" data-sort="1">Chờ gia hạn</span></p>';
                }
                return $content;
            });
        if (MerchantCan::do('subscription.edit')) {
            $datatables->addColumn('action', function ($row) {
                $disabled = '';
                if ($row->machine && $row->machine->newSubscriptionRequest) {
                    $disabled = 'disabled';
                }
                $action = '<div class="text-center">
                            <p class="text-center border-bottom ml-1 mr-1 pb-3">
                            <button class="btn btn-outline-danger extend-6" '.$disabled.'  data-id="' . $row->id . '" data-month="' . config('merchant.subscription_extend.month.6') . '" style="width: 160px">' . config('merchant.subscription_extend.month.6') . ' tháng <br>' . number_format(config('merchant.subscription_extend.price.6')) . ' <sup>đ</sup></button>
                            </p>
                            <p class="text-center ml-1 mr-1 mb-0">
                            <button class="btn btn-danger extend-12" '.$disabled.'  data-id="' . $row->id . '" data-month="' . config('merchant.subscription_extend.month.12') . '" style="width: 160px">' . config('merchant.subscription_extend.month.12') . ' tháng (+' . config('merchant.subscription_extend.extra.12') . ' tháng) <br><strong class="text-white">' . number_format(config('merchant.subscription_extend.price.12')) . ' <sup>đ</sup></strong></button>
                            </p>
                        </div>';
                return $action;
            });
        }
        return $datatables->rawColumns(['machine_model', 'spec', 'action', 'expire_status', 'created_at', 'date_expiration'])->make(true);
    }

    public function history($request)
    {
        $merchant = auth(MERCHANT)->user();
        $from                  = $request->from;
        $to                    = $request->to;
        $subscriptionHistories = $this->subscriptionHistory::query()
            ->with(['merchant', 'machine', 'subscriptionRequest'])
            ->whereHas('machine')
            ->whereHas('merchant')
            ->where('merchant_id', $merchant->getMerchantID());
        if ($from) {
            $subscriptionHistories = $subscriptionHistories->whereDate('created_at', '>=',  Carbon::createFromFormat('d/m/Y', $from)->toDateTime());
        }
        if ($to) {
            $subscriptionHistories = $subscriptionHistories->whereDate('created_at', '<=',  Carbon::createFromFormat('d/m/Y', $to)->toDateTime());
        }
        $subscriptionHistories = $subscriptionHistories->get();
        return DataTables::of($subscriptionHistories)->addColumn('code', function ($row) {
                return isset($code) ? $code : $row->code;
            })->addColumn('machine_model', function ($row) {
                return $row->machine_number && $row->machine_number == 1 ? ($row->machine ? 'Máy bán hàng ' . $row->machine->name . '<br>Model: ' . $row->machine->model : '---') : $row->code . ' (' . $row->machine_number . ' máy bán hàng)';
            })->addColumn('machine_address', function ($row) {
                return $row->machine->machine_address ?? '---';
            })->addColumn('date_expiration_begin', function ($row) {
                if ($row->date_expiration_begin) {
                    return sortSearchDate($row->date_expiration_begin);
                }
                return '---';
            })->addColumn('request_price', function ($row) {
                return sortSearchPrice($row->subscriptionRequest->request_price ?? 'Cài đặt ban đầu', $row->subscriptionRequest && $row->subscriptionRequest->request_price ? false : true);
            })->addColumn('date_expiration_end', function ($row) {
                if ($row->date_expiration_end) {
                    return sortSearchDate($row->date_expiration_end);
                }
                return '---';
            })->addColumn('created_at', function ($row) {
                if ($row->created_at) {
                    return sortSearchDate($row->created_at);
                }
                return '---';
            })->addColumn('status', function ($row) {
                $content = '';
                if(empty($row->subscription_request_id)){
                    return '<span class="badge-info badge">Thêm trực tiếp</span>';
                }
                switch ($row->status) {
                    case 0:
                        $content = '<span class="badge-danger badge">Gia hạn thất bại</span>';
                        break;
                    case 1:
                        $content = '<span class="badge-success badge">Gia hạn thành công</span>';
                        break;
                }
                return $content;
            })->rawColumns(['machine_model', 'date_expiration_begin', 'request_price', 'date_expiration_end', 'status', 'machine_model', 'request_price', 'created_at'])->make(true);
    }

    public function findSubscriptionsOfMerchant()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->with(['merchant','machine', 'machine.newSubscriptionRequest'])
            ->where('subscription.merchant_id', $merchant->getMerchantID())
            ->whereHas('machine'); // Only exist machine
    }

    public function findSubscriptionByID($id)
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()->where('id', $id)->with(['machine.newSubscriptionRequest'])
            ->where('merchant_id', $merchant->getMerchantID())
            ->first();
    }

    public function extend($subscription, $request)
    {
        try {
            $subscriptionRequest = $this->subscriptionRequest::create([
                'merchant_id'    => $subscription->merchant_id,
                'machine_id'     => $subscription->machine_id,
                'request_month'  => $request->month,
                'request_price'  => config('merchant.subscription_extend.price.' . $request->month),
                'payment_method' => 1,
                'created_by'     => auth(MERCHANT)->id(),
            ]);
            $this->setData($subscriptionRequest->id);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Yêu cầu gia hạn thành công!');
        } catch (\Exception $e) {
            Log::error('[SubscriptionRepository][extend]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Yêu cầu gia hạn không thành công!');
        }
        return $this->response;
    }

    public function subscriptionRequestCount()
    {

    }
}
