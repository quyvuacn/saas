<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data    = $this->request->all();
        $require = '';
        if (!isset($data['pass_make'])) {
            $require = 'required|min:8|max:255';
        }
        return [
            'name'        => 'required|min:10|max:50',
            'email'       => 'required|email|unique:admin|max:255',
            'permissions' => 'required',
            'password'    => $require,
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
            'name.required'        => ':attribute là bắt buộc',
            'name.min'             => ':attribute yêu cầu độ dài > 10 ký tự',
            'name.max'             => ':attribute yêu cầu độ dài < 50 ký tự',
            'email.required'       => ':attribute là bắt buộc',
            'email.email'          => ':attribute không đúng định dạng',
            'email.unique'         => ':attribute phải là duy nhất',
            'email.max'            => ':attribute yêu cầu độ dài < 255 ký tự',
            'password.required'    => ':attribute là bắt buộc',
            'password.min'         => ':attribute yêu cầu độ dài > 8 ký tự',
            'password.max'         => ':attribute yêu cầu độ dài < 255 ký tự',
            'permissions.required' => ':attribute là bắt buộc',
        ];
    }

    public function attributes()
    {
        return [
            'name'        => 'Tên tài khoản ',
            'email'       => 'Email ',
            'password'    => 'Mật khẩu ',
            'permissions' => 'Quyền ',
        ];
    }
}
