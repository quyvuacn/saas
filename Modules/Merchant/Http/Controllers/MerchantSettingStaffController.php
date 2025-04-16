<?php

namespace Modules\Merchant\Http\Controllers;

use App\Models\MerchantSettingStaff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Merchant\Classes\Facades\MerchantCan;
use Modules\Merchant\Http\Requests\MerchantStaffEditRequest;
use Modules\Merchant\Repositories\LogActionMerchantRepositoryInterface;
use Modules\Merchant\Repositories\MerchantSettingStaffInterface;

use App\Exports\MerchantStaffExport;
use App\Imports\MerchantStaffImport;
use Maatwebsite\Excel\Facades\Excel;

class MerchantSettingStaffController extends Controller
{
    protected $merchantSettingStaff;

    protected $logActionMerchantRepository;

    public function __construct(
        MerchantSettingStaffInterface $merchantSettingStaff,
        LogActionMerchantRepositoryInterface $logActionMerchantRepository
    ) {
        $this->middleware('auth:merchant');
        $this->merchantSettingStaff = $merchantSettingStaff;
        $this->logActionMerchantRepository = $logActionMerchantRepository;
    }

    public function list(Request $request)
    {
        if (!MerchantCan::do('user.list')) {
            return redirect()->route('merchant.dashboard')->with('error', __('Tài khoản không có quyền hạn này!'));
        }
        if ($request->ajax()) {
            return $this->merchantSettingStaff->list($request);
        }
        return view('merchant::staff.list');
    }

    public function destroy($staff, Request $request)
    {
        $staff = $this->merchantSettingStaff->findStaffByID($staff);
        if (!$staff) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Nhân viên không tồn tại!')];
        } else {
            if (!MerchantCan::do('user.change', $staff)) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn!')];
            } else {
                $response = $this->merchantSettingStaff->destroy($staff);

                if($response['status']) {
                    $attribute['content_request'] = [
                        'ID' => $staff->id
                    ];
                    $this->logActionMerchantRepository->createAction($request, $attribute);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function bulkDelete(Request $request)
    {
        $staff_ids = $request->list_id;
        $staffs    = $this->merchantSettingStaff->findStaffsByIDs($staff_ids)->pluck('id');
        if (empty($staffs)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Những nhân viên này không tồn tại!')];
        } else {
            if (!MerchantCan::do('user.edit')) {
                return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn!')];
            } else {
                $response = $this->merchantSettingStaff->bulkDelete($staffs);

                if($response['status']) {
                    $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
                }

                return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
            }
        }
    }

    public function editStaff($staff, Request $request)
    {
        $staff = $this->merchantSettingStaff->findStaffByID($staff);

        if (!$staff) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Nhân viên không tồn tại!')];
        }

        // Has Staff
        $validator = \Validator::make($request->all(), [
            'employee_email'      => 'required|email|max:99|unique:merchant_setting_staff,employee_email,' . $staff->id . ',id',
            'employee_department' => 'required|min:5|max:1000',
            'employee_quota'      => 'required|numeric|min:'.config('merchant.min_credit_quote').'|max:'.config('merchant.max_credit_quote'),
        ], [
            'employee_email.required'      => 'Email ' . __('là bắt buộc'),
            'employee_email.email'         => 'Email ' . __('không đúng định dạng'),
            'employee_email.max'           => 'Email ' . __('có chiều dài < 99 ký tự'),
            'employee_email.unique'        => 'Email ' . __('đã tồn tại'),
            'employee_department.required' => 'Đơn vị ' . __('là bắt buộc'),
            'employee_department.min'      => 'Đơn vị ' . __('phải có chiều dài > 5 ký tự'),
            'employee_department.max'      => 'Đơn vị ' . __('phải có chiều dài < 1000 ký tự'),
            'employee_quota.required'      => 'Hạn mức ' . __('là bắt buộc'),
            'employee_quota.numeric'       => 'Hạn mức ' . __('phải là số'),
            'employee_quota.min'           => 'Hạn mức ' . __('có giá trị nhỏ nhất là '.config('merchant.min_credit_quote')),
            'employee_quota.max'           => 'Hạn mức ' . __('có giá trị lớn nhất là '.config('merchant.max_credit_quote')),
        ]);

        // Validate Failed
        if ($validator->fails()) {
            $message = implode('<br> ', $validator->errors()->all());
            return response()->json(['status' => false, 'alert' => 'error', 'message' => $message]);
        }

        // Validate True, No Role
        if (!MerchantCan::do('user.change', $staff)) {
            return ['status' => false, 'alert' => 'error', 'message' => __('Tài khoản không có quyền hạn!')];
        }

        // Validate True, Has Role
        $response = $this->merchantSettingStaff->editStaff($staff, $request);

        if($response['status']) {
            $attribute['content_request'] = [
                'ID' => $staff->id,
                'Employee Quota' => $request->employee_quota
            ];
            $this->logActionMerchantRepository->createAction($request, $attribute);
        }

        return ['status' => $response['status'], 'alert' => $response['alert'], 'message' => $response['message']];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request)
    {
        $staffList = $this->merchantSettingStaff->findAll()->get();

        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);

        return Excel::download(new MerchantStaffExport($staffList), 'MerchantStaff.xlsx');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        $staffList = $this->merchantSettingStaff->findAll()->get()->pluck('employee_email');
        $file      = request()->file('file');
        $import    = new MerchantStaffImport($staffList);
        $import->import($file);
        $this->logActionMerchantRepository->createAction($request, ['content_request' => []]);
        return back()->with('message', 'Import Nhân viên thành công!');
    }
}
