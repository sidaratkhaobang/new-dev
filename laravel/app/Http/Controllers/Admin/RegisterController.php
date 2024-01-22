<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\AssetCarStatusEnum;
use App\Enums\FaceSheetStatusEnum;
use App\Enums\FaceSheetTypeEnum;
use App\Enums\LockLicensePlateTypeEnum;
use App\Enums\RegisterColorEnum;
use App\Enums\RegisterSignEnum;
use App\Enums\RegisterSignTypeEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportRegisterAvance;
use App\Exports\ExportRegisterFaceSheet;
use App\Exports\ExportRegisterTemplate;
use App\Http\Controllers\Controller;
use App\Models\AssetCar;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarCharacteristic;
use App\Models\CarCharacteristicTransport;
use App\Models\CarClass;
use App\Models\CarPark;
use App\Models\InsuranceLot;
use App\Models\LongTermRental;
use App\Models\PrepareFinance;
use App\Models\Register;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;

class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $lot_no_search = $request->lot_no_search;
        $car_class_search = $request->car_class_search;
        $license_plate_search = $request->license_plate_search;
        $status_search = $request->status_search;
        $license_plate_search_text = null;
        if ($license_plate_search) {
            $license_plate_model = Car::find($license_plate_search);
            if ($license_plate_model->engine_no) {
                $license_plate_search_text = __('inspection_cars.engine_no') . ' ' . $license_plate_model->engine_no;
            } else if ($license_plate_model->chassis_no) {
                $license_plate_search_text = __('inspection_cars.chassis_no') . ' ' . $license_plate_model->chassis_no;
            }
        }
        $car_class = CarClass::find($car_class_search);
        $car_class_search_text = $car_class && $car_class->full_name ? $car_class->full_name : null;
        $insurance_lot = InsuranceLot::find($lot_no_search);
        $lot_no_search_text = $insurance_lot && $insurance_lot->lot_no ? $insurance_lot->lot_no : null;

        $list = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')
            ->leftjoin('cars as car_db', 'car_db.id', '=', 'registereds.car_id')
            ->search($request)->sortable(['worksheet_no' => 'desc'])
            ->select('registereds.*', 'insurance_lots.lot_no')->paginate(PER_PAGE);
        $page_title = __('registers.page_title');

        // face sheet
        $lot_list = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')
            ->select('insurance_lots.id', 'insurance_lots.lot_no as name')->distinct('insurance_lots.lot_no')->get();

        $car_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')->get();
        $car_list->map(function ($item) {
            if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });

        $car_class_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->select('car_classes.id', 'car_classes.full_name as name')->get();
        $face_sheet_status_list = $this->getStatusFaceSheetList();
        $status_register_list = $this->getStatusRegisteredList();
        //end face sheet

        return view('admin.registers.index', [
            'list' => $list,
            'page_title' => $page_title,
            'status_register_list' => $status_register_list,
            'lot_list' => $lot_list,
            'car_list' => $car_list,
            'car_class_list' => $car_class_list,
            'face_sheet_status_list' => $face_sheet_status_list,
            'lot_no_search' => $lot_no_search,
            'lot_no_search_text' => $lot_no_search_text,
            'car_class_search' => $car_class_search,
            'car_class_search_text' => $car_class_search_text,
            'license_plate_search' => $license_plate_search,
            'license_plate_search_text' => $license_plate_search_text,
            'status_search' => $status_search,
        ]);
    }

    public function store(Request $request)
    {
        if (!boolval($request->is_draft)) {
            $validator = Validator::make($request->all(), [
                'car_characteristic' => [
                    'required',
                ],
                'car_category' => [
                    'required',
                ],
                'register_sign' => [
                    'required',
                ],
                'document_date' => [
                    'required',
                ],
                'receive_registered_dress_date' => [
                    'required',
                ],
                'receive_cmi' => [
                    'required',
                ],
                'receive_document_sale_date' => [
                    'required',
                ],
                'is_roof_receipt' => [
                    'required',
                ],
                'receive_roof_receipt_date' => [
                    Rule::when($request->is_roof_receipt == STATUS_ACTIVE, ['required'])
                ],
                'is_lock_license_plate' => [
                    'required'
                ],
                'type_lock_license_plate' => [
                    Rule::when($request->is_lock_license_plate == STATUS_ACTIVE, ['required'])
                ],
                'detail_lock_license_plate' => [
                    Rule::when($request->is_lock_license_plate == STATUS_ACTIVE, ['required'])
                ],
                'send_registered_date' => [
                    'required',
                ],
                'memo_no' => [
                    'required',
                ],
                'receipt_avance' => [
                    'required',
                ],
                'operation_fee_avance' => [
                    'required',
                ],
                // 'is_driver_in_center' => [
                //     Rule::when($request->in_center == BOOL_FALSE, ['required'])
                // ],

            ], [], [
                'car_characteristic' => __('registers.car_characteristic'),
                'car_category' => __('registers.car_category'),
                'register_sign' => __('registers.license_plate_registered'),
                'document_date' =>  __('registers.document_date'),
                'receive_registered_dress_date' =>  __('registers.receive_registered_dress_date'),
                'receive_cmi' =>  __('registers.receive_cmi'),
                'receive_document_sale_date' =>  __('registers.receive_document_sale_date'),
                'is_roof_receipt' => __('registers.is_receipt_roof'),
                'receive_roof_receipt_date' =>  __('registers.receive_roof_receipt_date'),
                'is_lock_license_plate' =>  __('registers.is_lock_license_plate'),
                'type_lock_license_plate' =>  __('registers.type_lock_license_plate'),
                'detail_lock_license_plate' =>  __('registers.detail_lock_license_plate'),
                'send_registered_date' =>  __('registers.send_registered_date'),
                'memo_no' =>  __('registers.memo_no'),
                'receipt_avance' =>  __('registers.receipt_avance'),
                'operation_fee_avance' =>  __('registers.operation_fee_avance'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $register = Register::find($request->id);
        if ($register) {
            $register->registered_sign = $request->register_sign;
            $register->description = $request->description;
            $register->document_date = $request->document_date;
            $register->receive_registered_dress_date = $request->receive_registered_dress_date;
            $register->receive_cmi = $request->receive_cmi;
            $register->receive_document_sale_date = $request->receive_document_sale_date;
            $register->is_lock_license_plate = $request->is_lock_license_plate;
            $register->receive_roof_receipt_date = $request->receive_roof_receipt_date;

            $register->is_roof_receipt = $request->is_roof_receipt;
            $register->type_lock_license_plate = $request->type_lock_license_plate;
            $register->detail_lock_license_plate = $request->detail_lock_license_plate;
            $register->send_registered_date = $request->send_registered_date;
            $register->remark = $request->remark;
            $register->memo_no = $request->memo_no;
            if ($request->receipt_avance) {
                $receipt_avance = str_replace(',', '', $request->receipt_avance);
                $register->receipt_avance = $receipt_avance;
            }
            if ($request->operation_fee_avance) {
                $operation_fee_avance = str_replace(',', '', $request->operation_fee_avance);
                $register->operation_fee_avance = $operation_fee_avance;
            }
            if (!boolval($request->is_draft) && strcmp($register->status, RegisterStatusEnum::PREPARE_REGISTER) === 0) {
                $register->status = RegisterStatusEnum::REGISTERING;
            }
            $register->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $register->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $register->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }

            if ($register->car_id) {
                $car = Car::find($register->car_id);
                $car->car_characteristic_id = $request->car_characteristic;
                $car->car_category_id = $request->car_category;
                $car->save();
            }
        }


        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeRegistered(Request $request)
    {
        if (!boolval($request->is_draft)) {
            $validator = Validator::make($request->all(), [
                'color_registered' => [
                    'required',
                ],
                'car_characteristic_transport_id' => [
                    'required',
                ],
                'registered_date' => [
                    'required',
                ],
                'receive_information_date' => [
                    'required',
                ],
                'license_plate' => [
                    'required',
                ],
                // 'receive_register_sign[]' => [
                //     'required',
                // ],
                'car_tax_exp_date' => [
                    'required',
                ],
                'receipt_date' => [
                    'required',
                ],
                'receipt_no' => [
                    'required',
                ],
                'tax' => [
                    'required',
                ],
                'service_fee' => [
                    'required',
                ],

            ], [], [
                'color_registered' => __('registers.color_registered'),
                'car_characteristic_transport_id' => __('registers.car_characteristic_transport'),
                'registered_date' => __('registers.registered_date'),
                'receive_information_date' => __('registers.receive_information_date'),
                'license_plate' => __('registers.license_plate'),
                // 'receive_register_sign' => __('registers.receive_register_sign'),
                'car_tax_exp_date' => __('registers.car_tax_exp_date'),
                'receipt_date' => __('registers.receipt_date'),
                'receipt_no' => __('registers.receipt_no'),
                'tax' => __('registers.tax'),
                'service_fee' => __('registers.service_fee'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $register = Register::find($request->id);
        if ($register) {
            $register->color_registered = $request->color_registered;
            $register->registered_date = $request->registered_date;
            $register->receive_information_date = $request->receive_information_date;
            $register->car_tax_exp_date = $request->car_tax_exp_date;
            $register->receipt_no = $request->receipt_no;
            $register->receipt_date = $request->receipt_date;
            $tax = $request->tax ? str_replace(',', '', $request->tax) : null;
            $register->tax = $tax;
            $service_fee = $request->service_fee ? str_replace(',', '', $request->service_fee) : null;
            $register->service_fee = $service_fee;
            $register->car_characteristic_transport_id = $request->car_characteristic_transport_id;
            $register->remark = $request->remark;
            $register->link = $request->link;
            if (!boolval($request->is_draft)) {
                $register->status = RegisterStatusEnum::REGISTERED;
            }
            if (isset($request->receive_register_sign)) {
                foreach ($request->receive_register_sign as $receive_register_sign) {
                    if ($receive_register_sign == RegisterSignEnum::IRON_SIGN) {
                        $register->is_license_plate = STATUS_ACTIVE;
                    } else if ($receive_register_sign == RegisterSignEnum::TAX_SIGN) {
                        $register->is_registration_book = STATUS_ACTIVE;
                    } elseif ($receive_register_sign == RegisterSignEnum::REGISTRATION_BOOK) {
                        $register->is_tax_sign = STATUS_ACTIVE;
                    }
                }
            }
            $register->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $register->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $register->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }

            if ($register->car_id) {
                $car = Car::find($register->car_id);
                $car->license_plate = $request->license_plate;
                $car->save();
            }

            if (strcmp($register->status, RegisterStatusEnum::REGISTERED) === 0) {
                $asset_car = AssetCar::where('car_id', $register->car_id)->first();
                if ($asset_car) {
                    $asset_car->registered_id = $register->id;
                    $asset_car->status = AssetCarStatusEnum::COMPLETE;
                    $asset_car->save();
                }
            }
        }

        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Register $register)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $page_title = __('lang.view') . __('registers.page_title');
        $url = 'admin.registers.index';
        $car_characteristic_list = CarCharacteristic::select('id', 'name')->get();
        $car_category_list = CarCategory::select('id', 'name')->get();
        $register_sign_list = $this->getRegisteredSignList();
        $status_list = $this->getStatusList();
        $lock_license_plate_list = $this->getLockLicensePlateTypeList();
        $is_receipt_roof_status_list = $this->getIsRoofStatusList();
        $optional_files = $register->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $register->paid_date = null;
        $prepare_finance = PrepareFinance::where('lot_id', $register->lot_id)->first();
        if ($prepare_finance) {
            $register->paid_date =  $prepare_finance->payment_date;
        }

        return view('admin.registers.form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'car_characteristic_list' => $car_characteristic_list,
            'car_category_list' => $car_category_list,
            'register_sign_list' => $register_sign_list,
            'status_list' => $status_list,
            'lock_license_plate_list' => $lock_license_plate_list,
            'is_receipt_roof_status_list' => $is_receipt_roof_status_list,
            'optional_files' => $optional_files,
            'view' => true,
        ]);
    }

    public function edit(Register $register)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Register);
        $page_title = __('lang.edit') . __('registers.page_title');
        $url = 'admin.registers.index';
        $car_characteristic_list = CarCharacteristic::select('id', 'name')->get();
        $car_category_list = CarCategory::select('id', 'name')->get();
        $register_sign_list = $this->getRegisteredSignList();
        $status_list = $this->getStatusList();
        $lock_license_plate_list = $this->getLockLicensePlateTypeList();
        $is_receipt_roof_status_list = $this->getIsRoofStatusList();
        $optional_files = $register->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $register->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $register->car_slot = $zone ? $zone->code . $zone->car_park_number : null;

        $register->paid_date = null;
        $prepare_finance = PrepareFinance::where('lot_id', $register->lot_id)->first();
        if ($prepare_finance) {
            $register->paid_date =  $prepare_finance->payment_date;
        }

        return view('admin.registers.form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'car_characteristic_list' => $car_characteristic_list,
            'car_category_list' => $car_category_list,
            'register_sign_list' => $register_sign_list,
            'status_list' => $status_list,
            'lock_license_plate_list' => $lock_license_plate_list,
            'is_receipt_roof_status_list' => $is_receipt_roof_status_list,
            'optional_files' => $optional_files,
        ]);
    }

    public function editRegistered(Register $register)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Register);
        $page_title = __('lang.edit') . __('registers.page_title');
        $url = 'admin.registers.index';
        $car_characteristic_list = CarCharacteristic::select('id', 'name')->get();
        $car_characteristic_transport_list = CarCharacteristicTransport::select('id', 'name')->get();
        $car_category_list = CarCategory::select('id', 'name')->get();
        $register_sign_list = $this->getRegisteredSignList();
        $status_list = $this->getStatusList();
        $lock_license_plate_list = $this->getLockLicensePlateTypeList();
        $is_receipt_roof_status_list = $this->getIsRoofStatusList();
        $optional_files = $register->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $color_registered_list = $this->getRegisteredColorList();
        $receive_register_sign = $this->getReceiveRegisterSignStatus();

        $signs = ['is_license_plate' => RegisterSignEnum::IRON_SIGN, 'is_tax_sign' => RegisterSignEnum::TAX_SIGN, 'is_registration_book' => RegisterSignEnum::REGISTRATION_BOOK];
        $receive_register_sign_arr = [];
        foreach ($signs as $key => $sign) {
            if ($register->$key == STATUS_ACTIVE) {
                array_push($receive_register_sign_arr, $sign);
            }
        }
        $register->receive_register_sign = $receive_register_sign_arr;

        if (is_null($register->car_tax_exp_date)) {
            $register->car_tax_exp_date = Carbon::parse($register->car_tax_exp_date)->addYear();
        }
        if (is_null($register->link)) {
            $register->link = "\\" . "\\" . "tls-lkb-kamonw8\สำเนาทะเบียนรถ\สำเนาทะเบียนรถใหม่";
        }

        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $register->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $register->car_slot = $zone ? $zone->code . $zone->car_park_number : null;

        $register->paid_date = null;
        $prepare_finance = PrepareFinance::where('lot_id', $register->lot_id)->first();
        if ($prepare_finance) {
            $register->paid_date =  $prepare_finance->payment_date;
        }

        $register->step = $this->setProgressStep($register->status);

        return view('admin.registers.registered-form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'car_characteristic_list' => $car_characteristic_list,
            'car_category_list' => $car_category_list,
            'register_sign_list' => $register_sign_list,
            'status_list' => $status_list,
            'lock_license_plate_list' => $lock_license_plate_list,
            'is_receipt_roof_status_list' => $is_receipt_roof_status_list,
            'optional_files' => $optional_files,
            'color_registered_list' => $color_registered_list,
            'car_characteristic_transport_list' => $car_characteristic_transport_list,
            'receive_register_sign' => $receive_register_sign,
        ]);
    }

    public function showRegistered(Register $register)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $page_title = __('lang.view') . __('registers.page_title');
        $url = 'admin.registers.index';
        $car_characteristic_list = CarCharacteristic::select('id', 'name')->get();
        $car_characteristic_transport_list = CarCharacteristicTransport::select('id', 'name')->get();
        $car_category_list = CarCategory::select('id', 'name')->get();
        $register_sign_list = $this->getRegisteredSignList();
        $status_list = $this->getStatusList();
        $lock_license_plate_list = $this->getLockLicensePlateTypeList();
        $is_receipt_roof_status_list = $this->getIsRoofStatusList();
        $optional_files = $register->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $color_registered_list = $this->getRegisteredColorList();
        $receive_register_sign = $this->getReceiveRegisterSignStatus();

        $signs = ['is_license_plate' => RegisterSignEnum::IRON_SIGN, 'is_tax_sign' => RegisterSignEnum::TAX_SIGN, 'is_registration_book' => RegisterSignEnum::REGISTRATION_BOOK];
        $receive_register_sign_arr = [];
        foreach ($signs as $key => $sign) {
            if ($register->$key == STATUS_ACTIVE) {
                array_push($receive_register_sign_arr, $sign);
            }
        }
        $register->receive_register_sign = $receive_register_sign_arr;

        $register->paid_date = null;
        $prepare_finance = PrepareFinance::where('lot_id', $register->lot_id)->first();
        if ($prepare_finance) {
            $register->paid_date =  $prepare_finance->payment_date;
        }

        $register->step = $this->setProgressStep($register->status);

        return view('admin.registers.registered-form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'car_characteristic_list' => $car_characteristic_list,
            'car_category_list' => $car_category_list,
            'register_sign_list' => $register_sign_list,
            'status_list' => $status_list,
            'lock_license_plate_list' => $lock_license_plate_list,
            'is_receipt_roof_status_list' => $is_receipt_roof_status_list,
            'optional_files' => $optional_files,
            'color_registered_list' => $color_registered_list,
            'car_characteristic_transport_list' => $car_characteristic_transport_list,
            'receive_register_sign' => $receive_register_sign,
            'view' => true,
        ]);
    }

    private function setProgressStep($status)
    {
        $step = 0;
        if (in_array($status, [RegisterStatusEnum::REGISTERING])) {
            $step = 1;
        } elseif (in_array($status, [RegisterStatusEnum::REGISTERED])) {
            $step = 2;
        }
        return $step;
    }

    public function SelectFaceSheet(Register $register)
    {
        $page_title = __('registers.select_car_face_sheet');
        $url = 'admin.registers.index';
        $status_register_list = $this->getStatusRegisteredList();
        $lot_list = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')
            ->select('insurance_lots.id', 'insurance_lots.lot_no as name')->distinct('insurance_lots.lot_no')->get();

        $car_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')->get();
        $car_list->map(function ($item) {
            if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });

        $car_class_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->select('car_classes.id', 'car_classes.full_name as name')->get();
        $face_sheet_status_list = $this->getStatusFaceSheetList();
        return view('admin.registers.face-sheet-form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'status_register_list' => $status_register_list,
            'lot_list' => $lot_list,
            'car_list' => $car_list,
            'car_class_list' => $car_class_list,
            'face_sheet_status_list' => $face_sheet_status_list,
        ]);
    }

    public function SelectAvance(Register $register)
    {
        $page_title = __('registers.avance_withdraw');
        $url = 'admin.registers.index';
        $status_register_list = $this->getStatusRegisteredList();
        $lot_list = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')
            ->select('insurance_lots.id', 'insurance_lots.lot_no as name')->distinct('insurance_lots.lot_no')->get();
        $car_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')->get();
        $car_list->map(function ($item) {
            if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });

        $car_class_list = Register::leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->select('car_classes.id', 'car_classes.full_name as name')->get();
        $face_sheet_status_list = $this->getStatusFaceSheetList();
        return view('admin.registers.avance-form', [
            'd' => $register,
            'page_title' => $page_title,
            'url' => $url,
            'status_register_list' => $status_register_list,
            'lot_list' => $lot_list,
            'car_list' => $car_list,
            'car_class_list' => $car_class_list,
            'face_sheet_status_list' => $face_sheet_status_list,
        ]);
    }

    public function checkCar(Request $request)
    {
        $lot_no = $request->lot_no;
        $car_id = $request->car_id;
        $car_class = $request->car_class;
        $leasing = $request->leasing;

        $register = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')
            ->leftjoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('registereds.status', $request->status)
            ->when($lot_no, function ($query) use ($lot_no) {
                $query->where('registereds.lot_id', $lot_no);
            })
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('registereds.car_id', $car_id);
            })
            ->when($car_class, function ($query) use ($car_class) {
                $query->where('cars.car_class_id', $car_class);
            })
            ->when($leasing, function ($query) use ($leasing) {
                $query->where('insurance_lots.leasing_id', $leasing);
            })
            ->select(
                'registereds.*',
                'insurance_lots.lot_no',
                'cars.id as car_id',
                'cars.car_class_id',
                'car_classes.full_name',
                'cars.chassis_no',
                'cars.engine_no'
            )->get();

        return response()->json([
            'register' => $register,
            'success' => true,
        ]);
    }

    public function exportExcelFaceSheet(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        if ($request->register_lists) {
            $registers = Register::whereIn('id', $request->register_lists)->get();
            foreach ($registers as $key => $register) {
                $register->index = $key + 1;
                $register->creditor_name = '';
                $register->car_class = '';
                $register->car_color = '';
                $register->engine_no = '';
                $register->chassis_no = '';
                $insurance_lot = InsuranceLot::find($register->lot_id);
                if ($insurance_lot) {
                    $register->lot_no = $insurance_lot->lot_no;
                }
                if ($register->car) {
                    $register->car_class = $register->car->CarClass ? $register->car->CarClass->full_name : '';
                    $register->car_color = $register->car->CarColor ? $register->car->CarColor->name : '';
                    $register->engine_no = $register->car->engine_no;
                    $register->chassis_no = $register->car->chassis_no;
                    $register->car_characteristic = $register->car->carCharacteristic ? $register->car->carCharacteristic->name : '';
                    $register->cc = $register->car->engine_size ? $register->car->engine_size : '';
                }
                if ($register->insurance) {
                    $register->leasing_name = $register->insurance->creditor ? $register->insurance->creditor->name : '';
                }
                if ($register->purchaseOrder) {
                    $register->creditor_name = $register->purchaseOrder->creditor ? $register->purchaseOrder->creditor->name : '';
                    if ($register->purchaseOrder->purchaseRequisiton) {
                        if ($register->purchaseOrder->purchaseRequisiton->reference_type == LongTermRental::class) {
                            $lt_rental = LongTermRental::find($register->purchaseOrder->purchaseRequisiton->reference_id);
                            $register->customer_name = $lt_rental->customer->name;
                        } else {
                            $register->customer_name = 'บริษัททรูลีสซิ่ง';
                        }
                    }
                }
            }


            if (count($registers) > 0) {
                $topic_face_sheet = $request->topic_face_sheet ?? null;
                $file = Excel::download(new ExportRegisterFaceSheet($registers, $topic_face_sheet), 'template.xlsx')->getFile();
                $custom_file_name = mb_convert_encoding($topic_face_sheet . '.xlsx', 'UTF-8', 'ISO-8859-1');
                // dd($custom_file_name);
                $fileResource = fopen($file->getPathname(), 'r');
                return response()
                    ->stream(
                        function () use ($fileResource) {
                            fpassthru($fileResource);
                        },
                        200,
                        [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment; filename="' . $custom_file_name . '"',
                        ]
                    );
            }
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function exportExcelAvance(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        if ($request->avance_list_arr) {
            $register_key = [];
            foreach ($request->avance_list_arr as $key => $avance_list_arr) {
                $register = Register::find($key);
                if ($register) {
                    $register->memo_no = $avance_list_arr['memo_no'];
                    if ($avance_list_arr['operation_fee_avance']) {
                        $operation_fee_avance = str_replace(',', '', $avance_list_arr['operation_fee_avance']);
                        $register->operation_fee_avance = $operation_fee_avance;
                    }
                    $receipt_avance = str_replace(',', '', $avance_list_arr['receipt_avance']);
                    $register->receipt_avance = $receipt_avance;
                    $register->save();
                    $register_key[] = $key;
                }
            }
        }
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        $registers = Register::whereIn('id', $register_key)->get();
        $total = 0;
        $receipt_avance_total = 0;
        $operation_fee_avance_total = 0;
        foreach ($registers as $key => $register) {
            $register->index = $key + 1;
            $register->creditor_name = '';
            $register->car_class = '';
            $register->car_color = '';
            $register->engine_no = '';
            $register->chassis_no = '';
            $insurance_lot = InsuranceLot::find($register->lot_id);
            if ($insurance_lot) {
                $register->lot_no = $insurance_lot->lot_no;
            }
            if ($register->car) {
                $register->car_class = $register->car->CarClass ? $register->car->CarClass->full_name : '';
                $register->car_color = $register->car->CarColor ? $register->car->CarColor->name : '';
                $register->engine_no = $register->car->engine_no;
                $register->chassis_no = $register->car->chassis_no;
                $register->car_characteristic = $register->car->carCharacteristic ? $register->car->carCharacteristic->name : '';
                $register->cc = $register->car->engine_size ? $register->car->engine_size : '';
            }
            if ($register->purchaseOrder) {
                $register->creditor_name = $register->purchaseOrder->creditor ? $register->purchaseOrder->creditor->name : '';

                if ($register->purchaseOrder->purchaseRequisiton) {
                    if ($register->purchaseOrder->purchaseRequisiton->reference_type == LongTermRental::class) {
                        $lt_rental = LongTermRental::find($register->purchaseOrder->purchaseRequisiton->reference_id);
                        $register->customer_name = $lt_rental->customer->name;
                    } else {
                        $register->customer_name = 'บริษัททรูลีสซิ่ง';
                    }
                }
            }
            $register->receipt_avance = $register->receipt_avance;
            $register->operation_fee_avance = $register->operation_fee_avance;
            $register->total = $register->receipt_avance + $register->operation_fee_avance;


            $receipt_avance_total += $register->receipt_avance;
            $operation_fee_avance_total += $register->operation_fee_avance;
        }
        $total = $receipt_avance_total + $operation_fee_avance_total;

        if (count($registers) > 0) {
            $topic_face_sheet = $request->topic_face_sheet ?? 'test';

            $file = Excel::download(new ExportRegisterAvance($registers, $topic_face_sheet, $receipt_avance_total, $operation_fee_avance_total, $total), 'template.xlsx')->getFile();
            $now = Carbon::now();
            $file_name = $now->format('dmY:H:i:s');
            $custom_file_name = 'Advance_' . $file_name . '.xlsx';
            $fileResource = fopen($file->getPathname(), 'r');
            return response()
                ->stream(
                    function () use ($fileResource) {
                        fpassthru($fileResource);
                    },
                    200,
                    [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment; filename="' . $custom_file_name . '"',
                    ]
                );
        }
        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeImportExcel(Request $request)
    {
        foreach ($request->import_list_arr as $import_list) {
            $register = Register::find($import_list['id']);
            if ($register) {
                // car_characteristic_transport
                $car_characteristic_transport = CarCharacteristicTransport::where('name', $import_list['car_characteristic_transport'])->first();
                if ($car_characteristic_transport) {
                    $register->car_characteristic_transport_id = $car_characteristic_transport->id;
                }
                // color_registered
                $register->color_registered = $import_list['color_registered'];
                // registered_date
                $register->registered_date = $import_list['registered_date'];
                // receive_information_date
                $register->receive_information_date = $import_list['receive_information_date'];
                // license_plate
                if ($register->car_id) {
                    $car = Car::find($register->car_id);
                    if ($car) {
                        $car->license_plate = $import_list['license_plate'];
                        $car->save();
                    }
                }
                // car_tax_exp_date
                $register->car_tax_exp_date = $import_list['car_tax_exp_date'];
                // receipt_date
                $register->receipt_date = $import_list['receipt_date'];
                // receipt_no
                $register->receipt_no = $import_list['receipt_no'];
                // tax
                $tax = $import_list['tax'] ? str_replace(',', '', $import_list['tax']) : null;
                $register->tax = $tax;
                // service_fee
                $service_fee = $import_list['service_fee'] ? str_replace(',', '', $import_list['service_fee']) : null;
                $register->service_fee = $service_fee;
                // link
                $register->link = $import_list['link'];
                // is_registration_book
                $register->is_registration_book = filter_var($import_list['is_registration_book'], FILTER_VALIDATE_BOOLEAN);
                // is_license_plate
                $register->is_license_plate = filter_var($import_list['is_license_plate'], FILTER_VALIDATE_BOOLEAN);
                // is_tax_sign
                $register->is_tax_sign = filter_var($import_list['is_tax_sign'], FILTER_VALIDATE_BOOLEAN);
                $register->status = isset($import_list['status']) ? $import_list['status'] : $register->status;
                $register->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
        ]);
    }

    public function importExcel(Request $request)
    {
        $data_request = $request->json_object;
        $key_arr = ['ลักษณะรถตามกรมขนส่ง', 'สีที่จดทะเบียน', 'วันที่จดทะเบียนเสร็จ', 'วันที่ได้รับข้อมูลมาบันทึก', 'เลขทะเบียนรถ', 'วันที่หมดอายุภาษีรถยนต์', 'วันที่ออกใบเสร็จ', 'เลขที่ใบเสร็จ', 'ค่าภาษี', 'ค่าบริการ', 'ลิงก์แนบที่อยู่ไฟล์สำเนาทะเบียนรถ', 'การได้รับเล่มทะเบียน (yes,no)', 'การได้รับป้ายเหล็ก (yes,no)', 'การได้รับป้ายภาษี (yes,no)'];
        $key_arr_size = count($key_arr);

        $key_remove = ['REGISTEREDS_ID', 'ลำดับ', 'ชื่อผู้ขาย', 'รุ่นรถ', 'CC', 'สีรถ', 'หมายเลขเครื่องยนต์', 'หมายเลขตัวถัง', 'ลูกค้า', 'ลักษณะรถ', 'LOT'];

        if ($data_request) {
            foreach ($data_request as $index => $item) {
                foreach ($key_remove as $key) {
                    if (array_key_exists($key, $item)) {
                        unset($data_request[$index][$key]);
                    }
                }
            }

            foreach ($data_request as $key => $item) {
                $item_size = count($item);
                if ($item_size != $key_arr_size) {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }

            $modified_array = [];
            foreach ($request->json_object as $object) {
                $validate_data = 0;
                if (isset($object['ลักษณะรถตามกรมขนส่ง'])) {
                    $car_characteristic_transport = CarCharacteristicTransport::where('name', $object['ลักษณะรถตามกรมขนส่ง'])->first();
                    if (!$car_characteristic_transport) {
                        $validate_data++;
                    }
                }

                if (isset($object['สีที่จดทะเบียน']) && !in_array($object['สีที่จดทะเบียน'], [
                    __('registers.registered_color_' . RegisterColorEnum::BLACK),
                    __('registers.registered_color_' . RegisterColorEnum::BLUE),
                    __('registers.registered_color_' . RegisterColorEnum::GREY),
                    __('registers.registered_color_' . RegisterColorEnum::RED),
                    __('registers.registered_color_' . RegisterColorEnum::WHITE),
                    __('registers.registered_color_' . RegisterColorEnum::MIX)
                ])) {
                    $validate_data++;
                } else if (isset($object['สีที่จดทะเบียน'])) {
                    $colors = [
                        __('registers.registered_color_' . RegisterColorEnum::BLACK) => RegisterColorEnum::BLACK,
                        __('registers.registered_color_' . RegisterColorEnum::BLUE) => RegisterColorEnum::BLUE,
                        __('registers.registered_color_' . RegisterColorEnum::GREY) => RegisterColorEnum::GREY,
                        __('registers.registered_color_' . RegisterColorEnum::RED) => RegisterColorEnum::RED,
                        __('registers.registered_color_' . RegisterColorEnum::WHITE) => RegisterColorEnum::WHITE,
                        __('registers.registered_color_' . RegisterColorEnum::MIX) => RegisterColorEnum::MIX,
                    ];
                    if (isset($colors[$object['สีที่จดทะเบียน']])) {
                        $object['สีที่จดทะเบียน'] = $colors[$object['สีที่จดทะเบียน']];
                    }
                }
                if (isset($object['วันที่จดทะเบียนเสร็จ'])) {

                    $date_string = $object['วันที่จดทะเบียนเสร็จ'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่จดทะเบียนเสร็จ'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่ได้รับข้อมูลมาบันทึก'])) {
                    $date_string = $object['วันที่ได้รับข้อมูลมาบันทึก'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่ได้รับข้อมูลมาบันทึก'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่หมดอายุภาษีรถยนต์'])) {
                    $date_string = $object['วันที่หมดอายุภาษีรถยนต์'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่หมดอายุภาษีรถยนต์'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่ออกใบเสร็จ'])) {
                    $date_string = $object['วันที่ออกใบเสร็จ'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่ออกใบเสร็จ'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['การได้รับเล่มทะเบียน (yes,no)']) && !in_array($object['การได้รับเล่มทะเบียน (yes,no)'], ['YES', 'yes', 'Yes', 'NO', 'no', 'No'])) {
                    $validate_data++;
                } else if (isset($object['การได้รับเล่มทะเบียน (yes,no)'])) {
                    if (in_array($object['การได้รับเล่มทะเบียน (yes,no)'], ['YES', 'yes', 'Yes'])) {
                        $object['การได้รับเล่มทะเบียน (yes,no)'] = STATUS_ACTIVE;
                    } else if (in_array($object['การได้รับเล่มทะเบียน (yes,no)'], ['NO', 'no', 'No'])) {
                        $object['การได้รับเล่มทะเบียน (yes,no)'] = STATUS_DEFAULT;
                    }
                }

                if (isset($object['การได้รับป้ายเหล็ก (yes,no)']) && !in_array($object['การได้รับป้ายเหล็ก (yes,no)'], ['YES', 'yes', 'Yes', 'NO', 'no', 'No'])) {
                    $validate_data++;
                } else if (isset($object['การได้รับป้ายเหล็ก (yes,no)'])) {
                    if (in_array($object['การได้รับป้ายเหล็ก (yes,no)'], ['YES', 'yes', 'Yes'])) {
                        $object['การได้รับป้ายเหล็ก (yes,no)'] = STATUS_ACTIVE;
                    } else if (in_array($object['การได้รับป้ายเหล็ก (yes,no)'], ['NO', 'no', 'No'])) {
                        $object['การได้รับป้ายเหล็ก (yes,no)'] = STATUS_DEFAULT;
                    }
                }

                if (isset($object['การได้รับป้ายภาษี (yes,no)']) && !in_array($object['การได้รับป้ายภาษี (yes,no)'], ['YES', 'yes', 'Yes', 'NO', 'no', 'No'])) {
                    $validate_data++;
                } else if (isset($object['การได้รับป้ายภาษี (yes,no)'])) {
                    if (in_array($object['การได้รับป้ายภาษี (yes,no)'], ['YES', 'yes', 'Yes'])) {
                        $object['การได้รับป้ายภาษี (yes,no)'] = STATUS_ACTIVE;
                    } else if (in_array($object['การได้รับป้ายภาษี (yes,no)'], ['NO', 'no', 'No'])) {
                        $object['การได้รับป้ายภาษี (yes,no)'] = STATUS_DEFAULT;
                    }
                }

                if ($validate_data > 0) {
                    return response()->json([
                        'success' => false,
                    ]);
                }

                $modified_object = [
                    'registered_id' => isset($object['REGISTEREDS_ID']) ? $object['REGISTEREDS_ID'] : null,
                    'no' => isset($object['ลำดับ']) ? $object['ลำดับ'] : null,
                    'sale_name' => isset($object['ชื่อผู้ขาย']) ? $object['ชื่อผู้ขาย'] : null,
                    'car_class' => isset($object['รุ่นรถ']) ? $object['รุ่นรถ'] : null,
                    'cc' => isset($object['CC']) ? $object['CC'] : null,
                    'car_color' => isset($object['สีรถ']) ? $object['สีรถ'] : null,
                    'engine_no' => isset($object['หมายเลขเครื่องยนต์']) ? $object['หมายเลขเครื่องยนต์'] : null,
                    'chassis_no' => isset($object['หมายเลขตัวถัง']) ? $object['หมายเลขตัวถัง'] : null,
                    'customer' => isset($object['ลูกค้า']) ? $object['ลูกค้า'] : null,
                    'car_characteristic' => isset($object['ลักษณะรถ']) ? $object['ลักษณะรถ'] : null,
                    'lot' => isset($object['LOT']) ? $object['LOT'] : null,
                    'car_characteristic_transport' => isset($object['ลักษณะรถตามกรมขนส่ง']) ? $object['ลักษณะรถตามกรมขนส่ง'] : null,
                    'color_registered' => isset($object['สีที่จดทะเบียน']) ? $object['สีที่จดทะเบียน'] : null,
                    'registered_date' => isset($object['วันที่จดทะเบียนเสร็จ']) ? $object['วันที่จดทะเบียนเสร็จ'] : null,
                    'receive_information_date' => isset($object['วันที่ได้รับข้อมูลมาบันทึก']) ? $object['วันที่ได้รับข้อมูลมาบันทึก'] : null,
                    'license_plate' => isset($object['เลขทะเบียนรถ']) ? $object['เลขทะเบียนรถ'] : null,
                    'car_tax_exp_date' => isset($object['วันที่หมดอายุภาษีรถยนต์']) ? $object['วันที่หมดอายุภาษีรถยนต์'] : null,
                    'receipt_date' => isset($object['วันที่ออกใบเสร็จ']) ? $object['วันที่ออกใบเสร็จ'] : null,
                    'receipt_no' => isset($object['เลขที่ใบเสร็จ']) ? $object['เลขที่ใบเสร็จ'] : null,
                    'tax' => isset($object['ค่าภาษี']) ? $object['ค่าภาษี'] : null,
                    'service_fee' => isset($object['ค่าบริการ']) ? $object['ค่าบริการ'] : null,
                    'link' => isset($object['ลิงก์แนบที่อยู่ไฟล์สำเนาทะเบียนรถ']) ? $object['ลิงก์แนบที่อยู่ไฟล์สำเนาทะเบียนรถ'] : null,
                    'is_registration_book' => isset($object['การได้รับเล่มทะเบียน (yes,no)']) ? $object['การได้รับเล่มทะเบียน (yes,no)'] : null,
                    'is_license_plate' => isset($object['การได้รับป้ายเหล็ก (yes,no)']) ? $object['การได้รับป้ายเหล็ก (yes,no)'] : null,
                    'is_tax_sign' => isset($object['การได้รับป้ายภาษี (yes,no)']) ? $object['การได้รับป้ายภาษี (yes,no)'] : null,

                ];
                $modified_array[] = $modified_object;
            }

            $request->merge(['json_object' => $modified_array]);


            return response()->json([
                'success' => true,
                'message' => 'ok',
                'data' => $request->json_object,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function exportExcelTemplate(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        if ($request->register_lists) {
            $registers = Register::whereIn('id', $request->register_lists)->get();
            foreach ($registers as $key => $register) {
                $register->index = $key + 1;
                $register->creditor_name = '';
                $register->car_class = '';
                $register->car_color = '';
                $register->engine_no = '';
                $register->chassis_no = '';
                $insurance_lot = InsuranceLot::find($register->lot_id);
                if ($insurance_lot) {
                    $register->lot_no = $insurance_lot->lot_no;
                }
                if ($register->car) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $register->car_class = $register->car->CarClass ? $register->car->CarClass->full_name : '';
                    $register->car_color = $register->car->CarColor ? $register->car->CarColor->name : '';
                    $register->engine_no = $register->car->engine_no;
                    $register->chassis_no = $register->car->chassis_no;
                    $register->car_characteristic = $register->car->carCharacteristic ? $register->car->carCharacteristic->name : '';
                    $register->cc = $register->car->engine_size ? $register->car->engine_size : '';
                }
                if ($register->purchaseOrder) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $register->creditor_name = $register->purchaseOrder->creditor ? $register->purchaseOrder->creditor->name : '';

                    if ($register->purchaseOrder->purchaseRequisiton) {
                        if ($register->purchaseOrder->purchaseRequisiton->reference_type == LongTermRental::class) {
                            $lt_rental = LongTermRental::find($register->purchaseOrder->purchaseRequisiton->reference_id);
                            $register->customer_name = $lt_rental->customer->name;
                        } else {
                            $register->customer_name = 'บริษัททรูลีสซิ่ง';
                        }
                    }
                }
            }

            if (count($registers) > 0) {
                $topic_face_sheet = $request->topic_face_sheet ?? null;
                $now = Carbon::now();
                $file_name = $now->format('dmY-H:i:s');
                $custom_file_name = 'REGISTEREDS_' . $file_name . '.xlsx';
                return Excel::download(new ExportRegisterTemplate($registers, $topic_face_sheet), $custom_file_name);
            }
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public static function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('registers.lock_license_plate' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('registers.lock_license_plate' . STATUS_DEFAULT),
            ],
        ]);
    }

    public static function getIsRoofStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('registers.is_receipt_roof' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('registers.is_receipt_roof' . STATUS_DEFAULT),
            ],
        ]);
    }


    private function getRegisteredColorList()
    {
        return collect([
            (object)[
                'id' => RegisterColorEnum::WHITE,
                'value' => RegisterColorEnum::WHITE,
                'name' => __('registers.registered_color_' . RegisterColorEnum::WHITE),
            ],
            (object)[
                'id' => RegisterColorEnum::BLACK,
                'value' => RegisterColorEnum::BLACK,
                'name' => __('registers.registered_color_' . RegisterColorEnum::BLACK),
            ],
            (object)[
                'id' => RegisterColorEnum::GREY,
                'value' => RegisterColorEnum::GREY,
                'name' => __('registers.registered_color_' . RegisterColorEnum::GREY),
            ],
            (object)[
                'id' => RegisterColorEnum::RED,
                'value' => RegisterColorEnum::RED,
                'name' => __('registers.registered_color_' . RegisterColorEnum::RED),
            ],
            (object)[
                'id' => RegisterColorEnum::BLUE,
                'value' => RegisterColorEnum::BLUE,
                'name' => __('registers.registered_color_' . RegisterColorEnum::BLUE),
            ],
            (object)[
                'id' => RegisterColorEnum::MIX,
                'value' => RegisterColorEnum::MIX,
                'name' => __('registers.registered_color_' . RegisterColorEnum::MIX),
            ],
        ]);
    }

    private function getRegisteredSignList()
    {
        return collect([
            (object)[
                'id' => RegisterSignTypeEnum::GREEN_SIGN,
                'value' => RegisterSignTypeEnum::GREEN_SIGN,
                'name' => __('registers.registered_sign_type_' . RegisterSignTypeEnum::GREEN_SIGN),
            ],
            (object)[
                'id' => RegisterSignTypeEnum::WHITE_SIGN,
                'value' => RegisterSignTypeEnum::WHITE_SIGN,
                'name' => __('registers.registered_sign_type_' . RegisterSignTypeEnum::WHITE_SIGN),
            ],
            (object)[
                'id' => RegisterSignTypeEnum::BLUE_SIGN,
                'value' => RegisterSignTypeEnum::BLUE_SIGN,
                'name' => __('registers.registered_sign_type_' . RegisterSignTypeEnum::BLUE_SIGN),
            ],
            (object)[
                'id' => RegisterSignTypeEnum::YELLOW_SIGN,
                'value' => RegisterSignTypeEnum::YELLOW_SIGN,
                'name' => __('registers.registered_sign_type_' . RegisterSignTypeEnum::YELLOW_SIGN),
            ],
            (object)[
                'id' => RegisterSignTypeEnum::GREEN_SERVICE_SIGN,
                'value' => RegisterSignTypeEnum::GREEN_SERVICE_SIGN,
                'name' => __('registers.registered_sign_type_' . RegisterSignTypeEnum::GREEN_SERVICE_SIGN),
            ],
        ]);
    }

    private function getLockLicensePlateTypeList()
    {
        return collect([
            (object)[
                'id' => LockLicensePlateTypeEnum::USE_OLD_LICENSE_PLATE,
                'value' => LockLicensePlateTypeEnum::USE_OLD_LICENSE_PLATE,
                'name' => __('registers.lock_license_plate_type_' . LockLicensePlateTypeEnum::USE_OLD_LICENSE_PLATE),
            ],
            (object)[
                'id' => LockLicensePlateTypeEnum::RESERVE_LICENSE_PLATE,
                'value' => LockLicensePlateTypeEnum::RESERVE_LICENSE_PLATE,
                'name' => __('registers.lock_license_plate_type_' . LockLicensePlateTypeEnum::RESERVE_LICENSE_PLATE),
            ],
        ]);
    }

    private function getReceiveRegisterSignStatus()
    {
        return collect([
            [
                'id' => RegisterSignEnum::IRON_SIGN,
                'value' => RegisterSignEnum::IRON_SIGN,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::IRON_SIGN),
            ],
            [
                'id' => RegisterSignEnum::TAX_SIGN,
                'value' => RegisterSignEnum::TAX_SIGN,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::TAX_SIGN),
            ],
            [
                'id' => RegisterSignEnum::REGISTRATION_BOOK,
                'value' => RegisterSignEnum::REGISTRATION_BOOK,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::REGISTRATION_BOOK),
            ],
        ]);
    }

    private function getStatusRegisteredList()
    {
        return collect([
            (object)[
                'id' => RegisterStatusEnum::PREPARE_REGISTER,
                'value' => RegisterStatusEnum::PREPARE_REGISTER,
                'name' => __('registers.status_' . RegisterStatusEnum::PREPARE_REGISTER . '_text'),
            ],
            (object)[
                'id' => RegisterStatusEnum::REGISTERING,
                'value' => RegisterStatusEnum::REGISTERING,
                'name' => __('registers.status_' . RegisterStatusEnum::REGISTERING . '_text'),
            ],
            (object)[
                'id' => RegisterStatusEnum::REGISTERED,
                'value' => RegisterStatusEnum::REGISTERED,
                'name' => __('registers.status_' . RegisterStatusEnum::REGISTERED . '_text'),
            ],
        ]);
    }

    private function getStatusFaceSheetList()
    {
        return collect([
            (object)[
                'id' => FaceSheetTypeEnum::REGISTER_NEW_CAR,
                'value' => FaceSheetTypeEnum::REGISTER_NEW_CAR,
                'name' => __('registers.type_face_sheet_' . FaceSheetTypeEnum::REGISTER_NEW_CAR),
            ],
            (object)[
                'id' => FaceSheetTypeEnum::RETURN_LEASING,
                'value' => FaceSheetTypeEnum::RETURN_LEASING,
                'name' => __('registers.type_face_sheet_' . FaceSheetTypeEnum::RETURN_LEASING),
            ],

        ]);
    }
}
