<?php

namespace Modules\Merchant\Repositories\Eloquent;

use App\Models\MerchantSettingStaff;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Repositories\MerchantSettingStaffInterface;
use Yajra\DataTables\DataTables;

class MerchantSettingStaffRepository extends BaseRepository implements MerchantSettingStaffInterface
{
    public function __construct(MerchantSettingStaff $model)
    {
        parent::__construct($model);
    }

    public function list($request)
    {
        $datatable = Datatables()->of($this->findAll()->get())
            ->addColumn('select', function($row){
                return '<div class="custom-control custom-checkbox small">
                            <input type="checkbox" class="custom-control-input staff_credit_select_class" name="staff_credit_select_'.$row->id.'" id="staff_credit_select_'.$row->id.'" value="'.$row->id.'">
                            <label class="custom-control-label" for="staff_credit_select_'.$row->id.'"></label>
                        </div>';
            })
            ->addColumn('employee_code', function($row){
                return sortSearchText($row->employee_code);
            })
            ->addColumn('employee_email', function($row){
                return sortSearchText($row->employee_email);
            })
            ->addColumn('employee_department', function($row){
                return $row->employee_department;
            })
            ->addColumn('credit_quota', function($row){
                return sortSearchCoin($row->employee_quota);
            });
        if (MerchantCan::do('user.edit')){
            $datatable->addColumn('action', function($row){
                return '<div class="text-center">
                                <span class="btn btn-primary btn-sm staff-credit-edit-btn" data-id="'.$row->id.'" data-email="'.$row->employee_email.'" data-quota="'.$row->employee_quota.'" data-department="'.$row->employee_department.'" data-toggle="modal" data-target="#staffEdit"><i class="fas fa-edit"></i> Sửa</span>
                                <span class="btn btn-danger btn-sm staff-credit-delete-btn" onclick="__deleteStaff('.$row->id.')" data-id="'.$row->id.'"><i class="fas fa-trash"></i> Xóa</span>
                            </div>';
            });
        }
        return $datatable->rawColumns(['select', 'employee_email', 'employee_code','credit_quota', 'action'])
            ->make();
    }

    public function findAll()
    {
        $merchant = auth(MERCHANT)->user();
        return $this->model::query()
            ->whereHas('merchant')
            ->with('merchant')
            ->where('is_deleted', '<>', $this->model::IS_DELETED)
            ->where('merchant_id', $merchant->getMerchantID());
    }

    public function findStaffByID($staff_id)
    {
        return $this->model::query()->where('id', $staff_id)->where('is_deleted', '<>', $this->model::IS_DELETED)->first();
    }

    public function findStaffsByIDs($staff_ids)
    {
        return $this->model::query()->whereIn('id', $staff_ids)->where('is_deleted', '<>', $this->model::IS_DELETED);
    }

    public function editStaff($staff, $request)
    {
        try {
            $staff->employee_email = $request->employee_email;
            $staff->employee_department = $request->employee_department;
            $staff->employee_quota = $request->employee_quota;
            $staff->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Cập nhật Nhân viên thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantSettingStaffRepository][editStaff]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn'));
        }
        return $this->response;
    }

    public function destroy($staff)
    {
        try {
            $staff->is_deleted = $this->model::IS_DELETED;
            $staff->save();
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Nhân viên thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantSettingStaffRepository][destroy]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn'));
        }
        return $this->response;
    }

    public function bulkDelete($staff_ids){
        try {
            $this->model::query()->whereIn('id', $staff_ids)->update(['is_deleted' => $this->model::IS_DELETED]);
            $this->setStatus(true);
            $this->setAlert('message');
            $this->setMessage(__('Xóa Nhân viên thành công!'));
        } catch (\Exception $e) {
            Log::error('[MerchantSettingStaffRepository][bulkDelete]--' . $e->getMessage());
            $this->setStatus(false);
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setAlert('error');
            $this->setMessage(__('Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn'));
        }
        return $this->response;
    }
}
