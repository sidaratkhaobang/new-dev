<?php

namespace App\Exports;

use App\Models\PurchaseOrderLine;
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

class ExportImportCar implements FromView
{
    public $po_line;
    public function __construct($po_line)
    {
        $this->po_line = $po_line;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('admin.import-cars.export-template', [
            'po_lines' => $this->po_line,
        ]);
    }
}
