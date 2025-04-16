<?php

namespace Modules\Admin\Http\Requests;

use App\Merchant;
use App\Models\Machine;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();

        $merchant = Merchant::query()->where('id', $data['merchant_id'])
            ->where('is_deleted', '!=', Merchant::DELETED)->count();

        $machine = Machine::query()->where('id', $data['machine_id'])
            ->where('is_deleted', '!=', Machine::IS_DELETED)->count();

        $validateDateExpire = '';
        if (!empty($data['date_expire_option'])) {
            $validateDateExpire = [
                'required',
                function ($attribute, $value, $fail) use ($data) {
                    if (strtotime($value) < time()) {
                        $fail('Ngày hết hạn không được nhỏ hơn ngày hiện tại');
                    }
                }
            ];
        }

        $validateMerchant = ['required', 'integer',
            function ($attribute, $value, $fail) use ($merchant) {
                if (empty($value)) {
                    $fail('Merchant không được để trống');
                } elseif (empty($merchant)) {
                    $fail('Merchant không hợp lệ');
                }
            },
        ];
        $validateMachine = ['required', 'integer',
            function ($attribute, $value, $fail) use ($machine) {
                if (empty($value)) {
                    $fail('Machine không được để trống');
                } elseif (empty($machine)) {
                    $fail('Machine không hợp lệ');
                }
            },
        ];
        return [
            'request_month' => 'required|integer|min:1|max:99',
            'request_price' => 'required|integer|min:50000|max:999999999999',
            'date_expire_option' => $validateDateExpire,
            'merchant_id' => $validateMerchant,
            'machine_id' => $validateMachine
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
            'request_month.required' => ':attribute là bắt buộc',
            'request_month.integer' => ':attribute phải là số',
            'request_month.min' => ':attribute phải lớn hơn 1',
            'request_month.max' => ':attribute phải nhỏ hơn 99',
            'request_price.required' => ':attribute là bắt buộc',
            'request_price.integer' => ':attribute phải là số',
            'request_price.min' => ':attribute phải lớn hơn 50000',
            'request_price.max' => ':attribute phải nhỏ hơn 999999999999',
            'date_expire_option.required' => ':attribute là bắt buộc',
            'merchant_id.required' => ':attribute là bắt buộc',
            'merchant_id.integer' => ':attribute không hợp lệ',
            'machine_id.required' => ':attribute là bắt buộc',
            'machine_id.integer' => ':attribute không hợp lệ',
        ];
    }

    public function attributes()
    {
        return [
            'request_month' => 'Số tháng gia hạn ',
            'request_price' => 'Số tiền thanh toán ',
            'date_expire_option' => 'Số tiền thanh toán ',
            'merchant_id' => 'Merchant ',
            'machine_id' => 'Machine ',
        ];
    }
}
