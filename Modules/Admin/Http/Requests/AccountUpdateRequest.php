<?php

namespace Modules\Admin\Http\Requests;

use App\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Classes\Facades\AdminCan;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data     = $this->request->all();
        $account  = $this->account;
        $required = '';
        $account = Admin::query()->where('id', $account)->where('is_deleted', '<>', Admin::DELETED)->first();
        if ($data['password'] != null) {
            $required = [
                'min:8',
                'max:20',
                function ($attribute, $value, $fail) use ($account) {
                    if (Hash::check($value, $account->password)) {
                        $fail(':attribute đã được sử dụng');
                    }
                },
            ];
        }
        $isRequired = AdminCan::do('adm.account.is_super_admin') ? 'required|' : '';
        return [
            'name'        => 'required|min:10|max:50',
            'permissions' => 'required',
            'password'    => $required,
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
            'password.different'   => ':attribute đã sử dụng',
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
