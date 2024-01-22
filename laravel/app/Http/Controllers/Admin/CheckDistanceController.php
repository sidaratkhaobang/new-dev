<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CheckDistance;
use App\Models\CheckDistanceLine;
use App\Models\CarBrand;
use App\Models\CarClass;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\RepairTrait;

class CheckDistanceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckDistance);
        $car_brand_id = $request->car_brand_id;
        $car_class_id = $request->car_class_id;

        $list = CheckDistance::select('check_distances.car_class_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'check_distances.car_class_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->where(function ($q) use ($car_brand_id, $car_class_id,) {
                if (!is_null($car_brand_id)) {
                    $q->where('car_brands.id', $car_brand_id);
                }
                if (!is_null($car_class_id)) {
                    $q->where('check_distances.car_class_id', $car_class_id);
                }
            })
            ->groupBy('car_class_id')->paginate(PER_PAGE);

        $list->map(function ($item) {
            $item->car_brand = ($item->carClass && $item->carClass->carType && $item->carClass->carType->car_brand) ? $item->carClass->carType->car_brand->name : null;
            $item->car_class = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : null;
            $sub_check = CheckDistance::where('car_class_id', $item->car_class_id)
                ->orderBy('distance')
                ->get();
            $item->sub_check = $sub_check;
            $sub_check->map(function ($item_line) use ($item) {
                $sub_check_line = CheckDistanceLine::where('check_distance_lines.check_distance_id', $item_line->id)
                    ->leftJoin('repair_lists', 'repair_lists.id', '=', 'check_distance_lines.repair_list_id')
                    ->select(
                        'repair_lists.code',
                        'repair_lists.name',
                        'check_distance_lines.id',
                        'check_distance_lines.price',
                        'check_distance_lines.check',
                        'check_distance_lines.remark'
                    )
                    ->orderBy('repair_lists.code')
                    ->get();
                $item_line->sub_check_line = $sub_check_line;
                $item->id = $item_line->id;
                return $item_line;
            });
            return $item;
        });

        $car_brand = CarBrand::find($car_brand_id);
        $car_brand_name = ($car_brand) ? $car_brand->name : null;
        $car_class = CarClass::find($car_class_id);
        $car_class_name = ($car_class) ? $car_class->full_name . ' - ' . $car_class->name : null;

        $page_title = __('check_distances.page_title');
        return view('admin.check-distances.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
            'car_brand_name' => $car_brand_name,
            'car_class_name' => $car_class_name,
            'car_brand_id' => $car_brand_id,
            'car_class_id' => $car_class_id,
        ]);
    }

    public function create()
    {
        $d = new CheckDistance();
        $d->car_brand_id = null;
        $check_list = RepairTrait::getCheckList();
        $repair_lists = RepairTrait::getRepairListId();
        $page_title = __('lang.create') . __('check_distances.page_title');
        return view('admin.check-distances.form', [
            'd' => $d,
            'page_title' => $page_title,
            'car_brand_name' => null,
            'car_class_name' => null,
            'check_list' => $check_list,
            'repair_lists' => $repair_lists,
        ]);
    }

    public function edit(CheckDistance $check_distance)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CheckDistance);
        $check_distances = CheckDistance::where('car_class_id', $check_distance->car_class_id)
            ->orderBy('distance')
            ->get();
        $check_distances->map(function ($item) {
            $check_line = CheckDistanceLine::where('check_distance_lines.check_distance_id', $item->id)
                ->leftJoin('repair_lists', 'repair_lists.id', '=', 'check_distance_lines.repair_list_id')
                ->select(
                    'check_distance_lines.repair_list_id',
                    'check_distance_lines.id',
                    'check_distance_lines.price',
                    'check_distance_lines.check',
                    'check_distance_lines.remark'
                )
                ->orderBy('repair_lists.code')
                ->get()->map(function ($item_line) {
                    $item_line->code = $item_line->repair_list_id;
                    return $item_line;
                });
            $item->check_line = $check_line;
            return $item;
        });
        $check_list = RepairTrait::getCheckList();
        $car_brand_name = ($check_distance->carClass && $check_distance->carClass->carType && $check_distance->carClass->carType->car_brand) ? $check_distance->carClass->carType->car_brand->name : null;
        $car_class_name = ($check_distance->carClass) ? $check_distance->carClass->full_name . ' - ' . $check_distance->carClass->name : null;
        $check_distance->car_brand_id = ($check_distance->carClass && $check_distance->carClass->carType && $check_distance->carClass->carType->car_brand) ? $check_distance->carClass->carType->car_brand->id : null;

        $page_title =  __('lang.edit') . __('check_distances.page_title');
        return view('admin.check-distances.form', [
            'd' => $check_distance,
            'page_title' => $page_title,
            'check_distances' => $check_distances,
            'check_list' => $check_list,
            'car_brand_name' => $car_brand_name,
            'car_class_name' => $car_class_name,
            'edit' => true,
        ]);
    }

    public function show(CheckDistance $check_distance)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckDistance);
        $check_distances = CheckDistance::where('car_class_id', $check_distance->car_class_id)
            ->orderBy('distance')
            ->get();
        $check_distances->map(function ($item) {
            $check_line = CheckDistanceLine::where('check_distance_lines.check_distance_id', $item->id)
                ->leftJoin('repair_lists', 'repair_lists.id', '=', 'check_distance_lines.repair_list_id')
                ->select(
                    'check_distance_lines.repair_list_id',
                    'check_distance_lines.id',
                    'check_distance_lines.price',
                    'check_distance_lines.check',
                    'check_distance_lines.remark'
                )
                ->orderBy('repair_lists.code')
                ->get()->map(function ($item_line) {
                    $item_line->code = $item_line->repair_list_id;
                    return $item_line;
                });
            $item->check_line = $check_line;
            return $item;
        });
        $check_list = RepairTrait::getCheckList();
        $repair_lists = RepairTrait::getRepairListId();
        $car_brand_name = ($check_distance->carClass && $check_distance->carClass->carType && $check_distance->carClass->carType->car_brand) ? $check_distance->carClass->carType->car_brand->name : null;
        $car_class_name = ($check_distance->carClass) ? $check_distance->carClass->full_name . ' - ' . $check_distance->carClass->name : null;
        $check_distance->car_brand_id = ($check_distance->carClass && $check_distance->carClass->carType && $check_distance->carClass->carType->car_brand) ? $check_distance->carClass->carType->car_brand->id : null;

        $page_title =  __('lang.view') . __('check_distances.page_title');
        return view('admin.check-distances.form', [
            'd' => $check_distance,
            'page_title' => $page_title,
            'check_distances' => $check_distances,
            'check_list' => $check_list,
            'car_brand_name' => $car_brand_name,
            'car_class_name' => $car_class_name,
            'repair_lists' => $repair_lists,
            'view' => true,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CheckDistance);
        $check_distance = CheckDistance::find($id);
        $check_distance_id = CheckDistance::where('car_class_id', $check_distance->car_class_id)->pluck('id');
        CheckDistance::whereIn('id', $check_distance_id)->delete();
        CheckDistanceLine::whereIn('check_distance_id', $check_distance_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->authorize(Actions::Manage . '_' . Resources::CheckDistance);
        $validator = Validator::make($request->all(), [
            'car_brand_id' => [
                'required',
            ],
            'car_class_id' => [
                'required',
            ],
            'data_distances' => [
                'required', 'array', 'min:1'
            ],
            'data_distances.*.distance' => [
                'required',
            ],

        ], [], [
            'car_brand_id' => __('car_classes.car_brand'),
            'car_class_id' =>  __('car_classes.class'),
            'data_distances' => __('check_distances.distance_table'),
            'data_distances.*.distance' => __('check_distances.distance'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->del_section != null) {
            CheckDistance::whereIn('id', $request->del_section)->delete();
            CheckDistanceLine::whereIn('check_distance_id', $request->del_section)->delete();
        }
        if ($request->del_checklist != null) {
            CheckDistanceLine::whereIn('id', $request->del_checklist)->delete();
        }
        if (!empty($request->data_distances)) {
            foreach ($request->data_distances as $distance_index => $request_distance) {
                if (isset($request_distance['id'])) {
                    $check_distance = CheckDistance::find($request_distance['id']);
                } else {
                    $check_distance = new CheckDistance();
                }
                // save distance
                $check_distance->car_class_id = $request->car_class_id;
                $check_distance->distance = $request_distance['distance'];
                $check_distance->month = $request_distance['month'];
                $check_distance->save();

                // save distance line
                if (isset($request_distance['check_line']) && sizeof($request_distance['check_line']) > 0) {
                    foreach ($request_distance['check_line'] as $distance_line_key => $distance_line) {
                        if (isset($distance_line['id'])) {
                            $check_distance_line = CheckDistanceLine::find($distance_line['id']);
                        } else {
                            $check_distance_line = new CheckDistanceLine();
                        }
                        $check_distance_line->check_distance_id = $check_distance->id;
                        $check_distance_line->repair_list_id = $distance_line['code'];
                        $check_distance_line->check = $distance_line['check'];
                        $check_distance_line->price = ($distance_line['price']) ? $distance_line['price'] : '0';
                        $check_distance_line->remark = $distance_line['remark'];
                        $check_distance_line->save();
                    }
                }
            }
        }

        $redirect_route = route('admin.check-distances.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    function selectCarClass(Request $request)
    {
        $car_class_arr = CheckDistance::groupBy('car_class_id')->pluck('car_class_id')->toArray();
        $car_class = CarClass::leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->whereNotIn('car_classes.id', $car_class_arr)
            ->select('car_classes.id', 'car_classes.name', 'car_classes.full_name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_brands.id', $request->parent_id);
                }
            })
            ->orderBy('car_classes.name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }

    public function copyCheckDistance(Request $request)
    {
        $car_class_copy = $request->car_class_copy;
        $car_class_new = $request->car_class_id;
        $check_distance_old = CheckDistance::where('car_class_id', $car_class_copy)->get();

        if (!empty($car_class_new)) {
            //duplicate data
            foreach ($check_distance_old as $key1 => $item_old) {
                $check_distance_new = $item_old->replicate();
                $check_distance_new->car_class_id = $car_class_new;
                $check_distance_new->save();

                //duplicate check distance line
                $check_distance_line_old = CheckDistanceLine::where('check_distance_id', $item_old->id)->orderBy('id')->get();
                if ($check_distance_line_old) {
                    foreach ($check_distance_line_old as $key2 => $item_line_old) {
                        $check_distance_line_new = $item_line_old->replicate();
                        $check_distance_line_new->check_distance_id = $check_distance_new->id;
                        $check_distance_line_new->save();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'redirect' => route('admin.check-distances.index'),
            ]);
        }
    }
}
