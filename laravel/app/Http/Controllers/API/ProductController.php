<?php

namespace App\Http\Controllers\API;

use App\Enums\CalculateTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductAdditionalRelation;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Product::leftjoin('branches', 'branches.id', '=', 'products.branch_id')
            ->leftjoin('service_types', 'service_types.id', '=', 'products.service_type_id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'service_types.id as service_type_id',
                'service_types.name as service_type_name',
                'branches.id as branch_id',
                'branches.name as branch_name',
                'products.standard_price',
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('products.name', 'like', '%' . $s . '%');
                    $q->orWhere('products.sku', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($request->service_type_id), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('service_types.id', $request->service_type_id);
                });
            })
            ->when(!empty($request->branch_id), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('branches.id', $request->branch_id);
                });
            })
            ->where('products.is_used_application', '1')
            ->where('products.status', STATUS_ACTIVE)
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Product::leftjoin('branches', 'branches.id', '=', 'products.branch_id')
            ->leftjoin('service_types', 'service_types.id', '=', 'products.service_type_id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'service_types.id as service_type_id',
                'service_types.name as service_type_name',
                'products.calculate_type',
                'products.standard_price',
                'branches.id as branch_id',
                'branches.name as branch_name',
                'products.booking_day_mon',
                'products.booking_day_tue',
                'products.booking_day_wed',
                'products.booking_day_thu',
                'products.booking_day_fri',
                'products.booking_day_sat',
                'products.booking_day_sun',
                'products.start_booking_time',
                'products.end_booking_time',
                'products.reserve_booking_duration',
                'products.start_date',
                'products.end_date',
                'products.fix_days',
                /* 'products.fix_return_time', */
            )
            ->where('products.id', $request->id)
            ->where('products.is_used_application', '1')
            ->where('products.status', STATUS_ACTIVE)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $product_additionals = ProductAdditional::select('product_additionals.id', 'product_additionals.name', 'product_additionals.price', 'product_additionals.is_stock', 'product_additionals.amount as stock', 'products_addtionals_relation.amount as product_amount', 'products_addtionals_relation.is_free')
            ->join('products_addtionals_relation', 'products_addtionals_relation.product_addtional_id', '=', 'product_additionals.id')
            ->get();
        $data->product_additionals = $product_additionals;

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
