<?php

namespace Modules\Merchant\Http\Requests;

use App\Merchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Util\RegexHelper;

class AccountSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi;
     * ^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$
     */
    public function rules()
    {
        $data     = $this->request->all();
        $required = "";

        if (isset($data['checkout_address']) && $data['checkout_address'] == 1) {
            $required = 'required|min:5|max:255';
        }

        return [
            'name'                 => 'required|min:5|max:255',
            'company'              => 'required|min:5|max:255',
            'company_address'      => 'required|min:5|max:255',
            'dept_collection_date' => 'required|numeric|min:1|max:31',
            'other_address_input'  => $required,
            'bank_name.*'          => 'required|min:5|max:255',
            'benefit_name.*'       => 'required|min:5|max:255',
            'bank_number.*'        => 'required|min:5|max:255',
            'website'              => "required|min:5|max:255|regex:/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&'\(\)\*\+,;=.]+$/i",
            'phone'                => 'required|min:5|max:20',
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
            'name.required'                 => ':attribute là bắt buộc',
            'name.min'                      => ':attribute yêu cầu độ dài > 5 ký tự',
            'name.max'                      => ':attribute yêu cầu độ dài < 255 ký tự',
            'company.required'              => ':attribute là bắt buộc',
            'company.min'                   => ':attribute yêu cầu độ dài > 5 ký tự',
            'company.max'                   => ':attribute yêu cầu độ dài < 255 ký tự',
            'company_address.required'      => ':attribute là bắt buộc',
            'company_address.min'           => ':attribute yêu cầu độ dài > 5 ký tự',
            'company_address.max'           => ':attribute yêu cầu độ dài < 255 ký tự',
            'dept_collection_date.required' => ':attribute là bắt buộc',
            'dept_collection_date.min'      => ':attribute phải là số > 1',
            'dept_collection_date.max'      => ':attribute phải là số < 32',
            'dept_collection_date.numeric'  => ':attribute không đúng định dạng số',
            'other_address_input.required'  => ':attribute là bắt buộc',
            'other_address_input.min'       => ':attribute yêu cầu độ dài > 5 ký tự',
            'other_address_input.max'       => ':attribute yêu cầu độ dài < 255 ký tự',
            'benefit_name.*.required'       => ':attribute là bắt buộc',
            'benefit_name.*.min'            => ':attribute yêu cầu độ dài > 5 ký tự',
            'benefit_name.*.max'            => ':attribute yêu cầu độ dài < 255 ký tự',
            'bank_name.*.required'          => ':attribute là bắt buộc',
            'bank_name.*.min'               => ':attribute yêu cầu độ dài > 5 ký tự',
            'bank_name.*.max'               => ':attribute yêu cầu độ dài < 255 ký tự',
            'bank_number.*.required'        => ':attribute là bắt buộc',
            'bank_number.*.min'             => ':attribute yêu cầu độ dài > 5 ký tự',
            'bank_number.*.max'             => ':attribute yêu cầu độ dài < 255 ký tự',
            'website.required'              => ':attribute là bắt buộc',
            'website.min'                   => ':attribute yêu cầu độ dài > 5 ký tự',
            'website.max'                   => ':attribute yêu cầu độ dài < 255 ký tự',
            'website.regex'                 => ':attribute không đúng định dạng',
            'phone.required'                => ':attribute là bắt buộc',
            'phone.min'                     => ':attribute yêu cầu độ dài > 10 ký tự',
            'phone.max'                     => ':attribute yêu cầu độ dài < 20 ký tự',

        ];
    }

    public function attributes()
    {
        return [
            'name'                 => '[Tên tài khoản] ',
            'company'              => '[Tên công ty] ',
            'company_address'      => '[Địa chỉ công ty] ',
            'dept_collection_date' => '[Ngày thu hồi công nợ] ',
            'other_address_input'  => '[Địa chỉ khác] ',
            'bank_name.*'          => '[Tên ngân hàng] ',
            'benefit_name.*'       => '[Người thụ hưởng] ',
            'bank_number.*'        => '[Số tài khoản] ',
            'website'              => '[Website] ',
            'phone'                => '[Hotline] ',
        ];
    }
}
