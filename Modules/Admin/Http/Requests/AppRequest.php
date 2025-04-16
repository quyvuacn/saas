<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $file = $_FILES;
        return [
            'version'          => 'required|min:1|max:50',
            'code'             => 'required|min:1|max:10000|unique:app_version',
            'brief'            => 'required|max:500|min:1',
            'file'             => [
                'required',
                'max:102400',
                function ($attribute, $value, $fail) use ($file) {
                    if(!empty($file['file']['type']) && $file['file']['type'] != 'application/vnd.android.package-archive'){
                        $fail("File APK phải có định dạng APK");
                    }
                },
            ],
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
            'version.required'          => ':attribute là bắt buộc',
            'version.min'               => ':attribute yêu cầu đồ dài > 1 ký tự',
            'code.required'             => ':attribute là bắt buộc',
            'code.min'                  => ':attribute yêu cầu đồ dài > 1 ký tự',
            'code.max'                  => ':attribute yêu cầu < 10000',
            'code.unique'               => ':attribute đã tồn tại',
            'brief.required'            => ':attribute là bắt buộc',
            'brief.min'                 => ':attribute yêu cầu đồ dài > 1 ký tự',
            'brief.max'                 => ':attribute yêu cầu đồ dài < 10000 ký tự',
            'file.max'                  => ':attribute ' . __('có kích thước lớn nhất là :max Kb', ['max' => 102400]),
            'file.required'             => ':attribute là bắt buộc'
        ];
    }

    public function attributes()
    {
        return [
            'version'   => 'Tên phiên bản ',
            'file'      => 'File APK ',
            'code'      => 'Version Code',
            'brief'     => 'Mô tả'
        ];
    }
}
