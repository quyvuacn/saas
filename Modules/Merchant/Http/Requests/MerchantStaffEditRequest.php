<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStaffEditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_email'      => 'required|email|max:99|unique:merchant_setting_staff',
            'employee_department' => 'required|min:5|max:255',
            'employee_quota'      => 'required|numeric|min:'.config('merchant.min_credit_quote').'|max:'.config('merchant.max_credit_quote'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'employee_email.required'      => ':attribute ' . __('là bắt buộc'),
            'employee_email.email'         => ':attribute ' . __('không đúng định dạng'),
            'employee_email.max'           => ':attribute ' . __('có chiều dài < 99 ký tự'),
            'employee_email.unique'        => ':attribute ' . __('đã tồn tại'),
            'employee_department.required' => ':attribute ' . __('là bắt buộc'),
            'employee_department.min'      => ':attribute ' . __('có chiều dài > 5 ký tự'),
            'employee_department.max'      => ':attribute ' . __('có chiều dài < 255 ký tự'),
            'employee_quota.required'      => ':attribute ' . __('là bắt buộc'),
            'employee_quota.numeric'       => ':attribute ' . __('phải là số'),
            'employee_quota.min'           => ':attribute ' . __('có giá trị nhỏ nhất là '.config('merchant.min_credit_quote')),
            'employee_quota.max'           => ':attribute ' . __('có giá trị lớn nhất là '.config('merchant.max_credit_quote')),

        ];
    }

    public function attributes()
    {
        return [
            'employee_email'      => 'Email ',
            'employee_department' => 'Đơn vị ',
            'employee_quota'      => 'Hạn mức ',
        ];
    }
}
