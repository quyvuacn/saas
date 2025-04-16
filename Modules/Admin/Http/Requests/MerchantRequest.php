<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Merchant\Http\Controllers\ProductController;

class MerchantRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();
        if($data['merchant_audit'] != 1){
            $arrValidate = ['merchant_cancel_reason' => 'required|min:10'];
        } else {
            $arrValidate = [
                'machine_count' => 'required|integer|min:1|max:100',
                'merchant_active_date' => 'required',
                'merchant_other_request' => 'required',
            ];
        }
        return $arrValidate;
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
            'machine_count.required' => ':attribute là bắt buộc',
            'machine_count.integer' => ':attribute phải là số',
            'machine_count.min' => ':attribute phải lớn hơn 1',
            'machine_count.max' => ':attribute không vượt quá 100 máy',
            'merchant_active_date.required' => ':attribute là bắt buộc',
            'merchant_other_request.required' => ':attribute là bắt buộc',
            'merchant_cancel_reason.required' => ':attribute là bắt buộc',
            'merchant_cancel_reason.min' => ':attribute phải lớn hơn 10 ký tự'
        ];
    }

    public function attributes()
    {
        return [
            'machine_count' => 'Số lượng máy cần cung cấp ',
            'merchant_active_date' => 'Ngày bắt đầu thuê bao ',
            'merchant_other_request' => 'Yêu cầu khác ',
            'merchant_cancel_reason' => 'Lý do hủy yêu cầu'
        ];
    }
}
