<?php

namespace Modules\Merchant\Http\Requests;

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
        if ($data['pass_make'] == 1) {
            $require = 'required|min:8';
        }
        return [
            'name'        => 'required|min:5|max:255',
            'email'       => 'required|max:255|email|unique:merchant',
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
            'name.min'             => ':attribute yêu cầu độ dài > 5 ký tự',
            'name.max'             => ':attribute yêu cầu độ dài < 255 ký tự',
            'email.required'       => ':attribute là bắt buộc',
            'email.email'          => ':attribute không đúng định dạng',
            'email.unique'         => ':attribute phải là duy nhất',
            'email.max'            => ':attribute yêu cầu độ dài < 255 ký tự',
            'password.required'    => ':attribute là bắt buộc',
            'password.min'         => ':attribute yêu cầu độ dài > 8 ký tự',
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
