<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineRequestBackRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();
        return [
            'reason' => 'required',
            'date_receive' => 'required'
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
            'reason.required'                 => ':attribute là bắt buộc',
            'date_receive.required'                => ':attribute là bắt buộc',
        ];
    }

    public function attributes()
    {
        return [
            'reason'                  => 'Lý do trả máy ',
            'date_receive'                 => 'Ngày thu hồi máy ',
        ];
    }
}
