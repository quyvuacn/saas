<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;


class SellingHistoryExport implements FromCollection, WithHeadings, WithColumnFormatting, WithMapping, WithEvents
{
    protected $histories;

    public function __construct($histories)
    {
        $this->histories = $histories;
    }

    public function collection()
    {
        return $this->histories;
    }

    public function headings()
    : array
    {
        return [
            'Mã giao dịch',
            'Mã máy/Tên máy',
            'Vị trí đặt máy',
            'Sản phẩm',
            'Giá bán',
            'Số lượng',
            'Thành tiền',
            'Thời gian bán',
            'Người mua',
            'Trạng thái',
        ];
    }

    public function map($row)
    : array {
        $products = json_decode($row->products);
        $content  = '---';
        switch ($row->status) {
            case 'NEW':
                $content = 'Mới';
                break;
            case 'SUCCESS':
                $content = 'Thành công';
                break;
            case 'PROCESSING':
                $content = 'Đang xử lý';
                break;
            case 'ERROR':
                $content = 'Lỗi';
                break;
        }

        $name     = '---';
        $price    = 0;
        $quantity = 0;

        if (isset($products[0])) {
            $name     = $products[0]->name;
            $price    = $products[0]->price;
            $quantity = $products[0]->quantity;
        }
        return [
            $row->transaction_id ?? '---',
            ($row->machine->model ?? '---') . ' / ' . ($row->machine->name ?? '---'),
            $row->machine->machine_address ?? '---',
            $name,
            number_format($price),
            $quantity,
            number_format($price * $quantity),
            $row->created_at->format('d-m-Y H:i:s'),
            $row->user->email ?: '---',
            $content,
        ];
    }

    public function columnFormats()
    : array
    {
        return [// 'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
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
                $event->sheet->getStyle('A1:J1')->applyFromArray($styleArray);
            },
        ];
    }
}
