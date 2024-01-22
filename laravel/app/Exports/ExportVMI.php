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

class ExportVMI implements FromView, WithStyles, ShouldAutoSize
{
    public $vmi_list;
    public function __construct($vmi_list)
    {
        $this->vmi_list = $vmi_list;
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
            1 => $blue_bg,
            'A1' => $yellow_bg,
            'B1' => $yellow_bg,
            'C1' => $yellow_bg,
            'D1' => $yellow_bg,
            'E1' => $yellow_bg,
        ];
    }

    public function view(): View
    {
        return view('admin.vmi-cars.export-template', [
            'vmi_list' => $this->vmi_list,
        ]);
    }
}
