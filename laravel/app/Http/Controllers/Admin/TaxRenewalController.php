<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ChangeRegistrationStatusEnum;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\Resources;
use App\Enums\TaxRenewalProviderEnum;
use App\Enums\TaxRenewalStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\Creditor;
use App\Models\LongTermRental;
use App\Models\Register;
use App\Models\TaxRenewal;
use App\Rules\TelRule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaxRenewalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::TaxRenewal);
        $status_list = $this->getStatus();
        $request_list = $this->getRequestTypeList();
        $request_id = $request->request_id;
        $status = $request->status;
        $leasing = $request->leasing;
        $leasing = Creditor::find($leasing);
        $leasing_text = $leasing ? $leasing->name : '';
        $month_expire = $request->month_expire;
        $month = null;
        $year = null;
        if ($month_expire) {
            $date = Carbon::createFromFormat('m/Y', $month_expire);
            $month = $date->format('m');
            $year = $date->format('Y');
        }
        $car_class = $request->car_class;
        $car_class_model = CarClass::find($car_class);
        $car_class_text = $car_class_model && $car_class_model->full_name ? $car_class_model->full_name : null;

        $car_id = $request->car_id;

        $car_text = null;
        $car = null;
        if ($car_id) {
            $car = Car::find($car_id);
            if ($car) {
                if ($car->license_plate) {
                    $car_text = $car->license_plate;
                } else if ($car->engine_no) {
                    $car_text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $car_text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
            }
        }

        $worksheet_no = $request->worksheet_no;
        $list = TaxRenewal::leftjoin('cars', 'cars.id', '=', 'tax_renewals.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->when((isset($month) && isset($year)), function ($query) use ($month, $year) {
                $query->whereYear('tax_renewals.car_tax_exp_date', $year);
                $query->whereMonth('tax_renewals.car_tax_exp_date', $month);
            })
            ->sortable(['worksheet_no' => 'desc'])
            ->select('tax_renewals.*')
            ->search($request)
            ->paginate(PER_PAGE);
        return view('admin.tax-renewals.index', [
            'lists' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'car_text' => $car_text,
            'status' => $status,
            'worksheet_no' => $worksheet_no,
            'car' => $car,
            'request_id' => $request_id,
            'request_list' => $request_list,
            'month_expire' => $month_expire,
            'car_class' => $car_class_model,
            'car_class_text' => $car_class_text,
            'leasing' => $leasing,
            'leasing_text' => $leasing_text,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'request_cmi_date' => [
                    'required',
                ],
                'receive_cmi_date' => [
                    'required',
                ],
                'is_check_inspection' => [
                    'required',
                ],
                'is_check_lpg_ngv' => [
                    'required',
                ],
                'is_check_blue_sign' => [
                    Rule::when(isset($request->is_check_blue_sign), ['required'])
                ],
                'is_check_green_sign' => [
                    Rule::when(isset($request->is_check_green_sign), ['required'])
                ],
                'is_check_yellow_sign' => [
                    Rule::when(isset($request->is_check_yellow_sign), ['required'])
                ],
                'is_receive_documents' => [
                    'required',
                ],
                'request_registration_book_date' => [
                    'required',
                ],
                'receive_registration_book_date' => [
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


            ], [], [
                'request_cmi_date' => __('tax_renewals.request_cmi_date'),
                'receive_cmi_date' => __('tax_renewals.receive_cmi_date'),
                'is_check_inspection' => __('tax_renewals.need_inspect_vif'),
                'is_check_lpg_ngv' => __('tax_renewals.need_inspect_lpg_ngv'),
                'is_check_blue_sign' => __('tax_renewals.need_inspect_blue_sign'),
                'is_check_green_sign' => __('tax_renewals.need_inspect_green_sign'),
                'is_check_yellow_sign' => __('tax_renewals.need_inspect_yellow_sign'),
                'is_receive_documents' => __('tax_renewals.receive_document_inspect'),
                'request_registration_book_date' => __('tax_renewals.request_registration_book_date'),
                'receive_registration_book_date' => __('tax_renewals.receive_registration_book_date'),
                'memo_no' =>  __('ownership_transfers.memo_no'),
                'receipt_avance' =>  __('ownership_transfers.receipt_avance'),
                'operation_fee_avance' =>  __('ownership_transfers.operation_fee_avance'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $tax_renewal = TaxRenewal::find($request->id);
        if ($tax_renewal) {
            $tax_renewal->request_cmi_date = $request->request_cmi_date;
            $tax_renewal->receive_cmi_date = $request->receive_cmi_date;
            $tax_renewal->is_check_inspection = $request->is_check_inspection;
            $tax_renewal->is_check_lpg_ngv = $request->is_check_lpg_ngv;
            $tax_renewal->is_check_blue_sign = $request->is_check_blue_sign;
            $tax_renewal->is_receive_documents = $request->is_receive_documents[0] ?? 0;
            $tax_renewal->remark = $request->remark;
            $tax_renewal->request_registration_book_date = $request->request_registration_book_date;
            $tax_renewal->receive_registration_book_date = $request->receive_registration_book_date;
            $tax_renewal->memo_no = $request->memo_no;
            $receipt_avance = $request->receipt_avance ? str_replace(',', '', $request->receipt_avance) : null;
            $tax_renewal->receipt_avance = $receipt_avance;
            $operation_fee_avance = $request->operation_fee_avance ? str_replace(',', '', $request->operation_fee_avance) : null;
            $tax_renewal->operation_fee_avance = $operation_fee_avance;
            if (isset($request->status) && in_array($tax_renewal->status, [TaxRenewalStatusEnum::PREPARE_DOCUMENT])) {
                $tax_renewal->status = $request->status;
            }
            $tax_renewal->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $tax_renewal->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $tax_renewal->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.tax-renewals.index');
        return $this->responseValidateSuccess($redirect_route);
    }


    public function storeWaitingSendTax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'send_tax_renew_date' => [
                'required',
            ],
            'provider' => [
                'required',
            ],


        ], [], [
            'send_tax_renew_date' => __('tax_renewals.send_tax_renew_date'),
            'provider' => __('tax_renewals.provider'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $tax_renewal = TaxRenewal::find($request->id);
        if ($tax_renewal) {
            $tax_renewal->remark = $request->remark;
            $tax_renewal->tax_forwarding_date = $request->send_tax_renew_date;
            $tax_renewal->provider = $request->provider;

            if (isset($request->status) && in_array($tax_renewal->status, [TaxRenewalStatusEnum::WAITING_SEND_TAX])) {
                $tax_renewal->status = $request->status;
            }
            $tax_renewal->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $tax_renewal->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $tax_renewal->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.tax-renewals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeTaxing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receive_tax_label_date' => [
                'required',
            ],
            'car_tax_exp_date' => [
                'required',
            ],
            'link' => [
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
            'receive_tax_label_date' => __('tax_renewals.receive_tax_label_date'),
            'car_tax_exp_date' => __('registers.car_tax_exp_date'),
            'link' => __('tax_renewals.link'),
            'receipt_date' => __('registers.receipt_date'),
            'receipt_no' => __('registers.receipt_no'),
            'tax' => __('registers.tax'),
            'service_fee' => __('registers.service_fee'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $tax_renewal = TaxRenewal::find($request->id);
        if ($tax_renewal) {
            $tax_renewal->remark = $request->remark;
            $tax_renewal->receive_tax_label_date = $request->receive_tax_label_date;
            $tax_renewal->car_tax_exp_date = $request->car_tax_exp_date;
            $tax_renewal->receipt_no = $request->receipt_no;
            $tax_renewal->receipt_date = $request->receipt_date;
            $tax = $request->tax ? str_replace(',', '', $request->tax) : null;
            $tax_renewal->tax = $tax;
            $service_fee = $request->service_fee ? str_replace(',', '', $request->service_fee) : null;
            $tax_renewal->service_fee = $service_fee;
            $tax_renewal->link = $request->link;

            if (isset($request->status) && in_array($tax_renewal->status, [TaxRenewalStatusEnum::RENEWING])) {
                $tax_renewal->status = $request->status;
            }
            $tax_renewal->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $tax_renewal->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $tax_renewal->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.tax-renewals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeWaitingSendTaxRegisterBook(Request $request)
    {
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'send_tax_date' => [
                    'required',
                ],
                'ems' => [
                    'required',
                ],
                'recipient_name' => [
                    'required',
                ],
                'tel' => [
                    'required', new TelRule
                ],
                'contact' => [
                    'required',
                ],
                'return_registration_book_date' => [
                    'required',
                ],

            ], [], [
                'send_tax_date' => __('tax_renewals.send_tax_date'),
                'ems' => __('tax_renewals.ems'),
                'recipient_name' => __('tax_renewals.recipient_name'),
                'tel' => __('tax_renewals.tel'),
                'contact' => __('tax_renewals.place'),
                'return_registration_book_date' => __('tax_renewals.return_registration_book_date'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $tax_renewal = TaxRenewal::find($request->id);
        if ($tax_renewal) {
            $tax_renewal->remark = $request->remark;
            $tax_renewal->send_tax_date = $request->send_tax_date;
            $tax_renewal->ems = $request->ems;
            $tax_renewal->recipient_name = $request->recipient_name;
            $tax_renewal->tel = $request->tel;
            $tax_renewal->contact = $request->contact;
            $tax_renewal->return_registration_book_date = $request->return_registration_book_date;

            if (isset($request->status) && in_array($tax_renewal->status, [TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK])) {
                $tax_renewal->status = $request->status;
            }
            $tax_renewal->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $tax_renewal->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $tax_renewal->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.tax-renewals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }
        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }
        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.view') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.prepare-info-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'view' => true,
        ]);
    }

    public function edit(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }
        if ($register) {
            $tax_renewal->registered_sign = $register->registered_sign ?? '-';
        }
        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }
        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.edit') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.prepare-info-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
        ]);
    }

    public function editWaitingSendTaxRenew(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }

        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }

        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.edit') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.waiting-send-tax-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
        ]);
    }

    public function showWaitingSendTaxRenew(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }
        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }

        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.view') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.waiting-send-tax-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'view' => true,
        ]);
    }

    public function editTaxing(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }

        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }
        $link_default = "\\" . "\\" . "tls-lkb-kamonw8\สำเนาทะเบียนรถ\สำเนาทะเบียนต่อภาษี";

        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.edit') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.taxing-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'link_default' => $link_default,
        ]);
    }

    public function showTaxing(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }
        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }

        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.view') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.taxing-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'view' => true,
        ]);
    }

    public function editWaitingSendTaxRegisterBook(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }

        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }

        $tax_history = TaxRenewal::where('status', TaxRenewalStatusEnum::SUCCESS)->where('car_id', $tax_renewal->car_id)->get();
        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.edit') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.waiting-send-tax-register-book-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'tax_history' => $tax_history,
        ]);
    }

    public function showWaitingSendTaxRegisterBook(TaxRenewal $tax_renewal)
    {
        $car = Car::find($tax_renewal->car_id);
        $register = Register::where('car_id', $tax_renewal->car_id)->first();
        $tax_renewal->car_age_start =  null;
        $tax_renewal->registered_date = null;
        if ($car) {
            if ($car->start_date) {
                $car_age_start = Carbon::now()->diff($car->start_date);
                $tax_renewal->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            }
            if ($car->registered_date) {
                $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
                $tax_renewal->registered_date = $registered_date ? $registered_date->format('d/m/Y') : null;
            }
        }

        if ($register && $register->job_type == LongTermRental::class) {
            $lt_rental = LongTermRental::find($register->job_id);
            if ($lt_rental) {
                $tax_renewal->receive_car_name =  $lt_rental->name_user_receive;
                $tax_renewal->receive_car_tel =  $lt_rental->phone_user_receive;
            }
        }
        $tax_history = TaxRenewal::where('status', TaxRenewalStatusEnum::SUCCESS)->where('car_id', $tax_renewal->car_id)->get();
        $provider_list = $this->getProviderList();
        $optional_files = $tax_renewal->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $page_title = __('lang.view') . __('tax_renewals.page_title');
        $url = 'admin.tax-renewals.index';
        $tax_renewal->step = $this->setProgressStep($tax_renewal->status);
        return view('admin.tax-renewals.waiting-send-tax-register-book-form', [
            'd' => $tax_renewal,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'car' => $car,
            'register' => $register,
            'provider_list' => $provider_list,
            'tax_history' => $tax_history,
            'view' => true,
        ]);
    }

    public static function getStatus()
    {
        $status = collect([
            (object) [
                'id' => TaxRenewalStatusEnum::PREPARE_DOCUMENT,
                'name' => __('tax_renewals.status_' . TaxRenewalStatusEnum::PREPARE_DOCUMENT . '_text'),
                'value' => TaxRenewalStatusEnum::PREPARE_DOCUMENT,
            ],
            (object) [
                'id' => TaxRenewalStatusEnum::WAITING_SEND_TAX,
                'name' => __('tax_renewals.status_' . TaxRenewalStatusEnum::WAITING_SEND_TAX . '_text'),
                'value' => TaxRenewalStatusEnum::WAITING_SEND_TAX,
            ],
            (object) [
                'id' => TaxRenewalStatusEnum::RENEWING,
                'name' => __('tax_renewals.status_' . TaxRenewalStatusEnum::RENEWING . '_text'),
                'value' => TaxRenewalStatusEnum::RENEWING,
            ],
            (object) [
                'id' => TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK,
                'name' => __('tax_renewals.status_' . TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK . '_text'),
                'value' => TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK,
            ],
            (object) [
                'id' => TaxRenewalStatusEnum::SUCCESS,
                'name' => __('tax_renewals.status_' . TaxRenewalStatusEnum::SUCCESS . '_text'),
                'value' => TaxRenewalStatusEnum::SUCCESS,
            ],
        ]);
        return $status;
    }

    public static function getProviderList()
    {
        $status = collect([
            (object) [
                'id' => TaxRenewalProviderEnum::KITJAROEN,
                'name' => __('tax_renewals.provider_' . TaxRenewalProviderEnum::KITJAROEN),
                'value' => TaxRenewalProviderEnum::KITJAROEN,
            ],
            (object) [
                'id' => TaxRenewalProviderEnum::THOUCHCHAI,
                'name' => __('tax_renewals.provider_' . TaxRenewalProviderEnum::THOUCHCHAI),
                'value' => TaxRenewalProviderEnum::THOUCHCHAI,
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

    private function setProgressStep($status)
    {
        $step = 0;
        if (in_array($status, [TaxRenewalStatusEnum::PREPARE_DOCUMENT])) {
            $step = 0;
        } elseif (in_array($status, [TaxRenewalStatusEnum::WAITING_SEND_TAX])) {
            $step = 1;
        } elseif (in_array($status, [TaxRenewalStatusEnum::RENEWING])) {
            $step = 2;
        } elseif (in_array($status, [TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK])) {
            $step = 3;
        } elseif (in_array($status, [TaxRenewalStatusEnum::SUCCESS])) {
            $step = 4;
        }
        return $step;
    }
}
