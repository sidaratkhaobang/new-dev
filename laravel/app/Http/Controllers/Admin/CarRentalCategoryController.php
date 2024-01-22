<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CarRentalCategory;
use App\Models\Car;
use App\Models\RentalCategory;
use Illuminate\Support\Facades\Validator;

class CarRentalCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $s = $request->s;
        $license_plate = $request->license_plate;
        $engine_no = $request->engine_no;
        $chassis_no = $request->chassis_no;
        $list = Car::select('engine_no','chassis_no','license_plate','id')
                ->sortable('engine_no')
                ->search($s, $request)
                ->paginate(PER_PAGE);

        $license_plate_list = Car::select('license_plate as name', 'id')->orderBy('license_plate')->get();
        $engine_no_list = Car::select('engine_no as name', 'id')->orderBy('engine_no')->get();
        $chassis_no_list = Car::select('chassis_no as name', 'id')->orderBy('chassis_no')->get();
        return view('admin.car-rental-categories.index', [
            'car_id' => $request->car_id,
            'engine_no_id' => $request->engine_no,
            'chassis_no_id' => $request->chassis_no,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'license_plate' => $license_plate,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'list' => $list,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('admin.car-rental-categories.form', [
        //     // 'list' => $list,
        //     // 's' => $request->s,
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rental_category' => ['required'],
        ], [], [
            'rental_category' => __('car_rental_categories.rental_category')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_rental_id = CarRentalCategory::where('car_id',$request->id)->first();
        if ($car_rental_id != null) {
        CarRentalCategory::where('car_id', $request->id)->delete();
        }
        if (!empty($request->rental_category)) {
            foreach ($request->rental_category as $rental_category) {
                $car_rental_category = new CarRentalCategory();
                $car_rental_category->car_id = $request->id;
                $car_rental_category->rental_category_id = $rental_category;
                $car_rental_category->save();
            }
        }
    
        $redirect_route = route('admin.car-rental-categories.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Car $car_rental_category)
    {
        $car_rental_category2 = $this->getRentalCategoryArray($car_rental_category->id);
        $rental_categories_list = RentalCategory::all();
        $page_title = __('lang.view') . __('car_rental_categories.page_title');
        $view = true;
        return view('admin.car-rental-categories.form', [
            'd' => $car_rental_category,
            'view' => $view,
            'page_title' => $page_title,
            'rental_category_list' => $rental_categories_list,
            'car_rental_category' => $car_rental_category2,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Car $car_rental_category)
    {
        $car_rental_category2 = $this->getRentalCategoryArray($car_rental_category->id);
        $rental_categories_list = RentalCategory::all();
        $page_title = __('lang.edit') . __('car_rental_categories.page_title');
        return view('admin.car-rental-categories.form', [
            'd' => $car_rental_category,
            'page_title' => $page_title,
            'rental_category_list' => $rental_categories_list,
            'car_rental_category' => $car_rental_category2,
        ]);
    }

    public function getRentalCategoryArray($car_rental_category)
    {
       return CarRentalCategory::leftJoin('cars', 'cars.id', '=', 'cars_rental_categories.car_id')
        ->leftJoin('rental_categories', 'rental_categories.id', '=', 'cars_rental_categories.rental_category_id')
        ->select('rental_categories.id as id','rental_categories.name as name')
        ->where('cars.id', $car_rental_category)
        ->pluck('cars_rental_categories.id')
        ->toArray();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
