<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

class ExportAssetPostValueCar implements FromView
{
    public $asset_lot;
    public function __construct($asset_lot)
    {
        $this->asset_lot = $asset_lot;
    }

    public function view(): View
    {
        return view('admin.asset-cars.excel.post-value-car.export-template', [
            'asset_lot' => $this->asset_lot,
        ]);
    }
}
