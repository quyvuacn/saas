<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRechargeSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            's' => 'required|min:3|max:255',
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
            's.required' => ':attribute là bắt buộc',
            's.min'      => ':attribute có chiều dài > 3 ký tự',
            's.max'      => ':attribute có chiều dài < 255 ký tự',
        ];
    }

    public function attributes()
    {
        return [
            's' => 'Từ khóa ',
        ];
    }
}
