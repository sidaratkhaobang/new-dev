<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ChangeRegistrationStatusEnum;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ChangeRegistration;
use App\Models\Creditor;
use App\Traits\ChangeRegisterTrait;
use App\Traits\RegisterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ChangeRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ChangeRegistration);
        $status_list = $this->getStatus();
        $request_list = $this->getRequestTypeList();
        $request_id = $request->request_id;
        $status = $request->status;
        $car_id = $request->car_id;
        $leasing = $request->leasing;
        $leasing = Creditor::find($leasing);
        $leasing_text = $leasing ? $leasing->name : '';
        $car_text = null;
        $car = null;
        if ($car_id) {
            $car = Car::find($car_id);
            if ($car->license_plate) {
                $car_text = $car->license_plate;
            } else if ($car->engine_no) {
                $car_text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $worksheet_no = $request->worksheet_no;
        $list = ChangeRegistration::leftjoin('cars as car_db', 'car_db.id', '=', 'change_registrations.car_id')
            ->leftjoin('hire_purchases as hire_purchases_db', 'hire_purchases_db.id', '=', 'change_registrations.hire_purchase_id')
            ->sortable(['worksheet_no' => 'desc'])
            ->select('change_registrations.*')
            ->search($request)
            ->paginate(PER_PAGE);
        return view('admin.change-registrations.index', [
            'lists' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'car_text' => $car_text,
            'status' => $status,
            'worksheet_no' => $worksheet_no,
            'car' => $car,
            'request_id' => $request_id,
            'request_list' => $request_list,
            'leasing' => $leasing,
            'leasing_text' => $leasing_text,
        ]);
    }

    public static function getStatus()
    {
        $status = collect([
            (object)[
                'id' => ChangeRegistrationStatusEnum::WAITING_DOCUMENT,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::WAITING_DOCUMENT . '_text'),
                'value' => ChangeRegistrationStatusEnum::WAITING_DOCUMENT,
            ],
            (object)[
                'id' => ChangeRegistrationStatusEnum::WAITING_SEND_DLT,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::WAITING_SEND_DLT . '_text'),
                'value' => ChangeRegistrationStatusEnum::WAITING_SEND_DLT,
            ],
            (object)[
                'id' => ChangeRegistrationStatusEnum::PROCESSING,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::PROCESSING . '_text'),
                'value' => ChangeRegistrationStatusEnum::PROCESSING,
            ],
            (object)[
                'id' => ChangeRegistrationStatusEnum::SUCCESS,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::SUCCESS . '_text'),
                'value' => ChangeRegistrationStatusEnum::SUCCESS,
            ],
        ]);
        return $status;
    }

    public static function getRequestTypeList()
    {
        $status = collect([
            (object)[
                'id' => ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY . '_text'),
                'value' => ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY,
            ],
            (object)[
                'id' => ChangeRegistrationTypeEnum::CHANGE_COLOR,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_COLOR . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_COLOR,
            ],
            (object)[
                'id' => ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC,
            ],
            (object)[
                'id' => ChangeRegistrationTypeEnum::CHANGE_TYPE,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_TYPE . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_TYPE,
            ],
            (object)[
                'id' => ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE . '_text'),
                'value' => ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE,
            ],
            (object)[
                'id' => ChangeRegistrationTypeEnum::CANCEL_USE_CAR,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR . '_text'),
                'value' => ChangeRegistrationTypeEnum::CANCEL_USE_CAR,
            ],
        ]);
        return $status;
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ChangeRegistration);
        $change_registeration = ChangeRegistration::findOrFail($request->change_registeration_id);
        $change_registeration_status = $change_registeration?->status;
        $change_registeration_type = $change_registeration?->type;
        $check_link_arrs = [
            ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY,
            ChangeRegistrationTypeEnum::CANCEL_USE_CAR
        ];
        $data = $request->all();
        $validator = $this->validateChangeRegisteration($change_registeration_status, $change_registeration_type, $data);
        if (!empty($validator)) {
            return $validator;
        }
        $change_registeration->requester_type_contact = $request->requester_type_contact;
        $change_registeration->name_contact = $request->name_contact;
        $change_registeration->tel_contact = $request->tel_contact;
        $change_registeration->email_contact = $request->email_contact;
        $change_registeration->address_contact = $request->address_contact;
        $change_registeration->request_registration_book_date = $request->request_registration_book_date;
        $change_registeration->is_power_attorney_tls = $request->is_power_attorney_tls;
        $change_registeration->is_power_attorney = $request->is_power_attorney;
        if (!in_array($change_registeration_type, $check_link_arrs)) {
            $change_registeration->link = $request->link;
        }
        $this->storeByStatusAndType($change_registeration, $change_registeration_status, $change_registeration_type, $request);
        $this->updateStatus($change_registeration, $change_registeration_status);
        $change_registeration->save();
        $redirect_route = route('admin.change-registrations.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function validateChangeRegisteration($status, $type, $data)
    {
        if (empty($status)) {
            return 'error';
        }
        $validator = Validator::make($data, [
            'requester_type_contact' => ['required'],
            'name_contact' => ['required'],
            'tel_contact' => ['required', 'numeric', 'digits:10'],
            'email_contact' => ['required', 'email'],
            'address_contact' => ['required'],
            'request_registration_book_date' => ['required'],
            'is_power_attorney_tls' => ['required'],
            'is_power_attorney' => ['required'],
            'link' => 'required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY . ',' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR,
        ], [], [
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
            'request_registration_book_date' => __('change_registrations.request_registration_book_date'),
            'is_power_attorney_tls' => __('change_registrations.is_power_attorney_tls'),
            'is_power_attorney' => __('change_registrations.is_power_attorney'),
            'link' => __('change_registrations.link_registration'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if ($status == ChangeRegistrationStatusEnum::WAITING_DOCUMENT) {
            $validator = Validator::make($data, [
                'receive_registration_book_date' => ['required'],
                'request_power_attorney_tls_date' => ['required'],
                'receive_power_attorney_tls_date' => ['required'],
                'request_power_attorney_date' => ['required'],
                'receive_power_attorney_date' => ['required'],
                'address_contact' => ['required'],
                'request_registration_book_date' => ['required'],
                'memo_no' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'receipt_avance' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'operation_fee_avance' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
            ], [], [
                'receive_registration_book_date' => __('change_registrations.receive_registration_book_date'),
                'request_power_attorney_tls_date' => __('change_registrations.request_power_attorney_tls_date'),
                'receive_power_attorney_tls_date' => __('change_registrations.receive_power_attorney_tls_date'),
                'request_power_attorney_date' => __('change_registrations.request_power_attorney_date'),
                'address_contact' => __('change_registrations.address_contact'),
                'request_registration_book_date' => __('change_registrations.request_registration_book_date'),
                'memo_no' => __('change_registrations.memo_no'),
                'receipt_avance' => __('change_registrations.receipt_avance'),
                'operation_fee_avance' => __('change_registrations.operation_fee_avance'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if ($status == ChangeRegistrationStatusEnum::WAITING_SEND_DLT) {
            $validator = Validator::make($data, [
                'processing_date' => ['required'],
                'completion_registration_date' => ['required_if:' . $type . ',' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR],
                'recive_licen_list' => ['required_if:' . $type . ',' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR],
            ], [], [
                'processing_date' => __('change_registrations.processing_date'),
                'completion_registration_date' => __('change_registrations.completion_registration_date'),
                'recive_licen_list' => __('change_registrations.recive_licen_list'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if ($status == ChangeRegistrationStatusEnum::PROCESSING) {
            $validator = Validator::make($data, [
                'completion_date' => ['required'],
                'return_registration_book_date' => ['required'],
                'ems' => ['required_if:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'delivery_date' => ['required_if:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'receipt_date' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'receipt_no' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'receipt_fee' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
                'service_fee' => ['required_unless:' . $type . ',' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY],
            ], [], [
                'completion_date' => __('change_registrations.completion_date'),
                'return_registration_book_date' => __('change_registrations.return_registration_book_date'),
                'ems' => __('change_registrations.ems'),
                'delivery_date' => __('change_registrations.delivery_date'),
                'receipt_date' => __('change_registrations.receipt_date'),
                'receipt_no' => __('change_registrations.receipt_no'),
                'receipt_fee' => __('change_registrations.receipt_fee'),
                'service_fee' => __('change_registrations.service_fee'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
    }

    public function storeByStatusAndType($model, $status, $type, $data)
    {
        if ($status == ChangeRegistrationStatusEnum::WAITING_DOCUMENT) {
            $model->receive_power_attorney_date = $data->receive_power_attorney_date;
            $model->receive_registration_book_date = $data->receive_registration_book_date;
            $model->request_power_attorney_tls_date = $data->request_power_attorney_tls_date;
            $model->receive_power_attorney_tls_date = $data->receive_power_attorney_tls_date;
            $model->request_power_attorney_date = $data->request_power_attorney_date;
            $model->address_contact = $data->address_contact;
            $model->request_registration_book_date = $data->request_registration_book_date;
            if ($type != ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY) {
                $model->memo_no = $data->memo_no;
                $model->receipt_avance = $data->receipt_avance;
                $model->operation_fee_avance = $data->operation_fee_avance;
            }
        }
        if ($status == ChangeRegistrationStatusEnum::WAITING_SEND_DLT) {
            $model->processing_date = $data->processing_date;
            if ($type == ChangeRegistrationTypeEnum::CANCEL_USE_CAR) {
                $model->completion_registration_date = $data->completion_registration_date;
                $model->request_registration_book_date = $data->request_registration_book_date;
            }
        }
        if ($status == ChangeRegistrationStatusEnum::PROCESSING) {
            $model->completion_date = $data->completion_date;
            $model->return_registration_book_date = $data->return_registration_book_date;
            if ($type == ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY) {
                $model->delivery_date = $data->delivery_date;
            }
            if ($type != ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY) {
                $model->receipt_date = $data->receipt_date;
                $model->receipt_no = $data->receipt_no;
                $model->receipt_fee = $data->receipt_fee;
                $model->service_fee = $data->service_fee;
            }
        }
    }

    public function updateStatus($model, $status)
    {
        if (empty($status) && empty($model)) {
            return null;
        }
        if ($status == ChangeRegistrationStatusEnum::WAITING_DOCUMENT) {
            $model->status = ChangeRegistrationStatusEnum::WAITING_SEND_DLT;
        }
        if ($status == ChangeRegistrationStatusEnum::WAITING_SEND_DLT) {
            $model->status = ChangeRegistrationStatusEnum::PROCESSING;
        }
        if ($status == ChangeRegistrationStatusEnum::PROCESSING) {
            $model->status = ChangeRegistrationStatusEnum::SUCCESS;
        }
    }

    public function show(ChangeRegistration $change_registration)
    {
        $this->authorize(Actions::View . '_' . Resources::ChangeRegistration);
        $page_title = __('lang.view') . __('change_registrations.page_title');
        $car_list = RegisterTrait::getCarList();
        $recive_licen_list = RegisterTrait::getReciveLicenList();
        $type_contact_list = ChangeRegisterTrait::getRequestTypeContactList();
        $change_registration->wait_registration_book_duration_day = $this->diffDate($change_registration->request_registration_book_date, $change_registration->receive_registration_book_date);
        $change_registration->wait_power_attorney_duration_day = $this->diffDate($change_registration->request_power_attorney_date, $change_registration->receive_power_attorney_date);
        $change_registration->completion_duration_date = $this->diffDate($change_registration->processing_date, $change_registration->completion_date);
        $change_registration->summary_avance = $this->calPriceSummary($change_registration->receipt_avance, $change_registration->operation_fee_avance);
        $change_registration->summary_service = $this->calPriceSummary($change_registration->receipt_fee, $change_registration->service_fee);
        $d = $change_registration;
        $media = $this->getSlipByType($change_registration);
        $view = true;
        return view('admin.change-registrations.form', [
            'page_title' => $page_title,
            'd' => $d,
            'car_list' => $car_list,
            'recive_licen_list' => $recive_licen_list,
            'type_contact_list' => $type_contact_list,
            'media' => $media,
            'view' => $view,
        ]);
    }

    public function edit(ChangeRegistration $change_registration)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ChangeRegistration);
        $page_title = __('lang.edit') . __('change_registrations.page_title');
        $car_list = RegisterTrait::getCarList();
        $recive_licen_list = RegisterTrait::getReciveLicenList();
        $type_contact_list = ChangeRegisterTrait::getRequestTypeContactList();
        $change_registration->wait_registration_book_duration_day = $this->diffDate($change_registration->request_registration_book_date, $change_registration->receive_registration_book_date);
        $change_registration->wait_power_attorney_duration_day = $this->diffDate($change_registration->request_power_attorney_date, $change_registration->receive_power_attorney_date);
        $change_registration->completion_duration_date = $this->diffDate($change_registration->processing_date, $change_registration->completion_date);
        $change_registration->summary_avance = $this->calPriceSummary($change_registration->receipt_avance, $change_registration->operation_fee_avance);
        $change_registration->summary_service = $this->calPriceSummary($change_registration->receipt_fee, $change_registration->service_fee);
        $d = $change_registration;
        $media = $this->getSlipByType($change_registration);
        $view = ($d?->status === ChangeRegistrationStatusEnum::SUCCESS) ? true : false;
        return view('admin.change-registrations.form', [
            'page_title' => $page_title,
            'd' => $d,
            'car_list' => $car_list,
            'recive_licen_list' => $recive_licen_list,
            'type_contact_list' => $type_contact_list,
            'media' => $media,
            'view' => $view
        ]);
    }

    public function diffDate($date_start, $date_end, $type = 'd'): int
    {
        if (empty($date_start) || empty($date_end)) {
            return 0;
        }
        $date1 = Carbon::parse($date_start);
        $date2 = Carbon::parse($date_end);
        $date_diff = $date1->diff($date2)->$type;
        return $date_diff;
    }

    public function calPriceSummary($price_first, $price_second): int
    {
        if (empty($price_first) || empty($price_second)) {
            return 0;
        }
        $price_total = floatval($price_first) + floatval($price_second);
        return $price_total;
    }

    public function getSlipByType($model): array
    {
        if (empty($model)) {
            return [];
        }
        $media = [];
        if (strcmp($model->type, ChangeRegistrationTypeEnum::CHANGE_COLOR) === 0) {
            $media = [
                'car_body_files' => get_medias_detail($model->getMedia('car_body_color_files')),
                'receipt_file' => get_medias_detail($model->getMedia('receipt_change_color_files'))
            ];
        }
        if (strcmp($model->type, ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC) === 0) {
            $media = [
                'car_body_files' => get_medias_detail($model->getMedia('car_body_characteristic_files')),
                'receipt_file' => get_medias_detail($model->getMedia('receipt_change_characteristic_files')),
            ];
        }
        if (strcmp($model->type, ChangeRegistrationTypeEnum::CHANGE_TYPE) === 0) {
            $media = [
                'car_body_files' => get_medias_detail($model->getMedia('car_body_type_files')),
                'receipt_file' => get_medias_detail($model->getMedia('receipt_change_type_files')),
            ];
        }
        if (strcmp($model->type, ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE) === 0) {
            $media = [
                'register_files' => get_medias_detail($model->getMedia('register_files')),
                'power_attorney_files' => get_medias_detail($model->getMedia('power_attorney_files')),
                'letter_consent_files' => get_medias_detail($model->getMedia('letter_consent_files')),
                'citizen_files' => get_medias_detail($model->getMedia('citizen_files')),
            ];
        }
        if (strcmp($model->type, ChangeRegistrationTypeEnum::CANCEL_USE_CAR) === 0) {
            $media = [
                'optional_cancel_use_car_files' => get_medias_detail($model->getMedia('optional_cancel_use_car_files')),
            ];
        }

        return $media;
    }

    public function getListAll($request_change_registration)
    {
        $car = Car::find($request_change_registration->car_id);
        return [
            'user_name_contact' => User::where('id', $request_change_registration->contact_user_id)->pluck('name')->first(),
            'user_name_recipient' => User::where('id', $request_change_registration->user_name_recipient)->pluck('name')->first(),
            'car_class_name' => CarClass::where('id', $car->car_class_id)->pluck('full_name')->first(),
            'car_name' => Car::where('id', $request_change_registration->car_id)->pluck('license_plate')->first(),
            'page_title' => __('lang.edit') . __('change_registrations.page_title_request'),
            'request_list' => ChangeRegisterTrait::getRequestTypeList(),
            'yes_no_list' => getYesNoList(),
            'request_type_contact_list' => ChangeRegisterTrait::getRequestTypeContactList(),
            'color_list' => registerTrait::getRegisteredColorList(),
            'optional_files' => get_medias_detail($request_change_registration->getMedia('optional_files')),
            'characteristic_transport_lists' => ChangeRegisterTrait::getCharacteristicTransportList(),
            'car_category_lists' => ChangeRegisterTrait::getCarCategoryList(),
            'optional_cancel_use_car_files' => get_medias_detail($request_change_registration->getMedia('optional_cancel_use_car_files')),
        ];
    }
}
