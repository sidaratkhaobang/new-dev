<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Province::select(
                'provinces.id',
                /* 'provinces.code', */
                'provinces.name_th',
                'provinces.name_en',
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('provinces.id', 'like', '%' . $s . '%');
                    /* $q->orWhere('provinces.code', 'like', '%' . $s . '%'); */
                    $q->orWhere('provinces.name_th', 'like', '%' . $s . '%');
                    $q->orWhere('provinces.name_en', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list,200);
    }
}
