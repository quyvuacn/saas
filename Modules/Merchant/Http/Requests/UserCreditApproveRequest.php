<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreditApproveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'credit_quota' => 'required|numeric|min:'.config('merchant.min_credit_quote').'|max:'.config('merchant.max_credit_quote'),
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
            'credit_quota.required' => ':attribute ' . __('là bắt buộc'),
            'credit_quota.numeric'  => ':attribute ' . __('phải là số'),
            'credit_quota.min'      => ':attribute ' . __('có giá trị nhỏ nhất là :min', ['min' => config('merchant.min_credit_quote')]),
            'credit_quota.max'      => ':attribute ' . __('có giá trị lớn nhất là :max', ['max' => config('merchant.max_credit_quote')]),
        ];
    }

    public function attributes()
    {
        return [
            'credit_quota' => 'Hạn mức ',
        ];
    }
}
