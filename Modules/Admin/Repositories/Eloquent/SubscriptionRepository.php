<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Subscription;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\SubscriptionHistoryRepositoryInterface;
use App\Models\SubscriptionRequest;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\SubscriptionRepositoryInterface;
use Yajra\DataTables\DataTables;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{

    protected $merchantRepository;
    protected $subscriptionHistoryRepository;

    public function __construct(
        Subscription $model,
        MerchantRepositoryInterface $merchantRepository,
        SubscriptionHistoryRepositoryInterface $subscriptionHistoryRepository
    )
    {
        $this->merchantRepository = $merchantRepository;
        $this->subscriptionHistoryRepository = $subscriptionHistoryRepository;
        parent::__construct($model);
    }

    public function list($request)
    {
        $subscriptionHistories = $this->model::query()
            ->with(['machineSubscription', 'merchantSubscription'])
            ->get();

        $dateToExpire = config('admin.date_about_to_expire');

        return Datatables::of($subscriptionHistories)
            ->addColumn('merchant_info', function ($row) {
                if(empty($row->merchantSubscription)){
                    return '';
                }
                $urlHistory = route('admin.subscription.history', ['merchantId' => $row->merchant_id]);
                $content = $row->name . '
                                <div class="small">
                                    Số điện thoại: <a href="tel:' . $row->merchantSubscription->phone . '">' . $row->merchantSubscription->phone . '</a>
                                </div>
                                <div class="small">
                                    Email: <a href="mailto:' . $row->merchantSubscription->email . '">' . $row->merchantSubscription->email . '</a>
                                </div>
                                <div class="small mt-3">
                                    <a href="'.$urlHistory.'">[Xem lịch sử thuê bao]</a>
                                </div>';
                return $content;
            })
            ->addColumn('machine', function ($row){
                $content = $row->machineSubscription->name ?? '';
                return $content;
            })
            ->addColumn('created_at', function ($row){
                $content = '<span data-sort="'.strtotime($row->created_at).'" data-search="'.$row->created_at->format('d-m-Y').'">' .$row->created_at->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('date_expire', function ($row){
                $content = '<span data-sort="'.strtotime($row->date_expiration).'" data-search="'.$row->date_expiration->format('d-m-Y').'">' .$row->date_expiration->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('status', function ($row) use ($dateToExpire) {
                $content = '';
                $dateExpire = strtotime($row->date_expiration);
                $strTime = $dateExpire - time();
                if ($strTime > 0) {
                    if ($strTime > $dateToExpire * 24 * 60 * 60)
                        $content .= '<span class="badge badge-pill text-primary">Đang hoạt động</span>';
                    else
                        $content .= '<span class="badge badge-pill text-warning">Sắp hết hạn</span>';
                } else {
                    $content .= '<span class="badge badge-pill text-danger">Đã hết hạn</span>';
                }
                return $content;
            })
            ->addColumn('action', function ($row){
                $content = '<div class="box-sub"><a class="btn btn-primary" href="' . route('admin.subscription.edit', ['subscription' => $row->id]) . '">Sửa thông tin</a></div>';
                return $content;
            })
            ->rawColumns(['merchant_info', 'machine', 'created_at', 'date_expire', 'status', 'action'])
            ->make();

    }

    public function isEditMachine($machine)
    {
        if(empty($machine->merchant_id)){
            return true;
        }
        $result = $this->model->where('machine_id', $machine->id)
            ->where('date_expiration', '<=', time())
            ->count();
        return empty($result);
    }

    public function findBySubscriptionRequest($merchantId, $machineId)
    {
        $result = $this->model->where('merchant_id', $merchantId)
            ->where('machine_id', $machineId)
            ->first();
        return $result;
    }

    public function updateSubscription($obj, $request)
    {
        try {
            $dateExpireBegin = $obj->date_expiration->format('Y-m-d');
            $obj->date_expiration = convertDateFlatpickr($request->date_expire);
            $obj->updated_by = auth(ADMIN)->user()->id;
            $obj->checksum = $obj->createChecksum();
            $obj->save();

            $attributeSubscriptionHistory = [
                'merchant_id' => $obj->merchant_id,
                'machine_id' => $obj->machine_id,
                'date_expire_option' => $obj->date_expire_option,
                'request_month' => $obj->request_month,
                'machine_address' => $obj->machine->machine_address ?? ''
            ];

            $this->subscriptionHistoryRepository->createSubscriptionHistory($attributeSubscriptionHistory, $dateExpireBegin, $obj->date_expiration->format('Y-m-d'));

            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Update thông tin thuê bao thành công'));
        } catch (\Exception $e) {
            Log::error('[AdminSubscription][updateSubscription]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Update thông tin thuê bao thất bại'));
        }
        return $this->response;
    }

    public function createSubscription($attributeSubscription)
    {
        $attributes = [
            'merchant_id' => $attributeSubscription['merchant_id'],
            'machine_id' => $attributeSubscription['machine_id'],
            'created_by' => auth(ADMIN)->user()->id,
        ];

        if (!empty($attributeSubscription['date_expire_option'])) {
            $attributes['date_expiration'] = $attributeSubscription['date_expire_option'];
        } else {
            $attributes['date_expiration'] = date('Y-m-d', strtotime("+".$attributeSubscription['request_month']." month"));
        }
        $subscription = $this->create($attributes);
        $checksum = $subscription->createCheckSum();
        $subscription->update(['checksum' => $checksum]);

        $dateExpireBegin = date('Y-m-d H:i:s', time());
        $this->subscriptionHistoryRepository->createSubscriptionHistory($attributeSubscription, $dateExpireBegin, $subscription->date_expiration->format('Y-m-d'));

        return $subscription;
    }

    public function updateSubscriptionBySubscriptionRequest($subscription, $attributeSubscription)
    {
        $dateExpireBegin = $subscription->date_expiration->format('Y-m-d');

        if (!empty($attributeSubscription['date_expire_option'])) {
            $subscription->date_expiration = $attributeSubscription['date_expire_option'];
        } elseif (strtotime($subscription->date_expiration) >= strtotime(date('d/m/Y'))) {
            $subscription->date_expiration = date('Y-m-d', strtotime($subscription->date_expiration." +".$attributeSubscription['request_month'] ."month"));
        } else {
            $subscription->date_expiration = date('Y-m-d', strtotime("+".$attributeSubscription['request_month']." month"));
        }
        $subscription->updated_by = auth(ADMIN)->user()->id;
        $subscription->checksum = $subscription->createCheckSum();
        $subscription->save();

        $this->subscriptionHistoryRepository->createSubscriptionHistory($attributeSubscription, $dateExpireBegin, $subscription->date_expiration->format('Y-m-d'));

        return $subscription;
    }

    public function getSubscriptionAboutToExpire()
    {
        $timeExpire = date('Y-m-d', strtotime("+14 days"));
        $expire = date('Y-m-d');
        $result = $this->model::query()
            ->where('date_expiration', '<=', $timeExpire)
            ->where('date_expiration', '>', $expire)
            ->orderBy('date_expiration', 'ASC')
            ->get();

        return $result;
    }

    public function getSubscriptionExpireAndMachineNotBack(){
        $result = $this->model::query()
            ->where('date_expiration', '<', date('Y-m-d'))
            ->join('machine', 'machine.id', '=', 'subscription.machine_id')
            ->where('date_expiration', '<', date('Y-m-d'))
            ->where('subscription.merchant_id', '<>', 'machine.merchant_id')
            ->where('machine.merchant_id', '<>', 0)
            ->get()
            ->all();
        return $result;
    }

    public function expireSubscription($machineId, $merchantId)
    {
        $model = $this->findBySubscriptionRequest($merchantId, $machineId);
        if(empty($model)){
            return false;
        }
        $dateExpire = date("Y-m-d", strtotime("-1 day", time()));
        $attributeSubscription = [
            'merchant_id' => $merchantId,
            'machine_id' => $machineId
        ];
        $this->subscriptionHistoryRepository->createSubscriptionHistory($attributeSubscription, $model->date_expiration->format('Y-m-d'), $dateExpire);
        $model->date_expiration = $dateExpire;
        return $model->save();
    }
}
