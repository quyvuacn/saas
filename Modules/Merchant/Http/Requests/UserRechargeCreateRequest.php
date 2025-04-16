<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRechargeCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_coin' => 'required|numeric|min:'.config('merchant.min_credit_quote').'|max:'.config('merchant.max_credit_quote'),
            'user_id'   => 'required|numeric|exists:App\User,id',
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
            'user_coin.required' => ':attribute là bắt buộc',
            'user_coin.numeric'  => ':attribute phải là số',
            'user_coin.min'      => ':attribute có giá trị nhỏ nhất là '.config('merchant.min_credit_quote'),
            'user_coin.max'      => ':attribute có giá trị lớn nhất là '.config('merchant.max_credit_quote'),
            'user_id.required'   => ':attribute là bắt buộc',
            'user_id.numeric'    => ':attribute phải là số',
            'user_id.exists'     => ':attribute không tồn tại',
        ];
    }

    public function attributes()
    {
        return [
            'user_coin' => 'Số Coin ',
            'user_id'   => 'Thông tin người dùng ',
        ];
    }
}
