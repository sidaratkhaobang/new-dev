<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\LongTermRentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomAccessory;
use App\Models\BomLine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LongTermRentalBomController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalBom);  
        $worksheet_no = $request->worksheet_no;
        $type = $request->type;
        $worksheet_list = Bom::select('id', 'worksheet_no as name')->get();
        $type_list = $this->getTypeList();
        $list = Bom::sortable(['created_at' => 'desc'])
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        return view('admin.long-term-rental-boms.index', [
            'list' => $list,
            's' => $request->s,
            'worksheet_no' => $worksheet_no,
            'worksheet_list' => $worksheet_list,
            'type_list' => $type_list,
            'type' => $type,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalBom);
        $d = new Bom();
        $type_lists = $this->getTypeList();
        $page_title = __('lang.add') . ' ' . __('long_term_rental_boms.bom');

        return view('admin.long-term-rental-boms.form', [
            'd' => $d,
            'page_title' => $page_title,
            'type_lists' => $type_lists,
        ]);
    }

    public static function getTypeList()
    {
        $type_lists = collect([
            (object) [
                'id' => LongTermRentalTypeEnum::CAR,
                'value' => LongTermRentalTypeEnum::CAR,
                'name' => __('long_term_rental_boms.car'),
            ],
            (object) [
                'id' => LongTermRentalTypeEnum::ACCESSORY,
                'value' => LongTermRentalTypeEnum::ACCESSORY,
                'name' => __('long_term_rental_boms.accessory'),
            ],
        ]);
        return $type_lists;
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalBom);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('boms', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'type' => [
                Rule::when(!isset($request->type_hidden), ['required']),
            ],
        ], [], [
            'name' => __('long_term_rental_boms.name'),
            'type' => __('long_term_rental_boms.type'),
            'pr_car.*.car_class_id' => __('car_inspections.section_topic'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (($request->type == LongTermRentalTypeEnum::CAR || $request->type_hidden == LongTermRentalTypeEnum::CAR) && !isset($request->pr_car)) {
            return $this->responseWithCode(false, 'กรุณากรอก ข้อมูลรถ', null, 422);
        }

        if (($request->type == LongTermRentalTypeEnum::ACCESSORY || $request->type_hidden == LongTermRentalTypeEnum::ACCESSORY) && !isset($request->accessories)) {
            return $this->responseWithCode(false, 'กรุณากรอก ข้อมูลอุปกรณ์', null, 422);
        }

        $long_term_rental_bom = Bom::firstOrNew(['id' => $request->id]);
        $long_term_rental_bom_count = Bom::all()->count() + 1;
        $prefix = 'LR-BOM-';
        if (!($long_term_rental_bom->exists)) {
            $long_term_rental_bom->worksheet_no = generateRecordNumber($prefix, $long_term_rental_bom_count);
        }
        $long_term_rental_bom->name = $request->name;
        if (!isset($request->edit)) {
            $long_term_rental_bom->type = $request->type;
        }
        $long_term_rental_bom->remark = $request->remark;
        $long_term_rental_bom->save();

        if ($request->type == LongTermRentalTypeEnum::CAR || $request->type_hidden == LongTermRentalTypeEnum::CAR) {
            $bom_line_del = BomLine::where('bom_id', $long_term_rental_bom->id)->delete();
            foreach ($request->pr_car as $data) {
                $long_term_rental_bom_line = new BomLine();
                $long_term_rental_bom_line->bom_id = $long_term_rental_bom->id;
                $long_term_rental_bom_line->car_class_id = $data['car_class_id'];
                $long_term_rental_bom_line->car_color_id = $data['car_color_id'];
                $long_term_rental_bom_line->amount = $data['amount_car'];
                $long_term_rental_bom_line->remark = $data['remark'];
                $long_term_rental_bom_line->save();
            }
        } else if ($request->type == LongTermRentalTypeEnum::ACCESSORY || $request->type_hidden == LongTermRentalTypeEnum::ACCESSORY) {
            $bom_accessorie_del = BomAccessory::where('bom_id', $long_term_rental_bom->id)->delete();
            foreach ($request->accessories as $data) {
                $long_term_rental_bom_accessory = new BomAccessory();
                $long_term_rental_bom_accessory->bom_id = $long_term_rental_bom->id;
                $long_term_rental_bom_accessory->accessories_id = $data['accessory_id'];
                $long_term_rental_bom_accessory->amount = $data['accessory_amount'];
                $long_term_rental_bom_accessory->save();
            }
        }
        $redirect_route = route('admin.long-term-rental-boms.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Bom $long_term_rental_bom)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalBom);
        $type_lists = $this->getTypeList();
        $pr_car_list = BomLine::where('bom_id', $long_term_rental_bom->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            return $item;
        });
        $accessories = BomAccessory::where('bom_id', $long_term_rental_bom->id)->get();
        $accessories->map(function ($item) {
            $item->accessory_text = ($item->carAccessory) ? $item->carAccessory->name : '';
            $item->accessory_id = $item->accessories_id;
            $item->amount_accessory = $item->amount;
            return $item;
        });
        $page_title = __('lang.view') . ' ' . __('long_term_rental_boms.bom');
        // dd($long_term_rental_bom);
        return view('admin.long-term-rental-boms.form', [
            'd' => $long_term_rental_bom,
            'page_title' => $page_title,
            'type_lists' => $type_lists,
            'pr_car_list' => $pr_car_list,
            'accessories' => $accessories,
            'view' => true,
        ]);
        // dd($long_term_rental_bom);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Bom $long_term_rental_bom)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalBom);
        $type_lists = $this->getTypeList();
        $pr_car_list = BomLine::where('bom_id', $long_term_rental_bom->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            return $item;
        });
        $accessories = BomAccessory::where('bom_id', $long_term_rental_bom->id)->get();
        $accessories->map(function ($item) {
            $item->accessory_text = ($item->carAccessory) ? $item->carAccessory->name : '';
            $item->accessory_id = $item->accessories_id;
            $item->amount_accessory = $item->amount;
            return $item;
        });
        // dd($accessories);
        $page_title = __('lang.edit') . ' ' . __('long_term_rental_boms.bom');
        // dd($long_term_rental_bom);
        return view('admin.long-term-rental-boms.form', [
            'd' => $long_term_rental_bom,
            'page_title' => $page_title,
            'type_lists' => $type_lists,
            'pr_car_list' => $pr_car_list,
            'accessories' => $accessories,
            'edit' => true,
        ]);
        // dd($long_term_rental_bom);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalBom);
        $long_term_rental_bom = Bom::find($id);
        $long_term_rental_bom->delete();

        return $this->responseComplete();
    }
}
