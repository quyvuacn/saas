<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AccountProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $account              = auth(MERCHANT)->user();
        $data                 = $this->request->all();
        $require_pass         = '';
        $require_pass_confirm = '';
        $require_email        = '';
        if ($account->isSuperAdmin()) {
            $require_email = 'required|email|max:255|unique:merchant,email,' . $account->id;
        }
        if ($data['new_password'] !== null || $data['password_confirmation'] !== null) {
            $require_pass = [
                'nullable',
                'min:8',
                'max:25',
                'required_with:password_confirmation',
                function ($attribute, $value, $fail) use ($account) {
                    if (Hash::check($value, $account->password)) {
                        $fail(':attribute đã được sử dụng');
                    }
                },
            ];
            $require_pass_confirm = 'nullable|min:8|max:25|required_with:new_password|same:new_password';
        }
        return [
            'name'                  => 'required|min:5|max:255',
            'email'                 => $require_email,
            'new_password'          => $require_pass,
            'password_confirmation' => $require_pass_confirm,
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
            'name.required'                       => ':attribute là bắt buộc',
            'name.min'                            => ':attribute yêu cầu độ dài > 5 ký tự',
            'name.max'                            => ':attribute yêu cầu độ dài < 255 ký tự',
            'email.required'                      => ':attribute là bắt buộc',
            'email.email'                         => ':attribute không đúng định dạng',
            'email.unique'                        => ':attribute phải là duy nhất',
            'email.max'                           => ':attribute yêu cầu độ dài < 255 ký tự',
            'new_password.nullable'               => ':attribute là bắt buộc',
            'new_password.min'                    => ':attribute yêu cầu độ dài > 8 ký tự',
            'new_password.max'                    => ':attribute yêu cầu độ dài < 25 ký tự',
            'new_password.required_with'          => ':attribute là bắt buộc',
            'password_confirmation.nullable'      => ':attribute là bắt buộc',
            'password_confirmation.min'           => ':attribute yêu cầu độ dài > 8 ký tự',
            'password_confirmation.max'           => ':attribute yêu cầu độ dài < 25 ký tự',
            'password_confirmation.required_with' => ':attribute là bắt buộc',
            'password_confirmation.same'          => ':attribute không khớp với mật khẩu',
        ];
    }

    public function attributes()
    {
        return [
            'name'                  => 'Tên tài khoản ',
            'email'                 => 'Email ',
            'new_password'          => 'Mật khẩu ',
            'password_confirmation' => 'Xác nhận mật khẩu ',
        ];
    }
}
