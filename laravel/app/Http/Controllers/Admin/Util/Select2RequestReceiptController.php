<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\InstallEquipment;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\DriverWage;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Enums\WageCalType;
use App\Enums\DrivingJobStatusEnum;
use App\Models\Amphure;
use App\Models\Cradle;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2RequestReceiptController extends Controller
{
    function getProvince(Request $request)
    {
        $list = Province::select('id', 'name_th as name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name_th', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getAmphureByProvince(Request $request)
    {
        // dd($request->parent_id);
        $list = Amphure::select('amphures.id', 'amphures.name_th as name')
            ->leftJoin('provinces', 'provinces.id', '=', 'amphures.province_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('amphures.name_th', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('provinces.id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDistrictByAmphure(Request $request)
    {
        // dd($request->parent_id);
        $list = District::select('districts.id', 'districts.name_th as name')
            ->leftJoin('amphures', 'amphures.id', '=', 'districts.amphure_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('districts.name_th', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('amphures.id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                ];
            });
        return response()->json($list);
    }


    function getZipcode(Request $request)
    {
        $sub_district = District::find($request->customer_subdistrict_id);
        return [
            'success' => true,
            'data' => $sub_district
        ];
    }    


}
