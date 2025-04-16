<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $file_rule = '';
        if (isset(request()->file) && request()->file !== null || isset(request()->create)) {
            $file_rule = 'mimes:jpeg,jpg,png|image|max:2048';
        }
        return [
            'name'          => 'required|min:5|max:255',
            'price_default' => 'required|numeric|min:1000|max:100000',
            'brief'         => 'required|min:5|max:1000',
            'file'          => $file_rule,
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
            'name.required'          => ':attribute là bắt buộc',
            'name.min'               => ':attribute yêu cầu đồ dài > 5 ký tự',
            'name.max'               => ':attribute yêu cầu đồ dài < 255 ký tự',
            'price_default.required' => ':attribute là bắt buộc',
            'price_default.numeric'  => ':attribute phải là số',
            'price_default.min'      => ':attribute ' . __('có giá trị nhỏ nhất là :min', ['min' => 1000]),
            'price_default.max'      => ':attribute ' . __('có giá trị lớn nhất là :max', ['max' => 100000]),
            'brief.required'         => ':attribute là bắt buộc',
            'brief.min'              => ':attribute yêu cầu đồ dài > 5 ký tự',
            'brief.max'              => ':attribute ' . __('có độ dài lớn nhất là :max', ['max' => 1000]),
            'file.mimes'             => ':attribute phải có định dạng jpeg, jpg, png',
            'file.image'             => ':attribute không phải là hình ảnh',
            'file.max'               => ':attribute ' . __('có kích thước lớn nhất là :max Kb', ['max' => 2048]),
        ];
    }

    public function attributes()
    {
        return [
            'name'          => 'Tên sản phẩm ',
            'price_default' => 'Giá mặc định sản phẩm',
            'brief'         => 'Mô tả sản phẩm ',
            'file'          => 'Hình ảnh sản phẩm',
        ];
    }
}
