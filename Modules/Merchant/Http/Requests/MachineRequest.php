<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'                 => 'required|min:5|max:255',
            'machine_request_count' => 'required',
            'machine_date_receive'  => 'required',
            'machine_position'      => 'required|min:5|max:255',
            'machine_other_request' => 'required|min:5|max:255',
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
            'title.required'                 => ':attribute là bắt buộc',
            'title.min'                      => ':attribute yêu cầu độ dài > 5 ký tự',
            'title.max'                      => ':attribute yêu cầu độ dài < 255 ký tự',
            'machine_request_count.required' => ':attribute là bắt buộc',
            'machine_date_receive.required'  => ':attribute là bắt buộc',
            'machine_position.required'      => ':attribute là bắt buộc',
            'machine_position.min'           => ':attribute yêu cầu độ dài > 5 ký tự',
            'machine_position.max'           => ':attribute yêu cầu độ dài < 255 ký tự',
            'machine_other_request.required' => ':attribute là bắt buộc',
            'machine_other_request.min'      => ':attribute yêu cầu độ dài > 5 kí tự',
            'machine_other_request.max'      => ':attribute yêu cầu độ dài < 255 kí tự',
        ];
    }

    public function attributes()
    {
        return [
            'title'                 => 'Nội dung yêu cầu ',
            'machine_request_count' => 'Số máy bán hàng bạn cần ',
            'machine_date_receive'  => 'Ngày nhận máy ',
            'machine_position'      => 'Nơi đặt máy ',
            'machine_other_request' => 'Yêu cầu khác ',
        ];
    }
}
