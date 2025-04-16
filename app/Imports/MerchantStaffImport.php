<?php

namespace App\Imports;

use App\Models\MerchantSettingStaff;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class MerchantStaffImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;

    protected $staffList;

    public function __construct($staffList)
    {
        $this->staffList = $staffList;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $merchant_id = auth(MERCHANT)->id();
        // $currentRowNumber = $this->getRowNumber(); // Error
        // $chunkOffset      = $this->getChunkOffset(); // Error
        if ($row['email'] && filter_var($row['email'], FILTER_VALIDATE_EMAIL) && strlen($row['email']) <= 255) {
            if (!$this->staffList->contains($row['email'])) {
                return new MerchantSettingStaff([
                    'merchant_id'         => $merchant_id,
                    'employee_code'       => substr($row['code'], 0, 50),
                    'employee_email'      => $row['email'],
                    'employee_department' => substr($row['unit'], 0, 255),
                    'employee_quota'      => intval($row['credit']) >= 0 ? intval($row['credit']) : 0,
                    'created_by'          => $merchant_id,
                    'updated_by'          => $merchant_id,
                ]);
            } else {
                MerchantSettingStaff::where('employee_email', $row['email'])->update([
                    'employee_code'       => substr($row['code'], 0, 50),
                    'employee_department' => substr($row['unit'], 0, 255),
                    'employee_quota'      => intval($row['credit']) >= 0 ? intval($row['credit']) : 0,
                    'updated_by'          => $merchant_id,
                ]);
            }
        }
    }

    public function rules()
    : array
    {
        return [
            '0.email'  => Rule::in(['Email']),
            '0.code'   => Rule::in(['Code']),
            '0.unit'   => Rule::in(['Unit']),
            '0.credit' => Rule::in(['Credit']),
            '*.email'  => 'email|required',
            '*.credit' => 'required|numeric|min:'.config('merchant.min_credit_quote').'|max:'.config('merchant.max_credit_quote'),
            '*.code'   => 'required|min:3|max:255',
            '*.unit'   => 'required|min:3|max:255',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'email'  => 'Email',
            'code'   => 'Mã nhân viên',
            'unit'   => 'Đơn vị',
            'credit' => 'Tín dụng',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.email.in'  => ':attribute không đúng định đạng',
            '0.code.in'   => ':attribute không đúng định đạng',
            '0.unit.in'   => ':attribute không đúng định đạng',
            '0.credit.in' => ':attribute không đúng định đạng',

            '*.email.required' => ':attribute là bắt buộc',
            '*.email.email'    => ':attribute không đúng định dạng email',

            '*.code.required' => ':attribute là bắt buộc',
            '*.code.min'      => ':attribute tối thiểu = 3 ký tự',
            '*.code.max'      => ':attribute tối đa = 255 ký tự',

            '*.unit.required' => ':attribute là bắt buộc',
            '*.unit.min'      => ':attribute tối thiểu = 3 ký tự',
            '*.unit.max'      => ':attribute tối đa = 255 ký tự',

            '*.credit.required' => ':attribute là bắt buộc',
            '*.credit.numeric'  => ':attribute không đúng định dạng email',
            '*.credit.min'      => ':attribute tối thiểu = '.config('merchant.min_credit_quote').' coin',
            '*.credit.max'      => ':attribute tối đa = '.config('merchant.max_credit_quote').' coin',
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
