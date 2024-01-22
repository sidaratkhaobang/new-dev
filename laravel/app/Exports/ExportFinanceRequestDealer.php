<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportFinanceRequestDealer implements FromView, WithStyles, ShouldAutoSize
{
    public $finance_data;
    public $header_suplier;
    public $table_summary_data;
    public $table_summary_car_price_data;

    public function __construct($finance_data)
    {
        $this->finance_data = $finance_data;
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


        // Find the range of columns that have data
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        $dataRange = "A1:{$lastColumn}{$lastRow}";
    }

    public function view(): View
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'test');
        $sheet->setCellValue('B1', 'test');
        return view(
            'admin.finance-request.export-dealer-template',
            [
                'finance_data' => $this->finance_data,
            ]);
    }
}
