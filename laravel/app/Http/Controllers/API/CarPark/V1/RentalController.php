<?php

namespace App\Http\Controllers\API\CarPark\V1;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Rental::select([
            'rentals.id',
            'rentals.worksheet_no',
            'rentals.rental_type',
            'rentals.service_type_id',
            'service_types.name as service_type_name',
            'rentals.pickup_date',
            'rentals.return_date',
            'rentals.branch_id',
            'branches.name as branch_name',
            'rentals.product_id',
            'products.name as product_name',
        ])
            ->leftjoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftjoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->when(!empty($s), function ($query) use ($s) {
                $query->where('rentals.worksheet_no', 'like', '%' . $s . '%');
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read($id)
    {
        $data = Rental::select([
            'rentals.id',
            'rentals.worksheet_no',
            'rentals.rental_type',
            'rentals.service_type_id',
            'service_types.name as service_type_name',
            'rentals.pickup_date',
            'rentals.return_date',
            'rentals.branch_id',
            'branches.name as branch_name',
            'rentals.product_id',
            'products.name as product_name',
        ])
            ->leftjoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftjoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->where('rentals.id', $id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
