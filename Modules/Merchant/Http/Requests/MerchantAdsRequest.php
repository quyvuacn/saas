<?php

namespace Modules\Merchant\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class MerchantAdsRequest extends FormRequest
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

        $end_date             = strtotime(convertDateFlatpickr(request()->end_date));
        $start_date           = strtotime(convertDateFlatpickr(request()->start_date));

        $end_date_required    = [
            'required',
            function ($attribute, $value, $fail) use ($start_date, $end_date) {
                if ($start_date >= $end_date) {
                    $fail('Ngày kết thúc phải lớn hơn ngày bắt đầu');
                }
            },
        ];
        $start_date_required  = [
            'required',
            function ($attribute, $value, $fail) use ($start_date, $end_date) {
                if ($start_date >= $end_date) {
                    $fail('Ngày bắt đầu phải nhỏ hơn ngày kết thúc');
                }
            },
        ];
        $has_machine          = request()->has_machine;
        $machines_list        = request()->machines_list;
        $has_machine_required = [
            function ($attribute, $value, $fail) use ($has_machine, $machines_list) {
                if ($has_machine == null || $machines_list == null) {
                    $fail('Bạn phải chọn máy bán hàng cho quảng cáo này.');
                }
            },
        ];
        return [
            'start_date'  => $start_date_required,
            'end_date'    => $end_date_required,
            'file'        => $file_rule,
            'has_machine' => $has_machine_required,
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
            'start_date.required' => ':attribute là bắt buộc.',
            'end_date.required'   => ':attribute là bắt buộc.',
            'file.mimes'          => ':attribute phải có định dạng jpeg, jpg, png.',
            'file.image'          => ':attribute không phải là hình ảnh.',
            'file.max'            => ':attribute ' . __('có kích thước lớn nhất là :max Kb', ['max' => 2048]),
        ];
    }

    public function attributes()
    {
        return [
            'start_date' => 'Ngày bắt đầu ',
            'end_date'   => 'Ngày kết thúc',
            'file'       => 'Hình ảnh quảng cáo ',
            'machines'   => 'Máy bán hàng ',
        ];
    }
}
