<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;


class UserExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithEvents
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
            'Email',
            'Full Name',
            'Phone Number',
            'Credit Quota',
            'Department',
        ];
    }

    public function map($user)
    : array {
        return [
            'demo@demo.com',
            'Demo Full Name',
            '1234567890',
            '100000',
            'Demo Department',
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
