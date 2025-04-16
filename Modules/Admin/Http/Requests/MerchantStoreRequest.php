<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'merchant_company' => 'required',
            'merchant_address' => 'required',
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
            'name.required' => ':attribute là bắt buộc',
            'email.required' => ':attribute là bắt buộc',
            'email.email' => ':attribute không đúng định dạng',
            'phone.required' => ':attribute là bắt buộc',
            'merchant_company.required' => ':attribute là bắt buộc',
            'merchant_address.required' => ':attribute là bắt buộc',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên Merchant ',
            'email' => 'Email Merchant ',
            'phone' => 'Số điện thoại ',
            'merchant_company' => 'Tên công ty ',
            'merchant_address' => 'Địa chỉ',
        ];
    }
}
