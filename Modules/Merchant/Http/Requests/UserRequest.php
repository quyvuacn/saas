<?php

namespace Modules\Merchant\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            $require = 'required|min:9|max:25';
        }
        $existUser = User::query()->where('email', $data['email'])->where('is_deleted', '<>', USER::DELETED)->first();
        $email_validates = [
            'required',
            'email',
            'max:99',
            function ($attribute, $value, $fail) use ($existUser) {
                if ($existUser) {
                    $fail(':attribute đã tồn tại');
                }
            },
        ];
        return [
            'email'    => $email_validates,
            'password' => $require,
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
            'email.required'    => ':attribute là bắt buộc',
            'email.email'       => ':attribute không đúng định dạng',
            'email.max'         => ':attribute yêu cầu độ dài < 99 ký tự',
            'password.required' => ':attribute là bắt buộc',
            'password.min'      => ':attribute yêu cầu độ dài >= 9 ký tự',
            'password.max'      => ':attribute yêu cầu độ dài < 25 ký tự',
        ];
    }

    public function attributes()
    {
        return [
            'email'    => 'Email ',
            'password' => 'Mật khẩu ',
        ];
    }
}
