<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ChangeRegistrationRequestTypeContactEnum;
use App\Enums\ChangeRegistrationStatusEnum;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\ChangeRegistration;
use App\Models\Creditor;
use App\Models\HirePurchase;
use App\Models\Leasing;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\RegisterTrait;
use App\Traits\ChangeRegisterTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RequestChangeRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RequestChangeRegistration);
        $status_list = ChangeRegisterTrait::getStatus();
        $request_list = ChangeRegisterTrait::getRequestTypeList();
        $request_id = $request->request_id;
        $leasing = $request->leasing;
        $status = $request->status;
        $car_id = $request->car_id;

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
            ->leftjoin('insurance_lots', 'insurance_lots.id', '=', 'hire_purchases_db.lot_id')
            ->sortable(['created_at' => 'desc'])
            ->select('change_registrations.*')
            ->search($request)
            ->paginate(PER_PAGE);
        return view('admin.request-change-registrations.index', [
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

    public function create()
    {
        $d = new ChangeRegistration();
        $d->type = ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY;
        $page_title = __('lang.create') . __('change_registrations.page_title_request');
        $request_list = ChangeRegisterTrait::getRequestTypeList();
        $yes_no_list = getYesNoList();
        $request_type_contact_list = ChangeRegisterTrait::getRequestTypeContactList();
        $characteristic_transport_lists = ChangeRegisterTrait::getCharacteristicTransportList();
        $car_category_lists = ChangeRegisterTrait::getCarCategoryList();
        $color_list = registerTrait::getRegisteredColorList();
        $url = 'admin.request-change-registrations.index';
        return view('admin.request-change-registrations.form', [
            'page_title' => $page_title,
            'd' => $d,
            'optional_files' => [],
            'url' => $url,
            'car_body_color_files' => [],
            'receipt_change_color_files' => [],
            'car_body_characteristic_files' => [],
            'receipt_change_characteristic_files' => [],
            'car_body_type_files' => [],
            'receipt_change_type_files' => [],
            'car_name' => null,
            'user_name_recipient' => [],
            'user_name_contact' => [],
            'register_files' => [],
            'power_attorney_files' => [],
            'letter_consent_files' => [],
            'citizen_files' => [],
            'optional_cancel_use_car_files' => [],
            'color_list' => $color_list,
            'request_type_contact_list' => $request_type_contact_list,
            'yes_no_list' => $yes_no_list,
            'request_list' => $request_list,
            'characteristic_transport_lists' => $characteristic_transport_lists,
            'car_category_lists' => $car_category_lists,
            'car_class_name' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_type_id' => [
                'required',
            ],
        ], [], [
            'request_type_id' => __('change_registrations.job_type'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY) === 0) {
            $validateLicensePlate = ChangeRegisterTrait::validateLicensePlateTaxCopy($request);
            if ($validateLicensePlate) {
                return $this->responseValidateFailed($validateLicensePlate);
            }
            $this->storeLicensePlateTaxCopy($request);
        } else if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::CHANGE_COLOR) === 0) {
            $validateChangeColor = ChangeRegisterTrait::validateChangeColor($request);
            if ($validateChangeColor) {
                return $this->responseValidateFailed($validateChangeColor);
            }
            $this->storeChangeColor($request);
        } else if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC) === 0) {
            $validateChangeCharacteristic = ChangeRegisterTrait::validateChangeCharacteristic($request);
            if ($validateChangeCharacteristic) {
                return $this->responseValidateFailed($validateChangeCharacteristic);
            }
            $this->storeCharacteristic($request);
        } else if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::CHANGE_TYPE) === 0) {
            $validateChangeType = ChangeRegisterTrait::validateChangeType($request);
            if ($validateChangeType) {
                return $this->responseValidateFailed($validateChangeType);
            }
            $this->storeType($request);
        } else if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE) === 0) {
            $validateSwapLicensePlate = ChangeRegisterTrait::validateSwapLicensePlate($request);
            if ($validateSwapLicensePlate) {
                return $this->responseValidateFailed($validateSwapLicensePlate);
            }
            $this->storeSwapLicensePlate($request);
        } else if (strcmp($request->request_type_id, ChangeRegistrationTypeEnum::CANCEL_USE_CAR) === 0) {
            $validateCancelUseCar = ChangeRegisterTrait::validateCancelUseCar($request);
            if ($validateCancelUseCar) {
                return $this->responseValidateFailed($validateCancelUseCar);
            }
            $this->storeCancelUseCar($request);
        }

        $redirect_route = route('admin.request-change-registrations.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeLicensePlateTaxCopy($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);
        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date;
        $request_change_registration->remark = $request->remark;
        $request_change_registration->is_tax_sign = $request->is_tax_sign;
        $request_change_registration->amount_tax_sign = $request->amount_tax_sign;
        $request_change_registration->is_license_plate = $request->is_license_plate;
        $request_change_registration->amount_license_plate = $request->amount_license_plate;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->requester_type_recipient = $request->requester_type_recipient;
        $this->storeRecipient($request_change_registration, $request);
        $this->storeReceipt($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->optional_files__pending_delete_ids) {
            $pending_delete_ids = $request->optional_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('optional_files')) {
            foreach ($request->file('optional_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('optional_files');
                }
            }
        }
    }

    public function storeChangeColor($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);

        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date_color;
        $request_change_registration->detail_change = $request->color_change;
        $request_change_registration->remark = $request->remark_color;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->car_body_color_files__pending_delete_ids) {
            $pending_delete_ids = $request->car_body_color_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('car_body_color_files')) {
            foreach ($request->file('car_body_color_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('car_body_color_files');
                }
            }
        }

        if ($request->receipt_change_color_files__pending_delete_ids) {
            $pending_delete_ids = $request->receipt_change_color_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('receipt_change_color_files')) {
            foreach ($request->file('receipt_change_color_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('receipt_change_color_files');
                }
            }
        }
    }

    public function storeCharacteristic($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);
        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date_characteristic;
        $request_change_registration->detail_change = $request->detail_change_characteristic;
        $request_change_registration->remark = $request->remark_characteristic;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->car_body_characteristic_files__pending_delete_ids) {
            $pending_delete_ids = $request->car_body_characteristic_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('car_body_characteristic_files')) {
            foreach ($request->file('car_body_characteristic_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('car_body_characteristic_files');
                }
            }
        }

        if ($request->receipt_change_characteristic_files__pending_delete_ids) {
            $pending_delete_ids = $request->receipt_change_characteristic_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('receipt_change_characteristic_files')) {
            foreach ($request->file('receipt_change_characteristic_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('receipt_change_characteristic_files');
                }
            }
        }
    }

    public function storeType($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);

        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date_type;
        $request_change_registration->detail_change = $request->detail_change_type;
        $request_change_registration->remark = $request->remark_type;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->car_body_type_files__pending_delete_ids) {
            $pending_delete_ids = $request->car_body_type_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('car_body_type_files')) {
            foreach ($request->file('car_body_type_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('car_body_type_files');
                }
            }
        }

        if ($request->receipt_change_type_files__pending_delete_ids) {
            $pending_delete_ids = $request->receipt_change_type_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('receipt_change_type_files')) {
            foreach ($request->file('receipt_change_type_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('receipt_change_type_files');
                }
            }
        }
    }

    public function storeSwapLicensePlate($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);
        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date_swap;
        $request_change_registration->is_car_alternate_tls = (filter_var($request->is_car_alternate_tls, FILTER_VALIDATE_BOOLEAN));
        $request_change_registration->car_owner_type = $request->car_owner_type;
        $request_change_registration->car_swap = $request->car_swap;
        $request_change_registration->car_class = $request->car_class_swap;
        $request_change_registration->engine_no = $request->engine_no;
        $request_change_registration->chassis_no = $request->chassis_no;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->register_files__pending_delete_ids) {
            $pending_delete_ids = $request->register_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('register_files')) {
            foreach ($request->file('register_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('register_files');
                }
            }
        }

        if ($request->power_attorney_files__pending_delete_ids) {
            $pending_delete_ids = $request->power_attorney_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('power_attorney_files')) {
            foreach ($request->file('power_attorney_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('power_attorney_files');
                }
            }
        }

        if ($request->letter_consent_files__pending_delete_ids) {
            $pending_delete_ids = $request->letter_consent_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('letter_consent_files')) {
            foreach ($request->file('letter_consent_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('letter_consent_files');
                }
            }
        }

        if ($request->citizen_files__pending_delete_ids) {
            $pending_delete_ids = $request->citizen_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('citizen_files')) {
            foreach ($request->file('citizen_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('citizen_files');
                }
            }
        }
    }

    public function storeCancelUseCar($request)
    {
        $request_change_registration = ChangeRegistration::firstOrNew(['id' => $request->id]);
        $hire_purchase = HirePurchase::where('car_id', $request->car_id)->first();
        $request_change_registration->type = $request->request_type_id;
        $request_change_registration->hire_purchase_id = $hire_purchase ? $hire_purchase->id : null;
        $request_change_registration->car_id = $request->car_id;
        $request_change_registration->receive_case_date = $request->receive_case_date_cancel_use_car;
        $request_change_registration->remark = $request->remark_cancel_use_car;
        $request_change_registration->requester_type_contact = $request->requester_type_contact;
        $this->storeContact($request_change_registration, $request);
        $request_change_registration->status = ChangeRegistrationStatusEnum::WAITING_DOCUMENT;
        $request_change_registration->save();

        if ($request->optional_cancel_use_car_files__pending_delete_ids) {
            $pending_delete_ids = $request->optional_cancel_use_car_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_change_registration->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('optional_cancel_use_car_files')) {
            foreach ($request->file('optional_cancel_use_car_files') as $file) {
                if ($file->isValid()) {
                    $request_change_registration->addMedia($file)->toMediaCollection('optional_cancel_use_car_files');
                }
            }
        }
    }

    public function storeContact($model, $request)
    {

        if (strcmp($request->requester_type_contact, ChangeRegistrationRequestTypeContactEnum::CUSTOMER) === 0) {
            $model->name_contact = $request->name_contact;
            $model->tel_contact = $request->tel_contact;
            $model->email_contact = $request->email_contact;
            $model->address_contact = $request->address_contact;
        } elseif (strcmp($request->requester_type_contact, ChangeRegistrationRequestTypeContactEnum::TLS) === 0) {
            $model->contact_user_id = $request->contact_user_id;
            $model->tel_contact = $request->tel_contact_tls;
            $model->email_contact = $request->email_contact_tls;
        }
    }

    public function storeRecipient($model, $request)
    {
        if (strcmp($request->requester_type_recipient, ChangeRegistrationRequestTypeContactEnum::CUSTOMER) === 0) {
            $model->name_recipient = $request->name_recipient;
            $model->tel_recipient = $request->tel_recipient;
            $model->email_recipient = $request->email_recipient;
            $model->address_recipient = $request->address_recipient;
        } elseif (strcmp($request->requester_type_recipient, ChangeRegistrationRequestTypeContactEnum::TLS) === 0) {
            $model->recipient_user_id = $request->recipient_user_id;
            $model->tel_recipient = $request->tel_recipient_tls;
            $model->email_recipient = $request->email_recipient_tls;
        }
    }

    public function storeReceipt($model, $request)
    {
        $model->name_receipt = $request->name_receipt;
        $model->tax_no_receipt = $request->tax_no_receipt;
        $model->tel_receipt = $request->tel_receipt;
        $model->email_receipt = $request->email_receipt;
        $model->address_receipt = $request->address_receipt;
    }

    public function show(ChangeRegistration $request_change_registration)
    {
        $data = $this->getListAll($request_change_registration);
        return view('admin.request-change-registrations.form', [
            'page_title' => __('lang.view') . __('change_registrations.page_title_request'),
            'd' => $request_change_registration,
            'optional_files' => $data['optional_files'],
            'car_name' => $data['car_name'],
            'request_type_contact_list' => $data['request_type_contact_list'],
            'yes_no_list' => $data['yes_no_list'],
            'request_list' => $data['request_list'],
            'color_list' => $data['color_list'],
            'user_name_contact' => $data['user_name_contact'],
            'user_name_recipient' => $data['user_name_recipient'],
            'car_body_color_files' => $data['car_body_color_files'],
            'receipt_change_color_files' => $data['receipt_change_color_files'],
            'url' => 'admin.request-change-registrations.index',
            'characteristic_transport_lists' => $data['characteristic_transport_lists'],
            'car_body_characteristic_files' => $data['car_body_characteristic_files'],
            'receipt_change_characteristic_files' => $data['receipt_change_characteristic_files'],
            'car_class_name' => $data['car_class_name'],
            'car_body_type_files' => $data['car_body_type_files'],
            'car_category_lists' => $data['car_category_lists'],
            'receipt_change_type_files' => $data['receipt_change_type_files'],
            'register_files' => $data['register_files'],
            'power_attorney_files' => $data['power_attorney_files'],
            'letter_consent_files' => $data['letter_consent_files'],
            'citizen_files' => $data['citizen_files'],
            'optional_cancel_use_car_files' => $data['optional_cancel_use_car_files'],
            'view' => true,
        ]);
    }

    public function edit(ChangeRegistration $request_change_registration)
    {
        $data = $this->getListAll($request_change_registration);
        return view('admin.request-change-registrations.form', [
            'page_title' => __('lang.edit') . __('change_registrations.page_title_request'),
            'd' => $request_change_registration,
            'optional_files' => $data['optional_files'],
            'car_name' => $data['car_name'],
            'request_type_contact_list' => $data['request_type_contact_list'],
            'yes_no_list' => $data['yes_no_list'],
            'request_list' => $data['request_list'],
            'color_list' => $data['color_list'],
            'user_name_contact' => $data['user_name_contact'],
            'user_name_recipient' => $data['user_name_recipient'],
            'car_body_color_files' => $data['car_body_color_files'],
            'receipt_change_color_files' => $data['receipt_change_color_files'],
            'url' => 'admin.request-change-registrations.index',
            'characteristic_transport_lists' => $data['characteristic_transport_lists'],
            'car_body_characteristic_files' => $data['car_body_characteristic_files'],
            'receipt_change_characteristic_files' => $data['receipt_change_characteristic_files'],
            'car_class_name' => $data['car_class_name'],
            'car_body_type_files' => $data['car_body_type_files'],
            'car_category_lists' => $data['car_category_lists'],
            'receipt_change_type_files' => $data['receipt_change_type_files'],
            'register_files' => $data['register_files'],
            'power_attorney_files' => $data['power_attorney_files'],
            'letter_consent_files' => $data['letter_consent_files'],
            'citizen_files' => $data['citizen_files'],
            'optional_cancel_use_car_files' => $data['optional_cancel_use_car_files'],
        ]);

        return redirect()->route('admin.request-change-registrations.index');
    }


    public function getListAll($request_change_registration)
    {
        $car = Car::find($request_change_registration->car_id);
        return [
            'user_name_contact' => User::where('id', $request_change_registration->contact_user_id)->pluck('name')->first(),
            'user_name_recipient' => User::where('id', $request_change_registration->recipient_user_id)->pluck('name')->first(),
            'car_class_name' => CarClass::where('id', $car->car_class_id)->pluck('full_name')->first(),
            'car_name' => Car::where('id', $request_change_registration->car_id)->pluck('license_plate')->first(),
            'request_list' => ChangeRegisterTrait::getRequestTypeList(),
            'yes_no_list' => getYesNoList(),
            'request_type_contact_list' => ChangeRegisterTrait::getRequestTypeContactList(),
            'color_list' => registerTrait::getRegisteredColorList(),
            'optional_files' => get_medias_detail($request_change_registration->getMedia('optional_files')),
            'car_body_color_files' => get_medias_detail($request_change_registration->getMedia('car_body_color_files')),
            'receipt_change_color_files' => get_medias_detail($request_change_registration->getMedia('receipt_change_color_files')),
            'characteristic_transport_lists' => ChangeRegisterTrait::getCharacteristicTransportList(),
            'car_body_characteristic_files' => get_medias_detail($request_change_registration->getMedia('car_body_characteristic_files')),
            'receipt_change_characteristic_files' => get_medias_detail($request_change_registration->getMedia('receipt_change_characteristic_files')),
            'car_category_lists' => ChangeRegisterTrait::getCarCategoryList(),
            'car_body_type_files' => get_medias_detail($request_change_registration->getMedia('car_body_type_files')),
            'receipt_change_type_files' => get_medias_detail($request_change_registration->getMedia('receipt_change_type_files')),
            'register_files' => get_medias_detail($request_change_registration->getMedia('register_files')),
            'power_attorney_files' => get_medias_detail($request_change_registration->getMedia('power_attorney_files')),
            'letter_consent_files' => get_medias_detail($request_change_registration->getMedia('letter_consent_files')),
            'citizen_files' => get_medias_detail($request_change_registration->getMedia('citizen_files')),
            'optional_cancel_use_car_files' => get_medias_detail($request_change_registration->getMedia('optional_cancel_use_car_files')),
        ];
    }

    public function getDefaultDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['branch'] = null;
        $data['engine_no'] = ($car) ? $car->engine_no : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;
        $data['cc'] = ($car) ? $car->engine_size : null;;
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
        $data['car_characteristic_transport'] = ($car && $car->register && $car->register->carCharacteristicTransport) ? $car->register->carCharacteristicTransport->name : null;
        $data['color_registered'] = ($car && $car->register) ? __('registers.registered_color_' . $car->register->color_registered) : null;
        $data['registered_sign'] = ($car && $car->register) ? __('registers.registered_sign_type_' . $car->register->registered_sign) : null;
        $data['car_category'] = ($car && $car->carCategory) ? $car->carCategory->name : null;
        $data['leasing'] = ($car && $car->creditor) ? $car->creditor->name : null;
        $data['status'] = ($car && $car->status) ? __('cars.status_' . $car->status) : null;
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
