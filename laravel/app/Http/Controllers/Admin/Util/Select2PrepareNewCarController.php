<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class Select2PrepareNewCarController extends Controller
{
    public function getPoData(Request $request)
    {
        $search = $request->s;
        $po_data = PurchaseOrder::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('po_no', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->po_no,
                ];
            });

        return $po_data;
    }


    public function getCarList(Request $request)
    {
        $search = $request?->s;
        $listCar = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('license_plate', 'like', '%' . $search . '%');
                $query_search->orWhere('engine_no', 'like', '%' . $search . '%');
                $query_search->orWhere('chassis_no', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($item) {
                $text = null;
                if ($item?->license_plate) {
                    $text = __('inspection_cars.license_plate') . ' ' . $item?->license_plate;
                } else if ($item?->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item?->engine_no;
                } else if ($item?->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item?->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($listCar);
    }
}
