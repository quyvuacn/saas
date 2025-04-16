<?php

namespace App\Imports;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Merchant\Jobs\UserImportEmailJob;
use Modules\Merchant\Repositories\Eloquent\UserRepository;
use Modules\Merchant\Repositories\UserRepositoryInterface;

class UserImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;

    protected $listUser;

    public function __construct($listUser)
    {
        $this->listUser = $listUser;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $merchant = auth(MERCHANT)->user();
        if ($row['email'] && filter_var($row['email'], FILTER_VALIDATE_EMAIL) && strlen($row['email']) <= 255) {
            // New user
            if (!$this->listUser->contains($row['email'])) {
                $model    = new User();
                $salt     = $model->generateSalt();
                $password = Str::random(9);
                $data     = [
                    'email'        => $row['email'],
                    'full_name'    => substr($row['full_name'], 0, 100),
                    'password'     => $model->hashPassword($salt, $password),
                    'salt'         => $salt,
                    'uid'          => $model->genereateUUID(),
                    'phone_number' => substr($row['phone_number'], 0, 20),
                    'status'       => 1,
                    'merchant_id'  => $merchant->getMerchantID(),
                ];
                if (is_numeric($row['credit_quota']) && $row['credit_quota'] > 0) {
                    $data['credit_quota']      = intval($row['credit_quota']);
                    $data['is_credit_account'] = User::IS_CREDIT;
                    $data['coin']              = intval($row['credit_quota']);
                }
                if ($row['department'] && strlen($row['department']) > 3 && strlen($row['department']) < 255) {
                    $data['department'] = $row['department'];
                }
                $user = $model::create($data);
                dispatch(new UserImportEmailJob($user, $password))->delay(Carbon::now()->addSeconds(15));
            } else {
                // Exist email
                $updateUser = User::where('email', $row['email'])->where('merchant_id', $merchant->getMerchantID())->where('is_deleted', '<>', User::DELETED)->first();
                if ($updateUser) {
                    $credit = [
                        'full_name'         => substr($row['full_name'], 0, 100),
                        'phone_number'      => substr($row['phone_number'], 0, 20),
                        'department'        => substr($row['department'], 0, 255),
                        'credit_updated_by' => $merchant->getMerchantID(),
                        'credit_quota'      => intval($row['credit_quota']) >= 0 ? intval($row['credit_quota']) : 0,
                        'coin'              => $updateUser->coin + (intval($row['credit_quota']) - $updateUser->credit_quota),
                    ];
                    if ($updateUser->is_credit_account !== User::IS_CREDIT) {
                        if (intval($row['credit_quota']) > 0) {
                            $credit['is_credit_account'] = User::IS_CREDIT;
                        } else {
                            $credit['credit_quota'] = 0;
                            $credit['coin']         = $updateUser->coin;
                        }
                    }
                    $updateUser->update($credit);
                }
            }
        }
    }

    public function rules()
    : array
    {
        return [
            '0.email'        => Rule::in(['Email']),
            '0.full_name'    => Rule::in(['Full Name']),
            '0.phone_number' => Rule::in(['Phone Number']),
            '0.credit_quota' => Rule::in(['Credit Quota']),
            '*.email'        => 'email|required',
            '*.full_name'    => 'min:5|max:255',
            '*.phone_number' => 'numeric|digits_between:10,20', // digit between 10, 20
            '*.credit_quota' => function($attribute, $value, $onFailure) {
                // Only validate if has Value
                if ($value) {
                    if (!is_numeric($value)) {
                        $onFailure(':attribute không đúng định dạng');
                        return;
                    }
                    if ($value < 50000) {
                        $onFailure(':attribute tối thiểu = ' . config('merchant.min_credit_quote') . ' coin');
                        return;
                    }
                    if ($value > 10000000) {
                        $onFailure(':attribute tối đa = ' . config('merchant.max_credit_quote') . ' coin');
                        return;
                    }
                }
            },
            '*.department' => function($attribute, $value, $onFailure) {
                // Only validate if has Value
                if ($value) {
                    if (strlen($value) < 3) {
                        $onFailure(':attribute yêu cầu độ dài tối thiểu = 3 ký tự');
                        return;
                    }
                    if (strlen($value) > 255) {
                        $onFailure(':attribute yêu cầu độ dài tối đa = 255 ký tự');
                        return;
                    }
                }
            }
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'email'        => 'Email',
            'full_name'    => 'Họ và tên',
            'phone_number' => 'Số điện thoại',
            'credit_quota' => 'Tín dụng',
            'department'   => 'Phòng ban',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.email.in'        => ':attribute không đúng định đạng',
            '0.full_name.in'    => ':attribute không đúng định đạng',
            '0.phone_number.in' => ':attribute không đúng định đạng',

            '*.email.required' => ':attribute là bắt buộc',
            '*.email.email'    => ':attribute không đúng định dạng email',

            '*.full_name.min' => ':attribute tối thiểu = 5 ký tự',
            '*.full_name.max' => ':attribute tối đa = 255 ký tự',

            '*.phone_number.digits_between' => ':attribute tối thiểu = 10 ký tự, tối đa 20 ký tự',
            '*.phone_number.numeric'        => ':attribute không đúng định dạng',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

    public function headingRow()
    : int
    {
        return 1;
    }

    public function batchSize()
    : int
    {
        return 1000;
    }

    public function chunkSize()
    : int
    {
        return 1000;
    }
}
