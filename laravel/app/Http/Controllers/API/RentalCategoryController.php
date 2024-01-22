<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use App\Models\RentalCategory;
use App\Models\RentalCategoryServiceTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class RentalCategoryController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $service_type_list = DB::table('rental_categories_service_types')
            ->leftJoin('service_types', 'service_types.id', '=', 'rental_categories_service_types.service_type_id')
            ->select(
                'rental_categories_service_types.rental_category_id',
                DB::raw("group_concat(service_types.name SEPARATOR ', ') as service_type_name"),
                DB::raw("group_concat(service_types.id SEPARATOR ', ') as service_type_id")
            )
            ->groupBy('rental_categories_service_types.rental_category_id');

        $list = RentalCategory::select('rental_categories.id', 'rental_categories.name')
            ->leftJoin('rental_categories_service_types as rantal_cat', 'rantal_cat.rental_category_id', '=', 'rental_categories.id')
            ->leftJoin('service_types as st2', 'st2.id', '=', 'rantal_cat.service_type_id')
            ->leftjoinSub($service_type_list, 'service_type_list', function ($join) {
                $join->on('service_type_list.rental_category_id', '=', 'rental_categories.id');
            })
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('rental_categories.name', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($request->service_type_id), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('st2.id', 'like', '%' . $request->service_type_id . '%');
                });
            })
            ->groupBy(
                'rental_categories.name',
                'rental_categories.id'
            )
            ->paginate(PER_PAGE);

        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $service_type_list = DB::table('rental_categories_service_types')
            ->leftJoin('service_types', 'service_types.id', '=', 'rental_categories_service_types.service_type_id')
            ->select(
                'rental_categories_service_types.rental_category_id',
                DB::raw("group_concat(service_types.name SEPARATOR ', ') as service_type_name"),
                DB::raw("group_concat(service_types.id SEPARATOR ', ') as service_type_id")
            )
            ->groupBy('rental_categories_service_types.rental_category_id');

        $data = RentalCategory::select('rental_categories.id', 'rental_categories.name')
            ->leftJoin('rental_categories_service_types as rantal_cat', 'rantal_cat.rental_category_id', '=', 'rental_categories.id')
            ->leftJoin('service_types as st2', 'st2.id', '=', 'rantal_cat.service_type_id')
            ->leftjoinSub($service_type_list, 'service_type_list', function ($join) {
                $join->on('service_type_list.rental_category_id', '=', 'rental_categories.id');
            })
            ->groupBy(
                'rental_categories.name',
                'rental_categories.id'
            )
            ->where('rental_categories.id', $request->id)->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
