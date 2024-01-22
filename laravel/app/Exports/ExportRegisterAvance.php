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

class ExportRegisterAvance implements FromView, WithStyles, ShouldAutoSize
{
    public $registers;
    public $topic_face_sheet;
    public $receipt_avance_total;
    public $operation_fee_avance_total;
    public $total;
    public function __construct($registers, $topic_face_sheet, $receipt_avance_total, $operation_fee_avance_total, $total)
    {
        $this->registers = $registers;
        $this->topic_face_sheet = $topic_face_sheet;
        $this->receipt_avance_total = $receipt_avance_total;
        $this->operation_fee_avance_total = $operation_fee_avance_total;
        $this->total = $total;
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

        $sheet->getStyle('L1:L' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        $sheet->getStyle('M1:M' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
        $sheet->getStyle('K1:K' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        return [
            //
        ];
    }


    public function view(): View
    {
        return view('admin.registers.export-avance', [
            'registers' => $this->registers,
            'test' => $this->topic_face_sheet,
            'receipt_avance_total' => $this->receipt_avance_total,
            'operation_fee_avance_total' => $this->operation_fee_avance_total,
            'total' => $this->total,
        ]);
    }

}
