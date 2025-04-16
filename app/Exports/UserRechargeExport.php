<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;


class UserRechargeExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithEvents
{
    protected $userDebtList;

    public function __construct($userDebtList)
    {
        $this->userDebtList = $userDebtList;
    }

    public function collection()
    {
        return $this->userDebtList;
    }

    public function headings()
    : array
    {
        return [
            'Mã giao dịch',
            'Mã Nhân viên',
            'Email',
            'Đơn vị',
            'Coin',
            'Thành Tiền',
            'Nội dung',
            'Trạng thái',
            'Ngày yêu cầu',
        ];
    }

    public function map($user)
    : array {
        return [
            $user->transaction_id ?? '---',
            $user->user->staff->employee_code ?? '-',
            $user->user->email ?? '---',
            $user->user->staff->employee_department ?? '---',
            $user->coin ? number_format($user->coin) : '---',
            $user->coin ? number_format($user->coin) : '---',
            $user->message ?? '---',
            $user->status == 1 ? 'Đã duyệt' : 'Mới',
            $user->created_at ? $user->created_at->format('d-m-Y') : '---',
        ];
    }

    public function columnFormats()
    : array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function registerEvents()
    : array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($styleArray) {
                $event->sheet->getStyle('A1:I1')->applyFromArray($styleArray);
            },
        ];
    }
}
