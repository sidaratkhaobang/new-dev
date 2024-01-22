<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;

class ExportAssetPostValueSubCar implements FromView
{
    public $accessory_lot;
    public function __construct($accessory_lot)
    {
        $this->accessory_lot = $accessory_lot;
    }

    public function view(): View
    {
        return view('admin.asset-cars.excel.post-value-sub-car.export-template', [
            'accessory_lot' => $this->accessory_lot,
        ]);
    }
}
