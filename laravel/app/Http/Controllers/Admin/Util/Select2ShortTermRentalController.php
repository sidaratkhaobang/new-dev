<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\Location;
use App\Models\Province;
use App\Models\RentalCheckIn;
use Illuminate\Http\Request;

class Select2ShortTermRentalController extends Controller
{
    public function getCustomerType(Request $request)
    {
        $search = $request->s;
        $customer_group_arrs = CustomerGroup::where('status', STATUS_ACTIVE)
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('name', 'Like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item?->id,
                    'text' => $item?->name
                ];
            });
        return $customer_group_arrs;
    }

    public function getProvince(Request $request)
    {
        $search = $request->s;
        $province_data_arrs = Province::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('name_th', 'Like', '%' . $search . '%');
            $query_search->orwhere('name_en', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item?->id,
                    'text' => $item?->name_th,
                ];
            });
        return $province_data_arrs;
    }

    public function getLocation(Request $request)
    {
        $search = $request->search ? $request->search : $request->s;
        $location_data = Location::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('name', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item?->id,
                    'text' => $item?->name,
                ];
            });
        return $location_data;
    }
}
