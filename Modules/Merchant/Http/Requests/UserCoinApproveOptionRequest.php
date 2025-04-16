<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCoinApproveOptionRequest extends FormRequest
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
            'user_coin.required'   => ':attribute là bắt buộc',
            'user_coin.numeric' => ':attribute ' . __('phải là số'),
            'user_coin.min'     => ':attribute ' . __('có giá trị nhỏ nhất là :min', ['min' => config('merchant.min_credit_quote')]),
            'user_coin.max'     => ':attribute ' . __('có giá trị lớn nhất là :max', ['max' => config('merchant.max_credit_quote')]),
        ];
    }

    public function attributes()
    {
        return [
            'user_coin' => 'Coin ',
        ];
    }
}
