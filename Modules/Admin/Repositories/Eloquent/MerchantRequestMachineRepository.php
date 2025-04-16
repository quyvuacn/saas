<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Merchant;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\MerchantRequestMachineRepositoryInterface;
use App\Models\MerchantRequestMachine;
use Yajra\DataTables\DataTables;

class MerchantRequestMachineRepository extends BaseRepository implements MerchantRequestMachineRepositoryInterface
{
    public function __construct(MerchantRequestMachine $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $machineRequest = $this->model->with('merchant')->get();
        return Datatables::of($machineRequest)
            ->addColumn('title', function ($row) {
                $content = $row->title;
                return $content;
            })
            ->addColumn('created_at', function ($row) {
                $content = date('d/m/Y H:i:s', strtotime($row->created_at));
                return $content;
            })
            ->addColumn('machine_request_count', function ($row) {
                $content = $row->machine_request_count;
                return $content;
            })
            ->addColumn('time_request', function ($row) {
                $content = '<div class="text-center">' . date('d/m/Y', strtotime($row->created_at)) . '<br/>' . date('d/m/Y', strtotime($row->machine_date_receive)) . '</div>';
                return $content;
            })
            ->addColumn('merchant_id', function ($row) {
                $content = (!empty($row->merchant->name)) ? $row->merchant->name : '';
                return $content;
            })
            ->addColumn('status', function ($row) {
                switch ($row->status) {
                    case MerchantRequestMachine::REQUEST_WAITING_AUDIT:
                        $content = '<span class="badge badge-dark">Chờ ký hợp đồng</span>';
                        break;
                    case MerchantRequestMachine::REQUEST_WAITING:
                        $content = '<span class="badge badge-warning">Đang chờ cài đặt</span>';
                        break;
                    case MerchantRequestMachine::REQUEST_SETUP_SUCCESS:
                        $content = '<span class="badge badge-info">Đang bàn giao máy</span>';
                        break;
                    case MerchantRequestMachine::REQUEST_SUCCESS:
                        $content = '<span class="badge badge-success">Đã bàn giao máy</span>';
                        break;
                    case MerchantRequestMachine::REQUEST_CANCEL:
                        $content = '<span class="badge badge-danger">Đã hủy</span>';
                        break;
                    default:
                        $content = '<span class="badge badge-primary">Yêu cầu mới</span>';
                        break;
                }
                $content = '<div class="text-center">' . $content . '</div>';
                return $content;
            })
            ->addColumn('action', function ($row) {
                switch ($row->status) {
                    case MerchantRequestMachine::REQUEST_NEW:
                        $content = '<a href="' . route('admin.machine.requestDetail', ['merchantRequest' => $row->id]) . '" class="btn btn-primary mb-2">Duyệt</a>';
                        break;
                    case MerchantRequestMachine::REQUEST_WAITING_AUDIT:
                        $content = '<a href="' . route('admin.machine.requestDetail', ['merchantRequest' => $row->id]) . '" class="btn btn-primary mb-2">Duyệt</a>';
                        break;
                    case MerchantRequestMachine::REQUEST_WAITING:
                        $content = '<a onclick="approveRequest(' . $row->id . ')" href="javascript:;" class="btn btn-success mb-2">Hoàn tất</a>';
                        break;
                    default:
                        $content = '';
                        break;
                }
                $content = '<div class="text-center">' . $content . '</div>';
                return $content;
            })
            ->rawColumns(['time_request', 'status', 'action'])
            ->make();
    }

    public function finalUpdateMerchantRequestMachine($obj)
    {
        $obj->status = $this->model::REQUEST_SETUP_SUCCESS;
        $obj->approved_by = auth('admin')->user()->id;
        $obj->approved_at = date('Y-m-d H:i:s', time());
        return $obj->save();
    }

    public function finalMerchantRequestMachineProcessing($obj)
    {
        $obj->status = $this->model::REQUEST_SUCCESS;
        $obj->approved_by = auth('admin')->user()->id;
        $obj->approved_at = date('Y-m-d H:i:s', time());
        return $obj->save();
    }

    public function updateMerchantRequestMachine($obj, $request)
    {
        $status = ($obj->status == $this->model::REQUEST_NEW) ? $this->model::REQUEST_WAITING_AUDIT : $this->model::REQUEST_WAITING;
        $status = ($request->merchant_audit == 1) ? $status : $this->model::REQUEST_CANCEL;

        $obj->approved_by = auth('admin')->user()->id;
        $obj->approved_at = date('Y-m-d H:i:s', time());
        $obj->status = $status;

        if ($request->merchant_audit == 1) {
            $obj->machine_request_count = $request->machine_request_count;
            $obj->machine_date_receive = convertDateFlatpickr($request->machine_date_receive);
            $obj->machine_other_request = $request->machine_other_request;
        } else {
            $obj->reason = $request->reason;
        }

        return $obj->save();
    }

    public function findRequest($requestId)
    {
        $result = $this->model->where('id', $requestId)->first();
        return $result;
    }

    public function getListRequestWaitingApprove()
    {
        $result = $this->model::query()
            ->select('merchant_request_machine.id as id', 'merchant_request_machine.title as request_content', 'merchant_request_machine.created_at as created_at', 'merchant_request_machine.machine_date_receive as date_success', 'merchant_request_machine.status as status', 'merchant.name as merchant_name', 'merchant_request_machine.machine_request_count as count')
            ->addSelect(DB::raw("'' as machine_name"), DB::raw("'merchant_request_machine' as type"))
            ->join('merchant', 'merchant.id', 'merchant_request_machine.merchant_id')
            ->where('merchant_request_machine.status', $this->model::REQUEST_SETUP_SUCCESS);
        return $result;
    }

    public function getListRequestNew()
    {
        $result = $this->model::query()
            ->select('merchant_request_machine.id as id', 'merchant_request_machine.title as request_content', 'merchant_request_machine.created_at as created_at')
            ->addSelect(DB::raw("'merchant_request_machine' as type"))
            ->join('merchant', 'merchant.id', 'merchant_request_machine.merchant_id')
            ->where('merchant_request_machine.status', $this->model::REQUEST_NEW);
        return $result;
    }

    public function getTotalRequet()
    {
        $result = $this->model::query()
            ->where('status', $this->model::REQUEST_NEW)
            ->count();
        return $result;
    }
}
