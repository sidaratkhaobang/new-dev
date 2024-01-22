<?php

namespace App\Exports;

use App\Enums\RentalTypeEnum;
use App\Models\ImportCarLine;
use App\Models\LongTermRental;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportFinanceRequest implements FromView, WithStyles, ShouldAutoSize
{
    public $finance_data;
    public $header_suplier;
    public $table_summary_data;
    public $table_summary_car_price_data;

    public function __construct($finance_data, $header_suplier, $table_summary_data, $table_summary_car_price_data)
    {
        $this->finance_data = $finance_data;
        $this->header_suplier = $header_suplier;
        $this->table_summary_data = $table_summary_data;
        $this->table_summary_car_price_data = $table_summary_car_price_data;
    }

    public function styles(Worksheet $sheet)
    {
        $blue_bg = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3399FF']
            ]
        ];

        $yellow_bg = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00']
            ]
        ];
//        return [
//            1 => $yellow_bg,
//            2 => $yellow_bg,
//        ];
    }

    public function view(): View
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'test');
        $sheet->setCellValue('B1', 'test');
        return view('admin.finance-request.export-template', [
            'finance_data' => $this->finance_data,
            'header_suplier' => $this->header_suplier,
            'table_summary_data' => $this->table_summary_data,
            'table_summary_car_price_data' => $this->table_summary_car_price_data
        ]);
    }
}
