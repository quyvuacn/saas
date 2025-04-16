<?php

namespace Modules\Admin\Http\Requests;

use App\Merchant;
use Illuminate\Foundation\Http\FormRequest;

class MachineRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();

        $merchant = $vaildateMonthSubscription = $dateAdded = '';
        if(!empty($data['merchant'])) {
            $merchant = Merchant::find($data['merchant'])->where('status', Merchant::REQUEST_SUCCESS)->get()->first();
            $vaildateMonthSubscription = 'required|integer|min:1|max:99';
            $dateAdded = 'required';
        }
        if(isset($data['merchant_id_current'])){
            if(empty($data['merchant']) || $data['merchant_id_current'] == $data['merchant']){
                 $vaildateMonthSubscription = $dateAdded = '';
            }
        }

        $statusMachine = empty($data['merchant']) ? 'required|integer' : '';

        $validateMerchant = [
            'integer',
            function ($attribute, $value, $fail) use ($merchant, $data) {
                if (!empty($data->merchant) && empty($merchant)) {
                    $fail(':attribute bạn chọn không hợp lệ');
                }
            },
        ];
        return [
            'name'                  => 'required|min:5|max:100',
            'model'                 => 'required|max:100',
            'date_added'            => $dateAdded,
            'merchant'              => $validateMerchant,
            'month_subscription'    => $vaildateMonthSubscription,
            'status_machine'        => $statusMachine
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
            'date_added.required'           => ':attribute là bắt buộc',
            'name.min'                      => ':attribute phải nhiều hơn 5 kí tự',
            'name.max'                      => ':attribute phải ít hơn 100 kí tự',
            'model.required'                => ':attribute là bắt buộc',
            'price_subscription.required'   => ':attribute là bắt buộc',
            'price_subscription.min'        => ':attribute phải lớn hơn 100,000',
            'price_subscription.max'        => ':attribute phải nhỏ hơn 999,999,999',
            'price_subscription.integer'    => ':attribute phải là số',
            'month_subscription.required'   => ':attribute là bắt buộc',
            'month_subscription.min'        => ':attribute phải lớn hơn 1 tháng',
            'month_subscription.max'        => ':attribute phải nhỏ hơn 99 tháng',
            'month_subscription.integer'    => ':attribute phải là số',
            'status_machine.required'       => ':attribute là bắt buộc',
            'status_machine.integer'        => ':attribute không đúng định dạng',
        ];
    }

    public function attributes()
    {
        return [
            'name'                  => 'Tên máy ',
            'model'                 => 'Model máy ',
            'date_added'            => 'Ngày nhận máy ',
            'merchant'              => 'Merchant ',
            'month_subscription'    => 'Số tháng gia hạn ',
            'price_subscription'    => 'Số tiền thanh toán ',
            'status_machine'        => 'Trạng thái máy ',
        ];
    }
}
