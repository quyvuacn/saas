<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequestMachineRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();
        if ($data['merchant_audit'] != 1) {
            $arrValidate = [
                'reason' => 'required|max:255',
            ];
        } else {
            $arrValidate = [
                'machine_request_count' => 'required|integer|min:1|max:100',
                'machine_date_receive'  => 'required',
                'machine_other_request' => 'required|min:5|max:255',
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
            'machine_request_count.required' => ':attribute là bắt buộc',
            'machine_request_count.min'      => ':attribute phải lớn hơn 1 máy',
            'machine_request_count.max'      => ':attribute phải nhỏ hơn 100 máy',
            'machine_date_receive.required'  => ':attribute là bắt buộc',
            'machine_other_request.required' => ':attribute là bắt buộc',
            'machine_other_request.min'      => ':attribute phải lớn hơn 5 kí tự',
            'machine_other_request.max'      => ':attribute phải ít hơn 255 kí tự',
            'reason.required'                => ':attribute là bắt buộc',
            'reason.max'                     => ':attribute phải ít hơn 255 kí tự',
        ];
    }

    public function attributes()
    {
        return [
            'machine_request_count' => 'Số lượng máy ',
            'machine_date_receive'  => 'Ngày nhận máy ',
            'machine_other_request' => 'Yêu cầu khác ',
            'reason'                => 'Lý do ',
        ];
    }
}
