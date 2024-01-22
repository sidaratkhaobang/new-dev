<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportOwnershipTransferTemplate implements FromView, WithStyles, ShouldAutoSize
{
    public $ownership_transfers;
    public $test;
    public function __construct($ownership_transfers,$test)
    {
        $this->ownership_transfers = $ownership_transfers;
        $this->test = $test;

    }
    
    public function styles(Worksheet $sheet)
    {
        $blue_bg = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ABBF3']
            ],'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ]
        ];

        $yellow_bg = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00']
            ]
        ];

        $sheet->getStyle('A2:I' . $sheet->getHighestRow())->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ECB1CF']
            ],'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ]);

        return [
            'A1:P1' => $blue_bg,
        ];
    }


    public function view(): View
    {
        return view('admin.ownership-transfers.export-template-edit', [
            'ownership_transfers' => $this->ownership_transfers,
            'test' => $this->test,
        ]);
    }



}
