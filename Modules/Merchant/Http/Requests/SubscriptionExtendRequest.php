<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionExtendRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subscription_id' => 'required|numeric',
            'month'           => 'required|numeric',
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
            'subscription_id.required' => ':attribute là bắt buộc',
            'subscription_id.numeric'  => ':attribute phải là số',
            'month.required'           => ':attribute là bắt buộc',
            'month.numeric'            => ':attribute phải là số',
        ];
    }

    public function attributes()
    {
        return [
            'subscription_id' => 'ID Subscription ',
            'month'           => 'Tháng ',
        ];
    }
}
