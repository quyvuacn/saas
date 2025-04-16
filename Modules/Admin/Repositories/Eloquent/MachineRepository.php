<?php


namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Machine;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Repositories\MachineRepositoryInterface;
use Yajra\DataTables\DataTables;

class MachineRepository extends BaseRepository implements MachineRepositoryInterface
{
    public function __construct(Machine $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $admin = auth(ADMIN)->user();
        if(!empty($request->merchant_id))
            $machineRequest = $this->model->with('merchant')->where('merchant_id', $request->merchant_id);
        else
            $machineRequest = $this->model->with('merchant');
        return datatables()->eloquent($machineRequest)
            ->addColumn('name', function($row){
                $content = '<a href="'.route('admin.machine.edit', ['machine' => $row->id]).'">'.$row->name.'</a><br>'.$row->model;
                return $content;
            })
            ->addColumn('merchant_name', function($row){
                $content = !empty($row->merchant) ? $row->merchant->name : '';
                return $content;
            })
            ->addColumn('device_id', function($row) use ($admin){
                $content = "<p>{$row->device_id}</p>";
                $content .= $admin->can('adm.merchant.edit') ? '<span style="cursor: pointer" class="badge-primary badge change-machine-device" data-id="'.$row->id.'" data-deviceID="'.$row->device_id.'">Thay đổi Device ID</span>' : '';
                return $content;
            })
            ->addColumn('machine_system_info', function($row){
                $attr = !empty($row->machine_system_info) ? json_decode($row->machine_system_info, true) : [];
                $content = '<ul class="list-group list-group-flush p-0 m-0 text-center">';
                if (is_array($attr)) {
                    foreach ($attr as $v){
                        if (is_array($v) && isset($v['name']) && isset($v['value'])) {
                            $content .= '<li class="list-group-item">'.$v['name'].' : '.$v['value'].'</li>';
                        }
                    }
                }
                $content .= '</ul>';
                return $content;
            })
            ->addColumn('date_added', function($row){
                if(empty($row->date_added)){
                    return '<span data-sort="0" data-search=""></span>';
                }
                $content = '<span class="d-none">' . strtotime($row->date_added) . ' ' . $row->date_added->format('d-m-Y') . '</span>';
                $content .= '<span>' .$row->date_added->format('d/m/Y') . '</span>';
                return $content;
            })
            ->addColumn('machine_note', function($row){
                $content = $row->machine_note;
                return $content;
            })
            ->addColumn('status', function($row){
                switch ($row->status)
                {
                    case Machine::MACHINE_AVAIABLE:
                        $content = '<span class="badge badge-pill text-primary">Sẵn trong kho</span>';
                        break;
                    case Machine::MACHINE_WAS_GRANTED:
                        $content = '<span class="badge badge-pill text-success">Đã cho thuê</span>';
                        break;
                    default:
                        $content = '<span class="badge badge-pill">Vấn đề khác</span>';
                        break;
                }
                return $content;
            })
            ->addColumn('action', function($row){
                $content = '<div class="text-center">
                                    <a href="'.route('admin.machine.edit', ['machine' => $row->id]).'" class="btn btn-primary mb-2">
                                        <i class="fas fa-edit mr-1"></i>
                                        Sửa
                                    </a>
                                    <br>
                                    <button onclick="deleteItem(\''.route('admin.machine.delete', ['machine' => $row->id]).'\', [] , deleteMachine)" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        Xóa
                                    </button>
                                </div>';
                return $content;
            })
            ->rawColumns(['name', 'status', 'machine_system_info', 'action', 'date_added', 'device_id'])
            ->make();
    }

    public function findMachineActive()
    {
        $result = $this->model->where('status', $this->model::MACHINE_WAS_GRANTED)
            ->whereNotNull('merchant_id');
        return $result;
    }

    public function findMachineAvailiable()
    {
        $result = $this->model::query()
            ->where('status', $this->model::MACHINE_AVAIABLE)
            ->get();
        return $result;
    }

    public function updateMachine($machine, $request, $arrAttr)
    {
        if(!is_object($machine)){
            $machine = $this->find($machine);
        }
        if(empty($machine)){
            return false;
        }
        $machine->name = $request->name;
        $machine->model = $request->model;
        $machine->merchant_id = $request->merchant;
        $machine->machine_system_info = json_encode($arrAttr);
        $machine->status = !empty($request->merchant) ? $this->model::MACHINE_WAS_GRANTED : ($request->status_machine == 0 ? $this->model::MACHINE_AVAIABLE : $this->model::MACHINE_OTHER_PROBLEM);
        $machine->machine_note = $request->machine_note;
        $machine->updated_by = auth('admin')->user()->id;
        if(!empty($request->merchant) && !empty($request->date_added)){
            $machine->date_added = convertDateFlatpickr($request->date_added);
        }
        return $machine->save();
    }


    public function createMachine($request, $arrAttr)
    {
        $attributes = [
            'name' => $request->name,
            'model' => $request->model,
            'code' => uniqid() . md5($request->name),
            'number_tray' => $request->tray_count,
            'machine_system_info' => json_encode($arrAttr),
            'machine_note' => $request->machine_note,
            'status' => !empty($request->merchant) ? $this->model::MACHINE_WAS_GRANTED : ($request->status_machine == 0 ? $this->model::MACHINE_AVAIABLE : $this->model::MACHINE_OTHER_PROBLEM),
            'status_connecting' => $this->model::CONNECT_FAILED,
            'created_by' => auth('admin')->user()->id,
        ];
        if(!empty($request->merchant)){
            $attributes['merchant_id'] = $request->merchant;
            $attributes['date_added'] = convertDateFlatpickr($request->date_added);
        }
        return $this->create($attributes);
    }

    public function deleteMachine($machine)
    {
        $machine->is_deleted = $machine::IS_DELETED;
        $machine->updated_by = auth('admin')->user()->id;
        return $machine->save();
    }

    public function getMaxId()
    {
        $result = $this->model->select('id')
            ->orderBy('id', 'DESC')
            ->first();
        return $result->id ?? 0;
    }

    public function removeMerchant($machineId)
    {
        $machine = $this->find($machineId);
        if(!$machine){
            return false;
        }
        $machine->status = $machine::MACHINE_AVAIABLE;
        $machine->merchant_id = null;
        return $machine->save();
    }

    public function getTotalGroupByStatus()
    {
        $result = $this->model::query()
            ->select(DB::raw('count(id) as cid, status'))
            ->orderBy('status', 'ASC')
            ->groupBy('status')
            ->get()
            ->toArray();
        return $result;
    }

    public function getAllStatusName()
    {
        return $this->model::STATUS_NAME;
    }

    public function getStatusName($status)
    {
        $name = $this->getAllStatusName();
        return $name[$status] ?? '';
    }

    public function changeDeviceID($machine, $request){
        try {
            $machine->device_id = $request->device_id;
            $machine->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage('Thay đổi Device ID thành công!');
        } catch (\Exception $e) {
            Log::error('[Adm][MachineRepository][changeDevice]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage('Đã xảy ra lỗi, hoặc Tài khoản không có quyền hạn này!');
        }
        return $this->response;
    }

    public function getCountMachineByMerchantId($merchantId){
        $result = $this->model::query()
            ->where('merchant_id', $merchantId)
            ->count();
        return $result;
    }

    public function checkExitsDeviceId($deviceId)
    {
        $result = $this->model::query()
            ->where('device_id', $deviceId)
            ->exists();
        return $result;
    }
}
