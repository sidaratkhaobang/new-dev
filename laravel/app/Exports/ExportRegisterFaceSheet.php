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

class ExportRegisterFaceSheet implements FromView, WithStyles, ShouldAutoSize
{
    public $registers;
    public $topic_face_sheet;
    public function __construct($registers,$topic_face_sheet)
    {
        $this->registers = $registers;
        $this->topic_face_sheet = $topic_face_sheet;

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
        
        return [
            //
        ];
    }


    public function view(): View
    {
        return view('admin.registers.export-template', [
            'registers' => $this->registers,
            'topic_face_sheet' => $this->topic_face_sheet,
        ]);
    }



}
