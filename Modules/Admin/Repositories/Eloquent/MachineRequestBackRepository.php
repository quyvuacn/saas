<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\MachineRequestBack;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Modules\Admin\Repositories\MachineRequestBackRepositoryInterface;
use Modules\Admin\Repositories\MerchantRepositoryInterface;
use Modules\Admin\Repositories\MerchantRequestMachineRepositoryInterface;
use Yajra\DataTables\DataTables;

class MachineRequestBackRepository extends BaseRepository implements MachineRequestBackRepositoryInterface
{
    protected $machineRepository;
    protected $merchantRequestMachineRepository;
    protected $merchantRepository;
    protected $subscriptionRepository;

    public function __construct(
        MachineRequestBack $model,
        MachineRepositoryInterface $machineRepository,
        MerchantRequestMachineRepositoryInterface $merchantRequestMachineRepository,
        MerchantRepositoryInterface $merchantRepository,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->machineRepository = $machineRepository;
        $this->merchantRequestMachineRepository = $merchantRequestMachineRepository;
        $this->merchantRepository = $merchantRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        parent::__construct($model);
    }

    public function list($request)
    {
        $machineRequest = $this->model->with(['machineInfo', 'merchantInfo'])->get();
        return Datatables::of($machineRequest)
            ->addColumn('machine_name', function ($row) {
                $content = $row->machineInfo->name ?? '';
                return $content;
            })
            ->addColumn('machine_model', function ($row) {
                $content = $row->machineInfo->model ?? '';
                return $content;
            })
            ->addColumn('address', function ($row) {
                $content = $row->address;
                return $content;
            })
            ->addColumn('created_at', function ($row) {
                return sortSearchDate($row->created_at);
            })
            ->addColumn('merchant', function ($row) {
                $content = !empty($row->merchantInfo->name) ? $row->merchantInfo->name : '';
                return $content;
            })
            ->addColumn('date_receive', function ($row) {
                if($row->date_return_machine) {
                    return sortSearchDate($row->date_return_machine);
                }
                return '---';
            })
            ->addColumn('status', function ($row) {
                switch ($row->status){
                    case $this->model::REQUEST_SUCCESS:
                        $content = '<span class="badge badge-success">Hoàn tất</span>';
                        break;
                    case $this->model::REQUEST_BACK_SUCCESS:
                        $content = '<span class="badge badge-warning">Đã xử lý</span>';
                        break;
                    case $this->model::REQUEST_WAITING_BACK:
                        $content = '<span class="badge badge-dark">Chờ thu hồi máy</span>';
                        break;
                    case $this->model::REQUEST_CANCEL:
                        $content = '<span class="badge badge-danger">Hủy yêu cầu</span>';
                        break;
                    default:
                        $content = '<span class="badge badge-primary">Yêu cầu mới</span>';
                        break;
                }
                return $content;
            })
            ->addColumn('action', function ($row) {
                $content = '';
                if ($row->status == MachineRequestBack::REQUEST_NEW) {
                    $content = '<a href="' . route('admin.machine.requestBackDetail', ['machineRequest' => $row->id]) . '" class="btn btn-primary">Xử lý yêu cầu</a>';
                    $content .= '<a href="javascript:void(0)" class="btn btn-danger ml-3" onclick="cancelRequestBackMachine('.$row->id.')">Hủy yêu cầu</a>';
                }
                if ($row->status == MachineRequestBack::REQUEST_WAITING_BACK) {
                    $content = '<a href="javascript:void(0);" onclick="finalApproveRequestBackMachine(' . $row->id . ')" class="btn btn-primary">Xử lý yêu cầu</a>';
                    $content .= '<a href="javascript:void(0)" class="btn btn-danger ml-3" onclick="cancelRequestBackMachine('.$row->id.')">Hủy yêu cầu</a>';
                }
                return $content;
            })
            ->rawColumns(['action', 'status', 'date_receive', 'created_at'])
            ->make();
    }

    public function listProcessing()
    {
        $queryMerchantRequestMachines = $this->merchantRequestMachineRepository->getListRequestWaitingApprove();
        $result = $this->model::query()
            ->select('machine_request_back.id as id', 'machine_request_back.request_content as request_content', 'machine_request_back.created_at as created_at', 'machine_request_back.date_receive as date_success', 'machine_request_back.status as status', 'merchant.name as merchant_name')
            ->addSelect(DB::raw("'0' as count"), 'machine.name as machine_name', DB::raw("'machine_request_back' as type"))
            ->where('machine_request_back.status', $this->model::REQUEST_BACK_SUCCESS)
            ->join('merchant', 'machine_request_back.merchant_id','merchant.id')
            ->join('machine', 'machine_request_back.machine_id','machine.id')
            ->union($queryMerchantRequestMachines)
            ->orderBy('created_at', 'DESC')
            ->get();
        return $result;
    }

    public function listNewRequestDashboard()
    {
        $queryMerchantRequestMachines = $this->merchantRequestMachineRepository->getListRequestNew();
        $result = $this->model::query()
            ->select('machine_request_back.id as id', 'machine_request_back.request_content as request_content', 'machine_request_back.created_at as created_at')
            ->addSelect(DB::raw("'machine_request_back' as type"))
            ->where('machine_request_back.status', $this->model::REQUEST_NEW)
            ->join('merchant', 'machine_request_back.merchant_id','merchant.id')
            ->union($queryMerchantRequestMachines)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();
        return $result;
    }

    public function approveRequestBack($machineRequest, $request)
    {
        if(!is_object($machineRequest) && is_integer($machineRequest)){
            $machineRequest = $this->model->find($machineRequest);
        }
        if(empty($machineRequest)){
            return false;
        }

        $machineRequest->reason = $request->reason;
        $machineRequest->date_receive = convertDateFlatpickr($request->date_receive);
        $machineRequest->status = $machineRequest::REQUEST_WAITING_BACK;
        $machineRequest->updated_by = auth('admin')->user()->id;
        return $machineRequest->save();
    }

    public function finalApproveRequestBack($machineRequest)
    {
        $machineRequest->status = $machineRequest::REQUEST_BACK_SUCCESS;
        $machineRequest->updated_by = auth('admin')->user()->id;
        return $machineRequest->save();
    }

    public function finalRequestBackProcessing($machineRequest)
    {
        $machineRequest->status = $machineRequest::REQUEST_SUCCESS;
        $machineRequest->updated_by = auth('admin')->user()->id;
        $machineRequest->save();

        $this->subscriptionRepository->expireSubscription($machineRequest->machine_id, $machineRequest->merchant_id);

        $this->machineRepository->removeMerchant($machineRequest->machine_id);

        $this->merchantRepository->updateMachineCount($machineRequest->merchant_id);
    }

    public function cancelRequestBack($machineRequest)
    {
        $machineRequest->status = $machineRequest::REQUEST_CANCEL;
        $machineRequest->updated_by = auth('admin')->user()->id;
        return $machineRequest->save();
    }

    public function getTotalNewRequest()
    {
        $result = $this->model::query()
            ->where('status', $this->model::REQUEST_NEW)
            ->count();
        return $result;
    }

}
