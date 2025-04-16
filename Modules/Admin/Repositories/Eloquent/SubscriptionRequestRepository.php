<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\SubscriptionRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\SubscriptionRequestRepositoryInterface;
use Yajra\DataTables\DataTables;

class SubscriptionRequestRepository extends BaseRepository implements SubscriptionRequestRepositoryInterface
{
    public function __construct(SubscriptionRequest $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $subscriptionRequest = $this->model::query()
            ->with(['merchant', 'machine'])->get();
        return Datatables::of($subscriptionRequest)
            ->addColumn('merchant', function ($row) {
                $content = !empty($row->merchant->name) ? $row->merchant->name : '';
                return $content;
            })
            ->addColumn('machine', function ($row) {
                $content = '';
                if(!empty($row->machine)){
                    $content = '<span>' . $row->machine->name . '</span>';
                    $content .= '<br/>';
                    $content .= '<span>' . $row->machine->model . '</span>';
                }
                return $content;
            })
            ->addColumn('request_content', function ($row) {
                $content = 'Thêm ' . $row->request_month . ' tháng sử dụng.<br><em class="small"> ' . $row->other_info . '</em>';
                return $content;
            })
            ->addColumn('created_at', function ($row) {
                $content = date('d/m/Y', strtotime($row->created_at));
                return $content;
            })
            ->addColumn('price', function ($row) {
                $content = number_format($row->request_price ?? 0) . ' <sup>đ</sup>';
                return $content;
            })
            ->addColumn('status', function ($row) {
                switch ($row->status) {
                    case $this->model::REQUEST_NEW:
                        $content = '<span class="badge badge-pill text-primary">Yêu cầu mới</span>';
                        break;
                    case $this->model::REQUEST_WAITING_CONTRACT:
                        $content = '<span class="badge badge-pill text-dark">Chờ ký hợp đồng</span>';
                        break;
                    case $this->model::REQUEST_WAITING_PAYMENT:
                        $content = '<span class="badge badge-pill text-warning">Chờ thanh toán</span>';
                        break;
                    case $this->model::REQUEST_SUCCESS:
                        $content = '<span class="badge badge-pill text-success">Đã hoàn tất</span>';
                        break;
                    default:
                        $content = '<span class="badge badge-pill text-danger">Đã hủy</span>';
                        break;
                }
                return $content;
            })
            ->addColumn('action', function ($row) {
                if ($row->status == $this->model::REQUEST_CANCEL || $row->status == $this->model::REQUEST_SUCCESS)
                    $content = '<a href="' . route('admin.subscription.viewRequest', ['subscriptionRequest' => $row->id]) . '" class="btn btn-info">Xem</a>';
                elseif ($row->status == $this->model::REQUEST_WAITING_PAYMENT)
                    $content = '<button onclick="approveSubscription(' . $row->id . ')" class="btn btn-success btn-approve">Hoàn tất</button>';
                else
                    $content = '<a href="' . route('admin.subscription.approve', ['subscriptionRequest' => $row->id]) . '" class="btn btn-primary">Duyệt</a>';
                return $content;
            })
            ->rawColumns(['request_content', 'machine', 'status', 'action', 'price'])
            ->make();
    }

    public function createSubscriptionRequest($request)
    {
        try {
            $attribute = [
                'merchant_id' => $request->merchant_id,
                'machine_id' => $request->machine_id,
                'request_price' => $request->request_price,
                'request_month' => $request->request_month,
                'date_expire_option' => $request->date_expire_option,
                'created_by' => auth(ADMIN)->user()->id
            ];
            $data = $this->create($attribute);
            $this->setStatus(true);
            $this->setData($data);
            $this->setAlert('message');
            $this->setMessage(__('Tạo yêu cầu gia hạn thuê bao thành công'));
        } catch (\Exception $e) {
            Log::error('[AdminSubscription][createRequestSubscription]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Tạo yêu cầu gia hạn thuê bao thất bại'));
        }
        return $this->response;
    }

    public function findSubscriptionApprove($subscriptionRequest)
    {
        $arrStatusAccept = [$this->model::REQUEST_NEW, $this->model::REQUEST_WAITING_CONTRACT];
        $result = $this->model->where('id', $subscriptionRequest)
            ->whereIn('status', $arrStatusAccept)
            ->first();
        return $result;
    }

    public function findSubscriptionFinalApprove($subscriptionRequest){
        $result = $this->model->where('id', $subscriptionRequest)
            ->where('status', $this->model::REQUEST_WAITING_PAYMENT)
            ->first();
        return $result;
    }

    public function finalUpdateStatusRequest($subscriptionRequest, $status)
    {
        $subscriptionRequest->status = $status;
        $subscriptionRequest->updated_by = auth(ADMIN)->user()->id;
        $subscriptionRequest->save();
        return $subscriptionRequest;
    }

    public function updateStatusRequestCancel($subscriptionRequest, $request)
    {
        $subscriptionRequest->status = $this->model::REQUEST_CANCEL;
        $subscriptionRequest->other_info = $request->other_info;
        return $subscriptionRequest->save();
    }

    public function approveSubscriptionRequest($subscriptionRequest, $request)
    {
        $subscriptionRequest->status = $subscriptionRequest->status == $this->model::REQUEST_NEW ? $this->model::REQUEST_WAITING_CONTRACT : $this->model::REQUEST_WAITING_PAYMENT;
        $subscriptionRequest->other_info = $request->other_info;
        $subscriptionRequest->date_expire_option = convertDateFlatpickr($request->date_expire_option);
        $subscriptionRequest->request_price = $request->request_price;
        return $subscriptionRequest->save();
    }

    public function getStatusRequest()
    {
        return [
            $this->getStatusRequestNew(),
            $this->getStatusRequestSuccess(),
            $this->getStatusRequestCancel()
        ];
    }

    public function getStatusRequestSuccess()
    {
        return $this->model::REQUEST_SUCCESS;
    }

    public function getStatusRequestCancel()
    {
        return $this->model::REQUEST_CANCEL;
    }

    public function getStatusRequestNew()
    {
        return $this->model::REQUEST_NEW;
    }

    public function getTotalRequest()
    {
        $result = $this->model::query()
            ->where('status', $this->model::REQUEST_NEW)
            ->count();
        return $result;
    }
}
