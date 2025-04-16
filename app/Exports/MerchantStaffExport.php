<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;


class MerchantStaffExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithEvents
{
    protected $staffList;

    public function __construct($staffList)
    {
        $this->staffList = $staffList;
    }

    public function collection()
    {
        return $this->staffList;
    }

    public function headings()
    : array
    {
        return [
            'Code',
            'Email',
            'Unit',
            'Credit',
        ];
    }

    public function map($user)
    : array {
        return [
            $user->employee_code,
            $user->employee_email,
            $user->employee_department,
            $user->employee_quota,
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
                $event->sheet->getStyle('A1:G1')->applyFromArray($styleArray);
            },
        ];
    }
}
