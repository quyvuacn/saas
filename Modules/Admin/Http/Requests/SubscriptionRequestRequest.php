<?php

namespace Modules\Admin\Http\Requests;

use App\Models\SubscriptionRequest as SubscriptionRequestModel;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = $this->request->all();
        $subscriptionRequestId = request('subscriptionRequest');
        $subscriptionRequest = SubscriptionRequestModel::query()
            ->where('id', $subscriptionRequestId)
            ->where('is_deleted', '!=', SubscriptionRequestModel::IS_DELETED)
            ->first();

        $subscription = Subscription::where([
            'merchant_id' => $subscriptionRequest->merchant_id,
            'machine_id' => $subscriptionRequest->machine_id,
        ])->first();

        $validatePrice = '';
        $validateDateExpire = '';

        if ($data['merchant_audit'] == 0) {
            $validateDateExpire = [
                function ($attribute, $value, $fail) use ($subscription) {
                    if (!empty($value) && !empty($subscription->date_expiration) && strtotime($subscription->date_expiration) >= strtotime(convertDateFlatpickr($value))) {
                        $fail(':attribute không được nhỏ hơn ngày hết hạn hiện tại');
                    }
                    if(!empty($value) && empty($subscription->date_expiration) && time() >= strtotime(convertDateFlatpickr($value))) {
                        $fail(':attribute không được nhỏ hơn ngày hiện tại');
                    }
                },
            ];
            $validatePrice = ['required', 'integer', 'min:1000', 'max:99999999999'];
        }
        return [
            'merchant_audit' => 'required|in:0,1',
            'other_info' => 'required|min:10|max:10000',
            'request_price' => $validatePrice,
            'date_expire_option' => $validateDateExpire
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
            'merchant_audit.required' => ':attribute là bắt buộc',
            'merchant_audit.in' => ':attribute không hợp lệ',
            'other_info.required' => ':attribute là bắt buộc',
            'other_info.max' => ':attribute phải ít hơn 10000 kí tự',
            'other_info.min' => ':attribute phải nhiều hơn 10 kí tự',
            'request_price.required' => ':attribute là bắt buộc',
            'request_price.integer' => ':attribute phải là số',
            'request_price.min' => ':attribute phải lớn hơn 1000',
            'request_price.max' => ':attribute phải nhỏ hơn 99999999999',
            'date_expire_option.required' => ':attribute là bắt buộc',
        ];
    }

    public function attributes()
    {
        return [
            'merchant_audit' => 'Trạng thái yêu cầu ',
            'other_info' => 'Thông tin giao dịch ',
            'request_price' => 'Số tiền thanh toán ',
            'date_expire_option' => 'Hạn sử dụng sau khi nạp thêm ',
        ];
    }
}
