<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomAccessory;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use App\Traits\LongTermRentalTrait;
use Illuminate\Http\Request;

class LongTermRentalSpecTorController extends Controller
{
    use LongTermRentalTrait;
    public function create(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        $lt_rental_id = $request->rental;
        $d = new LongTermRentalTor();
        $have_accessory_list = $this->getHaveAccessoryList();
        $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
        $page_title = __('lang.edit') .  __('long_term_rentals.specs_and_equipment');
        $data = [
            'd' => $d,
            'lt_rental_id' => $lt_rental_id,
            'page_title' => $page_title,
            'have_accessory_list' => $have_accessory_list,
            'redirect_route' => $redirect_route
        ];

        if (isset($request->accessory_controller)) {
            $data['accessory_controller'] = true;
            $data['redirect_route'] = route('admin.long-term-rental.specs.accessories.edit', ['rental' => $lt_rental_id]);
        }
        return view('admin.long-term-rental-spec-tors.form', $data);
    }

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->rental;
        $d = LongTermRentalTor::find($lt_rental_tor_id);
        $have_accessory_list = $this->getHaveAccessoryList();
        $tor_line_list = LongTermRentalTorLine::where('lt_rental_tor_id', $lt_rental_tor_id)
            ->get();

        $tor_line_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        // TODO
        $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
        $car_accessory = [];
        $index = 0;
        foreach ($tor_line_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }
        if (isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
            $page_title = __('lang.edit') .  __('long_term_rentals.specs_and_equipment');
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route
            ];
        } else {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
            $page_title = __('lang.edit') .  __('long_term_rentals.specs_and_equipment');
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route,
                // 'accessory_controller' => true,
            ];
        }
        return view('admin.long-term-rental-spec-tors.form', $data);
    }

    public function editCar(Request $request)
    {
        if (isset($request->accessory_controller)) {
            $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecsAccessory);
        } else {
            $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        }
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->rental;
        $d = LongTermRentalTor::find($lt_rental_tor_id);
        $have_accessory_list = $this->getHaveAccessoryList();
        $tor_line_list = LongTermRentalTorLine::where('id', $request->id)
            ->first();

        $tor_line_list->car_class_text = ($tor_line_list->carClass) ? $tor_line_list->carClass->full_name . ' - ' . $tor_line_list->carClass->name : '';
        $tor_line_list->car_color_text = ($tor_line_list->color) ? $tor_line_list->color->name : '';

        $car_accessory = LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $tor_line_list->id)->get();
        $car_accessory->map(function ($item) {
            $item->accessory_id = $item->accessory_id;
            $item->accessory_text = ($item->accessory) ? $item->accessory->name : '';;
            $item->amount_accessory = $item->amount;
            return $item;
        });

        $page_title = __('lang.edit') .  __('long_term_rentals.spec_equipment');

        if (isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id, 'accessory_controller' => true]);
            $accessory_controller = true;
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route,
                'accessory_controller' => $accessory_controller,
            ];
        } else {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route,
            ];
        }


        return view('admin.long-term-rental-spec-tors.form-car', $data);
    }

    public function showCar(Request $request)
    {
        if (isset($request->accessory_controller)) {
            $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecsAccessory);
        } else {
            $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpec);
        }
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->rental;
        $d = LongTermRentalTor::find($lt_rental_tor_id);
        $have_accessory_list = $this->getHaveAccessoryList();
        $tor_line_list = LongTermRentalTorLine::where('id', $request->id)
            ->first();

        $tor_line_list->car_class_text = ($tor_line_list->carClass) ? $tor_line_list->carClass->full_name . ' - ' . $tor_line_list->carClass->name : '';
        $tor_line_list->car_color_text = ($tor_line_list->color) ? $tor_line_list->color->name : '';

        $car_accessory = LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $tor_line_list->id)->get();
        $car_accessory->map(function ($item) {
            $item->accessory_id = $item->accessory_id;
            $item->accessory_text = ($item->accessory) ? $item->accessory->name : '';;
            $item->amount_accessory = $item->amount;
            return $item;
        });

        $redirect_route = route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id]);
        $page_title = __('lang.view') .  __('long_term_rentals.spec_equipment');
        if (isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id, 'accessory_controller' => true]);
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route,
                'accessory_controller' => true,
                'view_only' => true,
            ];
        } else {
            $redirect_route = route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id]);
            $data = [
                'd' => $d,
                'lt_rental_id' => $lt_rental_id,
                'lt_rental_tor_id' => $lt_rental_tor_id,
                'page_title' => $page_title,
                'have_accessory_list' => $have_accessory_list,
                'car_list' => $tor_line_list,
                'car_accessory' => $car_accessory,
                'redirect_route' => $redirect_route,
                'view_only' => true,
            ];
        }


        return view('admin.long-term-rental-spec-tors.form-car', $data);
    }

    public function getDataBomAccessory(Request $request)
    {
        $lists = BomAccessory::leftjoin('accessories', 'accessories.id', '=', 'bom_accessories.accessories_id')
            ->where('bom_accessories.bom_id', $request->id)
            ->get();
        return response()->json([
            'lists' => $lists,
        ]);
    }

    public function show(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpec);
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->rental;
        $d = LongTermRentalTor::find($lt_rental_tor_id);
        $have_accessory_list = $this->getHaveAccessoryList();
        $tor_line_list = LongTermRentalTorLine::where('lt_rental_tor_id', $lt_rental_tor_id)
            ->get();

        $tor_line_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        // TODO
        $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
        $car_accessory = [];
        $index = 0;
        foreach ($tor_line_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories;'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        $redirect_route = route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id]);
        if (isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.show', ['rental' => $lt_rental_id, 'accessory_controller' => true]);
        }
        $page_title = __('lang.view') .  __('long_term_rentals.specs_and_equipment');
        return view('admin.long-term-rental-spec-tors.form', [
            'd' => $d,
            'lt_rental_id' => $lt_rental_id,
            'lt_rental_tor_id' => $lt_rental_tor_id,
            'page_title' => $page_title,
            'have_accessory_list' => $have_accessory_list,
            'car_list' => $tor_line_list,
            'car_accessory' => $car_accessory,
            'view_only' => true,
            'redirect_route' => $redirect_route,

        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        $lt_rental_id =  $request->lt_rental_id;
        $lt_rental_tor = LongTermRentalTor::firstOrNew(['id' => $request->id]);
        $lt_rental_tor->remark_tor = $request->remark_tor;
        $lt_rental_tor->lt_rental_id = $lt_rental_id;
        $lt_rental_tor->save();
        if ($lt_rental_tor->id) {
            if (isset($request->pending_delete_car_ids)) {
                LongTermRentalTorLine::whereIn('id', $request->pending_delete_car_ids)->delete();
            }
            $long_term_car_accessory = $this->saveCarAccessory($request, $lt_rental_tor->id);
        }

        $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
        if (isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.accessories.edit', ['rental' => $lt_rental_id]);
        }
        return $this->responseValidateSuccess($redirect_route);
    }

    // spec accessory update
    public function updateAccessory(Request $request)
    {
        $lt_rental_id =  $request->lt_rental_id;
        $lt_rental_tor_line = LongTermRentalTorLine::find($request->id);
        LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $lt_rental_tor_line->id)->delete();
        if (isset($request->accessories) && sizeof($request->accessories) > 0) {

            foreach ($request->accessories as $accessory_key => $accessory) {
                $long_term_accessory = new LongTermRentalTorLineAccessory();
                $long_term_accessory->lt_rental_tor_line_id = $lt_rental_tor_line->id;
                $long_term_accessory->accessory_id = $accessory['accessory_id'];
                $long_term_accessory->amount = intval($accessory['accessory_amount']);
                $long_term_accessory->tor_section = $accessory['tor_section'];
                $long_term_accessory->remark = $accessory['remark'];
                $long_term_accessory->type_accessories = $accessory['type_accessories'];
                $long_term_accessory->save();
            }
        }


        if (isset($request->accessory_controller)) {
            $data['accessory_controller'] = true;
            $redirect_route = route('admin.long-term-rental.specs.accessories.edit', ['rental' => $lt_rental_id]);
        } else {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $lt_rental_id]);
        }
        return $this->responseValidateSuccess($redirect_route);
    }


    private function saveCarAccessory($request, $lt_rental_tor_id)
    {
        if (!empty($request->cars)) {
            foreach ($request->cars as $car_index => $request_car) {
                if (isset($request_car['id'])) {
                    $lt_rental_tor_line = LongTermRentalTorLine::find($request_car['id']);
                } else {
                    $lt_rental_tor_line = new LongTermRentalTorLine();
                }
                $lt_rental_tor_line->lt_rental_tor_id = $lt_rental_tor_id;
                $lt_rental_tor_line->car_class_id = $request_car['car_class_id'];
                $lt_rental_tor_line->car_color_id = $request_car['car_color_id'];
                $lt_rental_tor_line->amount = intval($request_car['amount_car']);
                $lt_rental_tor_line->remark = $request_car['remark'];
                $lt_rental_tor_line->have_accessories = $request_car['have_accessories'];
                $lt_rental_tor_line->save();

                // save accessory
                LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $lt_rental_tor_line->id)->delete();
                if (isset($request_car['accessory']) && sizeof($request_car['accessory']) > 0) {
                    foreach ($request_car['accessory'] as $accessory_key => $accessory) {
                        $long_term_accessory = new LongTermRentalTorLineAccessory();
                        $long_term_accessory->lt_rental_tor_line_id = $lt_rental_tor_line->id;
                        $long_term_accessory->accessory_id = $accessory['id'];
                        $long_term_accessory->amount = intval($accessory['amount']);
                        $long_term_accessory->amount_per_car = intval($accessory['amount_per_car']);
                        $long_term_accessory->tor_section = isset($accessory['tor_section']) ? $accessory['tor_section'] : null;
                        $long_term_accessory->remark = $accessory['remark'];
                        $long_term_accessory->type_accessories = $accessory['type_accessories'];
                        $long_term_accessory->save();
                    }
                }
            }
        }
        return true;
    }
}
