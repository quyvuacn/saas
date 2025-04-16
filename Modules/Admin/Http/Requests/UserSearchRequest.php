<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|min:3|max:255',
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
            'email.required' => ':attribute là bắt buộc',
            'email.email'    => ':attribute không đúng định dạng',
            'email.max'      => ':attribute yêu cầu độ dài < 255 ký tự',
            'email.min'      => ':attribute yêu cầu độ dài > 3 ký tự',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'Email ',
        ];
    }
}
