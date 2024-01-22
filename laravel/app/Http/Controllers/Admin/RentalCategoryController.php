<?php

namespace App\Http\Controllers\Admin;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $s = $request->s;
        $service_type_list = DB::table('rental_categories_service_types')
            ->leftJoin('service_types', 'service_types.id', '=', 'rental_categories_service_types.service_type_id')
            ->select('rental_categories_service_types.rental_category_id', DB::raw("group_concat(service_types.name SEPARATOR ', ') as service_type_name"))
            ->groupBy('rental_categories_service_types.rental_category_id');

        $list = RentalCategory::select('rental_categories.name', 'rental_categories.id', 'rental_categories.status', 'service_type_list.service_type_name')
            ->leftJoin('rental_categories_service_types as rantal_cat', 'rantal_cat.rental_category_id', '=', 'rental_categories.id')
            ->leftJoin('service_types as st2', 'st2.id', '=', 'rantal_cat.service_type_id')
            ->leftjoinSub($service_type_list, 'service_type_list', function ($join) {
                $join->on('service_type_list.rental_category_id', '=', 'rental_categories.id');
            })
            ->when($s, function ($query) use ($s) {
                $query->where('st2.name', 'like', '%' . $s . '%');
                $query->orWhere('rental_categories.name', 'like', '%' . $s . '%');
            })
            ->sortable('name')
            ->groupBy(
                'rental_categories.name',
                'rental_categories.id',
                'rental_categories.status',
                'service_type_list.service_type_name'
            )
            ->paginate(PER_PAGE);

        return view('admin.rental-categories.index', [
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
        $d = new RentalCategory();
        $d->status = STATUS_ACTIVE;
        $service_type_list = ServiceType::all();
        $listStatus = $this->getListStatus();
        $service_type = [];
        $page_title = __('lang.create') . __('rental_categories.page_title');
        return view('admin.rental-categories.form', [
            'd' => $d,
            'page_title' => $page_title,
            'service_type_list' => $service_type_list,
            'listStatus' => $listStatus,
            'service_type' => $service_type
        ]);
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
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('rental_categories', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'status' => ['required'],
            'service_type' => ['required'],
        ], [], [
            'name' => __('rental_categories.name'),
            'status' => __('rental_categories.status'),
            'service_type' => __('rental_categories.service_category')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental_categories = RentalCategory::firstOrNew(['id' => $request->id]);
        $rental_categories->name = $request->name;
        $rental_categories->status = $request->status;
        $rental_categories->save();


        if ($rental_categories->id) {
            $rental_categories_relation = $this->saveRentalCategoryServiceTypeRelation($request, $rental_categories->id);
        }

        $redirect_route = route('admin.rental-categories.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveRentalCategoryServiceTypeRelation($request, $rental_category_id)
    {
        RentalCategoryServiceTypes::where('rental_category_id', $rental_category_id)->delete();
        if (!empty($request->service_type)) {
            foreach ($request->service_type as $service_type) {
                $rental_category_relation = new RentalCategoryServiceTypes();
                $rental_category_relation->rental_category_id = $rental_category_id;
                $rental_category_relation->service_type_id = $service_type;
                $rental_category_relation->save();
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(RentalCategory $rental_category)
    {
        $service_type = $this->getServiceTypeArray($rental_category->id);
        $service_type_list = ServiceType::all();
        $listStatus = $this->getListStatus();
        $page_title = __('lang.view') . __('rental_categories.page_title');
        $view = true;
        return view('admin.rental-categories.form', [
            'd' => $rental_category,
            'page_title' => $page_title,
            'view' => $view,
            'service_type_list' => $service_type_list,
            'service_type' => $service_type,
            'listStatus' => $listStatus,
        ]);
    }

    public function getServiceTypeArray($rental_category)
    {
        return RentalCategoryServiceTypes::leftJoin('service_types', 'service_types.id', '=', 'rental_categories_service_types.service_type_id')
            ->select('service_types.id as id', 'service_types.name as name')
            ->where('rental_categories_service_types.rental_category_id', $rental_category)
            ->pluck('rental_categories.id')
            ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(RentalCategory $rental_category)
    {
        $service_type = $this->getServiceTypeArray($rental_category->id);

        $service_type_list = ServiceType::all();
        $listStatus = $this->getListStatus();
        $page_title = __('lang.edit') . __('rental_categories.page_title');
        return view('admin.rental-categories.form', [
            'd' => $rental_category,
            'page_title' => $page_title,
            'service_type_list' => $service_type_list,
            'service_type' => $service_type,
            'listStatus' => $listStatus,
        ]);
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
        $rental_categories = RentalCategory::find($id);
        $rental_categories->delete();

        return $this->responseComplete();
    }
}
