<?php

namespace App\Traits;

use App\Enums\ChangeRegistrationCarOwnTypeEnum;
use App\Enums\ChangeRegistrationRequestTypeContactEnum;
use App\Enums\ChangeRegistrationStatusEnum;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\RegisterColorEnum;
use App\Enums\TransferTypeEnum;
use App\Models\CarCategory;
use App\Models\CarCharacteristicTransport;
use App\Rules\TelRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

trait ChangeRegisterTrait
{
    public static function getStatus()
    {
        $status = collect([
            (object) [
                'id' => ChangeRegistrationStatusEnum::WAITING_DOCUMENT,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::WAITING_DOCUMENT . '_text'),
                'value' => ChangeRegistrationStatusEnum::WAITING_DOCUMENT,
            ],
            (object) [
                'id' => ChangeRegistrationStatusEnum::WAITING_SEND_DLT,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::WAITING_SEND_DLT . '_text'),
                'value' => ChangeRegistrationStatusEnum::WAITING_SEND_DLT,
            ],
            (object) [
                'id' => ChangeRegistrationStatusEnum::PROCESSING,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::PROCESSING . '_text'),
                'value' => ChangeRegistrationStatusEnum::PROCESSING,
            ],
            (object) [
                'id' => ChangeRegistrationStatusEnum::SUCCESS,
                'name' => __('change_registrations.status_' . ChangeRegistrationStatusEnum::SUCCESS . '_text'),
                'value' => ChangeRegistrationStatusEnum::SUCCESS,
            ],
        ]);
        return $status;
    }

    public static function getRequestTypeContactList()
    {
        $status = collect([
            (object) [
                'id' => ChangeRegistrationRequestTypeContactEnum::CUSTOMER,
                'name' => __('change_registrations.request_type_contact_' . ChangeRegistrationRequestTypeContactEnum::CUSTOMER),
                'value' => ChangeRegistrationRequestTypeContactEnum::CUSTOMER,
            ],
            (object) [
                'id' => ChangeRegistrationRequestTypeContactEnum::TLS,
                'name' => __('change_registrations.request_type_contact_' . ChangeRegistrationRequestTypeContactEnum::TLS),
                'value' => ChangeRegistrationRequestTypeContactEnum::TLS,
            ],
        ]);
        return $status;
    }

    public static function getCarOwnTypeList()
    {
        $status = collect([
            (object) [
                'id' => ChangeRegistrationCarOwnTypeEnum::JURISTIC_PERSON,
                'name' => __('change_registrations.car_own_type_' . ChangeRegistrationCarOwnTypeEnum::JURISTIC_PERSON),
                'value' => ChangeRegistrationCarOwnTypeEnum::JURISTIC_PERSON,
            ],
            (object) [
                'id' => ChangeRegistrationCarOwnTypeEnum::PERSON,
                'name' => __('change_registrations.car_own_type_' . ChangeRegistrationCarOwnTypeEnum::PERSON),
                'value' => ChangeRegistrationCarOwnTypeEnum::PERSON,
            ],
        ]);
        return $status;
    }


    public static function getRequestTypeList()
    {
        $status = collect([
            (object) [
                'id' => ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY . '_text'),
                'value' => ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY,
            ],
            (object) [
                'id' => ChangeRegistrationTypeEnum::CHANGE_COLOR,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_COLOR . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_COLOR,
            ],
            (object) [
                'id' => ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC,
            ],
            (object) [
                'id' => ChangeRegistrationTypeEnum::CHANGE_TYPE,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CHANGE_TYPE . '_text'),
                'value' => ChangeRegistrationTypeEnum::CHANGE_TYPE,
            ],
            (object) [
                'id' => ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE . '_text'),
                'value' => ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE,
            ],
            (object) [
                'id' => ChangeRegistrationTypeEnum::CANCEL_USE_CAR,
                'name' => __('change_registrations.request_type_' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR . '_text'),
                'value' => ChangeRegistrationTypeEnum::CANCEL_USE_CAR,
            ],
        ]);
        return $status;
    }

    public static function getCharacteristicTransportList()
    {
        $characteristic_transport_lists = CarCharacteristicTransport::select('id', 'name')->get();
        return $characteristic_transport_lists;
    }

    public static function getCarCategoryList()
    {
        $car_category_list = CarCategory::select('id', 'name')->get();
        return $car_category_list;
    }

    public static function validateLicensePlateTaxCopy($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date' => [
                'required',
            ],
            'is_tax_sign' => [
                'required',
            ],
            'amount_tax_sign' =>[
                Rule::when(filter_var($request->is_tax_sign, FILTER_VALIDATE_BOOLEAN), ['required'])
            ],
            'is_license_plate' => [
                'required',
            ],
            'amount_license_plate' =>[
                Rule::when(filter_var($request->is_license_plate, FILTER_VALIDATE_BOOLEAN), ['required'])
            ] ,
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'name_recipient' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_recipient' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_recipient' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'address_recipient' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])
            ],
            'tel_recipient_tls' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_recipient_tls' => [
                Rule::when($request->requester_type_recipient === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])

            ],
            'name_receipt' => [
                'required',
            ],
            'tax_no_receipt' => [
                'required',
            ],
            'tel_receipt' => [
                'required',new TelRule
            ],
            'email_receipt' => [
                'required', 'email'
            ],
            'address_receipt' => [
                'required',
            ],
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date' => __('change_registrations.receive_case_date'),
            'is_tax_sign' => __('change_registrations.is_tax_sign'),
            'amount_tax_sign' => __('change_registrations.amount_tax_sign'),
            'is_license_plate' => __('change_registrations.is_license_plate'),
            'amount_license_plate' => __('change_registrations.amount_license_plate'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
            'name_recipient' => __('change_registrations.name_contact'),
            'tel_recipient' => __('change_registrations.tel_contact'),
            'email_recipient' => __('change_registrations.email_contact'),
            'address_recipient' => __('change_registrations.address_contact'),
            'tel_recipient_tls' => __('change_registrations.tel_contact'),
            'email_recipient_tls' => __('change_registrations.email_contact'),
            'name_receipt' => __('change_registrations.name_contact'),
            'tax_no_receipt' => __('change_registrations.tax_no_receipt'),
            'tel_receipt' => __('change_registrations.tel_contact'),
            'email_receipt' => __('change_registrations.email_contact'),
            'address_receipt' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

    public static function validateChangeColor($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date_color' => [
                'required',
            ],
            'color_change' => [
                'required',
            ],
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date_color' => __('change_registrations.receive_case_date'),
            'color_change' => __('change_registrations.color_change'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

    public static function validateChangeCharacteristic($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date_characteristic' => [
                'required',
            ],
            'detail_change_characteristic' => [
                'required',
            ],
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date_characteristic' => __('change_registrations.receive_case_date'),
            'detail_change_characteristic' => __('change_registrations.change_characteristic_detail'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

    public static function validateChangeType($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date_type' => [
                'required',
            ],
            'detail_change_type' => [
                'required',
            ],
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date_type' => __('change_registrations.receive_case_date'),
            'detail_change_type' => __('change_registrations.change_characteristic_detail'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

    public static function validateSwapLicensePlate($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date_swap' => [
                'required',
            ],
            'is_car_alternate_tls' => [
                'required',
            ],
            'car_owner_type' => [
                'required',
            ],
            'car_swap' => [
                'required',
            ],
            'car_class_swap' => [
                'required',
            ],
            'engine_no' => [
                'required',
            ],
            'chassis_no' => [
                'required',
            ],
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],

            
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date_swap' => __('change_registrations.receive_case_date'),
            'is_car_alternate_tls' => __('change_registrations.is_car_alternate_tls'),
            'car_owner_type' => __('change_registrations.car_owner_type'),
            'car_swap' => __('change_registrations.car_swap'),
            'car_class_swap' => __('change_registrations.car_class'),
            'engine_no' => __('change_registrations.engine_no'),
            'chassis_no' => __('change_registrations.chassis_no'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

    public static function validateCancelUseCar($request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'receive_case_date_cancel_use_car' => [
                'required',
            ],
            'requester_type_contact' => [
                'required',
            ],
            'name_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
            'tel_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required',new TelRule])
            ],
            'email_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required', 'email'])
            ],
            'tel_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required',new TelRule])
            ],
            'email_contact_tls' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::TLS, ['required', 'email'])
            ],
            'address_contact' => [
                Rule::when($request->requester_type_contact === ChangeRegistrationRequestTypeContactEnum::CUSTOMER, ['required'])

            ],
        ], [], [
            'car_id' => __('change_registrations.license_plate'),
            'receive_case_date_cancel_use_car' => __('change_registrations.receive_case_date'),
            'detail_change_type' => __('change_registrations.change_characteristic_detail'),
            'requester_type_contact' => __('change_registrations.requester_type_contact'),
            'name_contact' => __('change_registrations.name_contact'),
            'tel_contact' => __('change_registrations.tel_contact'),
            'email_contact' => __('change_registrations.email_contact'),
            'tel_contact_tls' => __('change_registrations.tel_contact'),
            'email_contact_tls' => __('change_registrations.email_contact'),
            'address_contact' => __('change_registrations.address_contact'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $validator;
        }
    }

}
