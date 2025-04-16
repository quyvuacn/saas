<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $end_date             =  !empty(request()->time_end_show) ? strtotime(convertDateTimeFlatpickr(request()->time_end_show)) : '';
        $start_date           = !empty(request()->time_begin_show) ? strtotime(convertDateTimeFlatpickr(request()->time_begin_show)) : '';

        $end_date_required    = [
            'required',
            function ($attribute, $value, $fail) use ($start_date, $end_date) {
                if ($start_date >= $end_date) {
                    $fail(':attribute phải lớn hơn ngày bắt đầu');
                }
            },
        ];
        $start_date_required  = [
            'required',
            function ($attribute, $value, $fail) use ($start_date, $end_date) {
                if ($start_date >= $end_date) {
                    $fail(':attribute phải nhỏ hơn ngày kết thúc');
                }
            },
        ];

        return [
            'name'              => 'required|min:5|max:500',
            'time_begin_show'   => $start_date_required,
            'time_end_show'     => $end_date_required,
            'brief'             => 'required|min:10|max:255',
            'content'           => 'required|min:10|max:1000',
            'file'              => 'mimes:jpeg,jpg,png|image|max:1024'
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
            'name.required'             => ':attribute ' . __('là bắt buộc'),
            'name.min'                  => ':attribute ' . __('có chiều dài > 5 ký tự'),
            'name.max'                  => ':attribute ' . __('có chiều dài < 50 ký tự'),
            'time_begin_show.required'  => ':attribute ' . __('là bắt buộc'),
            'time_end_show.required'    => ':attribute ' . __('là bắt buộc'),
            'brief.required'            => ':attribute ' . __('là bắt buộc'),
            'brief.min'                 => ':attribute ' . __('có chiều dài > 10 ký tự'),
            'brief.max'                 => ':attribute ' . __('có chiều dài < 255 ký tự'),
            'content.required'          => ':attribute ' . __('là bắt buộc'),
            'content.min'               => ':attribute ' . __('có chiều dài > 10 ký tự'),
            'content.max'               => ':attribute ' . __('có chiều dài < 1000 ký tự'),
            'file.mimes'                => ':attribute phải có định dạng jpeg, jpg, png.',
            'file.image'                => ':attribute không phải là hình ảnh.',
            'file.max'                  => ':attribute ' . __('có kích thước lớn nhất là :max Kb', ['max' => 1024]),
        ];
    }

    public function attributes()
    {
        return [
            'name'              => 'Tiêu đề ',
            'time_begin_show'   => 'Thời gian xuất bản ',
            'time_end_show'     => 'Thời gian ngừng hiển thị thông báo ',
            'brief'             => 'Miêu tả ',
            'content'           => 'Nội dung ',
            'file'              => 'Hình ảnh kèm thông báo '
        ];
    }
}
