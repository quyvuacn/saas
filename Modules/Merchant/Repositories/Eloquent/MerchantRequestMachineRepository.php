<?php


namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\MerchantRequestMachine;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Repositories\MerchantRequestMachineRepositoryInterface;
use Yajra\DataTables\DataTables;

class MerchantRequestMachineRepository extends BaseRepository implements MerchantRequestMachineRepositoryInterface
{
    public function __construct(MerchantRequestMachine $model)
    {
        parent::__construct($model);
    }

    public function requestHistory($request)
    {
        $merchant = auth(MERCHANT)->user();
        $requestHistories = $this->merchantMachines($merchant->getMerchantID(), false)->get();
        $datatables = Datatables::of($requestHistories)
            ->addColumn('content', function ($row) {
                return $row->title;
            })->addColumn('machine_count', function ($row) {
                return sortSearchText($row->machine_request_count);
            })->addColumn('request_date', function ($row) {
                return sortSearchDate($row->created_at);
            })->addColumn('request_receive', function ($row) {
                if ($row->machine_date_receive) {
                    return sortSearchDate($row->machine_date_receive);
                }
                return '---';
            })->addColumn('request_position', function ($row) {
                return $row->machine_position;
            })->addColumn('request_other', function ($row) {
                return $row->machine_other_request;
            })->addColumn('request_status', function ($row) {
                switch ($row->status) {
                    case $this->model::REQUEST_WAITING_AUDIT:
                        $content = '<span class="badge badge-dark">Chờ ký hợp đồng</span>';
                        break;
                    case $this->model::REQUEST_WAITING:
                        $content = '<span class="badge badge-warning">Đang chờ cài đặt</span>';
                        break;
                    case $this->model::REQUEST_SETUP_SUCCESS:
                        $content = '<span class="badge badge-info">Cài đặt hoàn tất</span>';
                        break;
                    case $this->model::REQUEST_SUCCESS:
                        $content = '<span class="badge badge-success">Hoàn tất</span>';
                        break;
                    case $this->model::REQUEST_CANCEL:
                        $content = '<span class="badge badge-danger">Đã hủy</span>';
                        break;
                    default:
                        $content = '<span class="badge badge-primary">Yêu cầu mới</span>';
                        break;
                }
                $content = '<div class="text-center">' . $content . '</div>';
                return $content;
            });
        return $datatables->rawColumns(['request_status', 'request_date' , 'request_receive', 'machine_count'])
            ->make(true);
    }


    public function requestMachine($request)
    {
        try {
            $account = auth(MERCHANT)->user();
            $model = $this->model::create([
                'title'                 => $request->title,
                'machine_request_count' => $request->machine_request_count,
                'machine_date_receive'  => Carbon::createFromFormat('d/m/Y', $request->machine_date_receive),
                'machine_position'      => $request->machine_position,
                'machine_other_request' => $request->machine_other_request,
                'created_by'            => $account->id,
                'merchant_id'           => $account->getMerchantID(),
            ]);
            $this->setData($model->id);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Yêu cầu cung cấp máy bán hàng thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantRequestMachineRepository][requestMachine]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Yêu cầu cung cấp máy bán hàng không thành công!');
        }
        return $this->response;
    }

    public function deleteRequest($request)
    {
        try {
            $request->is_deleted = $this->model::DELETED;
            $request->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Xóa Yêu cầu thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantRequestMachineRepository][deleteRequest]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Xóa Yêu cầu không thành công!');
        }
        return $this->response;
    }

    public function updateRequest($request, $requestChanges)
    {
        try {
            $request->title                 = $requestChanges->title;
            $request->machine_request_count = $requestChanges->machine_request_count;
            $request->machine_date_receive  = Carbon::createFromFormat('d/m/Y', $requestChanges->machine_date_receive);
            $request->machine_position      = $requestChanges->machine_position;
            $request->machine_other_request = $requestChanges->machine_other_request;
            $request->updated_by            = auth(MERCHANT)->id();
            $request->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Sửa yêu cầu thành công!');
        } catch (\Exception $e) {
            Log::error('[MerchantRequestMachineRepository][updateRequest]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Sửa yêu cầu không thành công!');
        }
        return $this->response;
    }

    public function merchantMachines($merchant_id, $new = true)
    {
        $query = $this->model::query()
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->where('merchant_id', $merchant_id)
            ->orderBy('created_at', 'DESC');
        if ($new) {
            $query = $query->where('status', $this->model::REQUEST_NEW);
        } else {
            $query = $query->where(function ($q) {
                $q->orWhere('status', $this->model::REQUEST_WAITING_AUDIT);
                $q->orWhere('status', $this->model::REQUEST_WAITING);
                $q->orWhere('status', $this->model::REQUEST_CANCEL);
                $q->orWhere('status', $this->model::REQUEST_SETUP_SUCCESS);
                $q->orWhere('status', $this->model::REQUEST_SUCCESS);
            });
        }
        return $query;
    }

    public function findNewRequestByID($request_id)
    {
        return $this->model::query()
            ->where('id', $request_id)
            ->where('status', $this->model::REQUEST_NEW)
            ->where('is_deleted', '<>', $this->model::DELETED)
            ->first();
    }
}
