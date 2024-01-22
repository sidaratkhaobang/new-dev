<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccidentSlideEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\AccidentTypeEnum;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\CaseAccidentEnum;
use App\Enums\ClaimantAccidentEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\RepairClaimEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\Resources;
use App\Enums\ResponsibleEnum;
use App\Enums\RightsEnum;
use App\Enums\SlideTypeEnum;
use App\Enums\WoundType;
use App\Enums\ZoneEnum;
use App\Http\Controllers\Controller;
use App\Jobs\Accident as JobsAccident;
use App\Jobs\EmailJob;
use App\Models\Accident;
use App\Models\AccidentSlide;
use App\Models\Car;
use App\Models\Cradle;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\Province;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Traits\CompensationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AccidentTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use App\Jobs\AccidentJob;
use App\Models\AccidentClaimLines;
use App\Models\AccidentExpense;
use App\Models\Amphure;
use App\Models\ClaimList;
use App\Models\District;
use App\Models\ReplacementCar;
use App\Models\Slide;
use App\Traits\RepairTrait;
use Illuminate\Support\Facades\Date;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\CarAuctionTrait;
use Illuminate\Support\Facades\Auth;

class AccidentInformController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentInform);
        $worksheet = $request->worksheet;
        $accident_type = $request->accident_type;
        $license_plate = $request->license_plate;
        $status = $request->status;
        $list = Accident::sortable(['worksheet_no' => 'desc'])
            ->search($request)
            ->select('accidents.*')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $car = Car::find($item->car_id);
            if ($car) {
                if ($car->license_plate) {
                    $text = $car->license_plate;
                } else if ($car->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
                $item->license_plate = $text;
            }

            // rental
            $worksheet = RentalLine::where('car_id', $car->id)
                ->whereDate('pickup_date', '<=', Carbon::now())
                ->whereDate('return_date', '>=', Carbon::now())
                ->first();

            // lt rental
            if (is_null($worksheet)) {
                $worksheet = null;
                $lt_rental_car = LongTermRentalPRCar::where('car_id', $car->id)
                    ->first();
                if ($lt_rental_car) {
                    $lt_pr_line = LongTermRentalPRLine::where('id', $lt_rental_car->lt_rental_pr_line_id)->first();
                    if ($lt_pr_line) {
                        $worksheet = LongTermRental::find($lt_pr_line->lt_rental_id);
                    }
                }
            } else {
                $worksheet = Rental::find($worksheet->rental_id);
            }

            if ($worksheet) {
                $item->customer_name = $worksheet->customer_name;
            }

            return $item;
        });

        $license_plate_text = Car::where('id', $license_plate)->value('license_plate');
        $worksheet_text = Accident::where('id', $worksheet)->value('worksheet_no');
        // $worksheet_list = Accident::select('id', 'worksheet_no as name')->get();
        $accident_type_list = AccidentTrait::getAccidentTypeIndexList();
        $status_list = AccidentTrait::getStatusJobList();

        return view('admin.accident-informs.index', [
            'list' => $list,
            // 'worksheet_list' => $worksheet_list,
            'accident_type_list' => $accident_type_list,
            'status_list' => $status_list,
            'worksheet' => $worksheet,
            'accident_type' => $accident_type,
            'license_plate' => $license_plate,
            'status' => $status,
            'worksheet_text' => $worksheet_text,
            'license_plate_text' => $license_plate_text,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentInform);
        $d = new Accident();
        $d->is_replacement = BOOL_FALSE;
        $d->report_no = "N/A";
        $d->report_date = Carbon::now();
        $d->accident_date = Carbon::now();
        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();

        $need_list = AccidentTrait::getNeedList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $replace_list = AccidentTrait::getReplacementList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();
        $province_name = null;
        $amphure_name =  null;
        $district_name =  null;
        $optional_files = [];
        $replacement_car_files = [];
        $license_plate_list = Car::whereNotIn('status', [CarEnum::ACCIDENT, CarEnum::SOLD_OUT])->get();
        $license_plate_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });
        $page_title = __('lang.create') . __('accident_informs.page_title');
        return view('admin.accident-informs.form', [
            'page_title' => $page_title,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'license_plate_list' => $license_plate_list,
            'd' => $d,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'need_list' => $need_list,
            'optional_files' => $optional_files,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'replace_list' => $replace_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'accident_type' => [
                'required',
            ],
            // 'claim_type' => [
            //     'required',
            // ],
            // 'claim_by' => [
            //     'required',
            // ],
            'report_date' => [
                'required',
            ],
            'reporter' => [
                'required',
            ],
            'report_tel' => [
                'required', 'numeric', 'digits:10',
            ],
            // 'report_no' => [
            //     'required',
            // ],
            'license_plate' => [
                'required',
            ],
            'accident_date' => [
                'required',
            ],
            'driver' => [
                'required',
            ],
            'main_area' => [
                'required',
            ],
            'case' => [
                'required',
            ],
            'accident_description' => [
                'required',
            ],
            'accident_place' => [
                'required',
            ],
            // 'current_place' => [
            //     'required',
            // ],
            'region' => [
                'required',
            ],
            'province' => [
                'required',
            ],
            // 'district' => [
            //     'required',
            // ],
            // 'subdistrict' => [
            //     'required',
            // ],

            // 'wrong_type' => [Rule::when($request->is_parties == STATUS_ACTIVE, ['required'])],
            'amount_wounded_driver' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],
            'amount_wounded_parties' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],

            'amount_deceased_driver' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            'amount_deceased_parties' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            // 'place_employee' => [Rule::when($request->is_driver_employee == STATUS_ACTIVE, ['required'])],
            'cradle' => [Rule::when($request->is_repair == STATUS_ACTIVE, ['required'])],

            'first_lifter' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_date' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_price' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_tel' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required', 'numeric', 'digits:10'])],

            'lift_date' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_from' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_to' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_price' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],

            'replacement_expect_date' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_type' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'is_driver_replacement' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_expect_place' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
        ], [], [
            'accident_type' => __('accident_informs.accident_type'),
            'claim_type' => __('accident_informs.claim_type'),
            'claim_by' => __('accident_informs.claim_by'),
            'report_date' => __('accident_informs.report_date'),
            'reporter' => __('accident_informs.reporter'),
            'report_tel' => __('accident_informs.report_tel'),
            'license_plate' => __('accident_informs.license_plate_chassis_engine'),
            'accident_date' => __('accident_informs.accident_date'),
            'driver' => __('accident_informs.driver'),
            'main_area' => __('accident_informs.main_area'),
            'case' => __('accident_informs.case'),
            'accident_description' => __('accident_informs.accident_description'),
            'accident_place' => __('accident_informs.accident_place'),
            'current_place' => __('accident_informs.current_place'),
            'region' => __('accident_informs.sector'),
            'province' => __('accident_informs.province'),
            'district' => __('accident_informs.amphure'),
            'subdistrict' => __('accident_informs.district'),
            'wrong_type' => __('accident_informs.wrong_type'),
            'amount_wounded_driver' => __('accident_informs.amount_wounded_driver'),
            'amount_wounded_parties' => __('accident_informs.amount_wounded_parties'),
            'amount_deceased_driver' => __('accident_informs.amount_deceased_driver'),
            'amount_deceased_parties' => __('accident_informs.amount_deceased_parties'),
            'cradle' => __('accident_informs.cradle_recommend'),

            'first_lifter' => __('accident_informs.first_lifter'),
            'first_lift_date' => __('accident_informs.first_lift_date'),
            'first_lift_price' => __('accident_informs.first_lift_price'),
            'first_lift_tel' => __('accident_informs.lift_tel'),

            'lift_date' => __('accident_informs.lift_date'),
            'lift_from' => __('accident_informs.lift_from'),
            'lift_to' => __('accident_informs.lift_to'),
            'lift_price' => __('accident_informs.lift_price'),

            'replacement_expect_date' => __('accident_informs.replacement_date'),
            'replacement_type' => __('accident_informs.replacement_type'),
            'is_driver_replacement' => __('accident_informs.need_driver_replacement'),
            'replacement_expect_place' => __('accident_informs.replacement_place'),

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $accident_informs = Accident::firstOrNew(['id' => $request->id]);
        if (is_null($accident_informs->id)) {
            $accident_count = Accident::count() + 1;
            $prefix = 'AC';
            if (!($accident_informs->exists)) {
                $accident_informs->worksheet_no = generateRecordNumber($prefix, $accident_count);
            }

            $accident_informs->accident_type = $request->accident_type;
            $accident_informs->claim_type = $request->claim_type;
            $accident_informs->claim_by = $request->claim_by;
            $accident_informs->report_date = $request->report_date;
            $accident_informs->reporter = $request->reporter;
            $accident_informs->report_tel = $request->report_tel;
            $accident_informs->report_no = $request->report_no;

            $accident_informs->car_id = $request->license_plate;
            $accident_informs->job_type = $request->job_type;
            $accident_informs->job_id = $request->job_id;

            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->driver = $request->driver;
            $accident_informs->main_area = $request->main_area;
            $accident_informs->case = $request->case;
            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->accident_description = $request->accident_description;
            $accident_informs->accident_place = $request->accident_place;
            $accident_informs->current_place = $request->current_place;
            $accident_informs->region = $request->region;
            $accident_informs->province_id = $request->province;
            $accident_informs->district_id = $request->district;
            $accident_informs->subdistrict_id = $request->subdistrict;

            $accident_informs->is_parties = $request->is_parties;
            $accident_informs->is_wounded = $request->is_wounded;
            $accident_informs->is_deceased = $request->is_deceased;
            $accident_informs->is_repair = $request->is_repair;

            $accident_informs->wrong_type = $request->wrong_type;
            $accident_informs->amount_wounded_driver = $request->amount_wounded_driver;
            $accident_informs->amount_wounded_parties = $request->amount_wounded_parties;

            $accident_informs->amount_deceased_driver = $request->amount_deceased_driver;
            $accident_informs->amount_deceased_parties = $request->amount_deceased_parties;

            $accident_informs->cradle = $request->cradle;
            $accident_informs->remark = $request->remark;

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_informs->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $image) {
                    if ($image->isValid()) {
                        $accident_informs->addMedia($image)->toMediaCollection('optional_files');
                    }
                }
            }

            // replacement_car
            if ($request->is_replacement == STATUS_ACTIVE) {
                $accident_informs->is_replacement = $request->is_replacement;
                $accident_informs->is_driver_replacement = $request->is_driver_replacement;
                $accident_informs->replacement_expect_date = $request->replacement_expect_date;
                $accident_informs->replacement_type = $request->replacement_type;
                $accident_informs->replacement_expect_place = $request->replacement_expect_place;
            }

            // if ($request->replacement_car_files__pending_delete_ids) {
            //     $pending_delete_ids = $request->replacement_car_files__pending_delete_ids;
            //     if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
            //         foreach ($pending_delete_ids as $media_id) {
            //             $accident_informs->deleteMedia($media_id);
            //         }
            //     }
            // }

            // if ($request->hasFile('replacement_car_files')) {
            //     foreach ($request->file('replacement_car_files') as $image) {
            //         if ($image->isValid()) {
            //             $accident_informs->addMedia($image)->toMediaCollection('replacement_car_files');
            //         }
            //     }
            // }
            $accident_informs->status = AccidentStatusEnum::WAITING_CLAIM;
            $accident_informs->save();

            if ($request->is_replacement == STATUS_ACTIVE) {
                $car_replacement = $this->createCarReplacement($accident_informs, $request);
            }

            if ($accident_informs->is_parties && (strcmp($accident_informs->wrong_type, MistakeTypeEnum::TRUE) === 0)) {
                CompensationTrait::createCompensation($accident_informs->id);
            }

            // slide car first
            if (strcmp($request->first_lifting, STATUS_ACTIVE) === 0) {
                $accident_slide_first = new AccidentSlide();
                $accident_slide_first->accident_id = $accident_informs->id;
                // $accident_slide_first->job_type = Accident::class;
                // $accident_slide_first->job_id = $accident_informs->id;
                $accident_slide_first->slide_driver = $request->first_lifter;
                $accident_slide_first->slide_date = $request->first_lift_date;
                $slide_price_first = str_replace(',', '', $request->first_lift_price);
                $accident_slide_first->slide_price = $slide_price_first ? $slide_price_first : null;
                $accident_slide_first->slide_tel = $request->first_lift_tel;
                $accident_slide_first->slide_from = $request->current_place;
                $accident_slide_first->slide_to = "TRUE LEASING";
                $accident_slide_first->save();
            }

            if (strcmp($request->need_folklift, STATUS_ACTIVE) === 0) {
                $car_slide = $this->createCarSlide($accident_informs, $request);
                $accident_slide_second = new AccidentSlide();
                $accident_slide_second->accident_id = $accident_informs->id;
                $accident_slide_second->job_type = Slide::class;
                $accident_slide_second->job_id = $car_slide->id;
                $accident_slide_second->slide_date = $request->lift_date;
                $accident_slide_second->slide_from = $request->lift_from;
                $accident_slide_second->slide_to = $request->lift_to;
                $slide_price = str_replace(',', '', $request->lift_price);
                $accident_slide_second->slide_price = $slide_price ? $slide_price : null;
                $accident_slide_second->slide_driver = "TRUE LEASING";
                $accident_slide_second->save();
            }


            if ($request->tags != null) {
                $url = null;
                $dealer_name = null;
                $po_no = null;
                $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
                if (App::environment('production')) {
                    $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
                }
                $tag = explode(',', $request->tags);
                $mails = $tag;

                $car_license_plate = $accident_informs->car->license_plate;
                if (!is_null($car_license_plate)) {
                    $car_license_plate = __('inspection_cars.license_plate') . ' ' . $accident_informs->car->license_plate;
                }
                if (is_null($car_license_plate)) {
                    $car_license_plate = __('inspection_cars.chassis_no') . ' ' . $accident_informs->car->chassis_no;
                }
                if (is_null($car_license_plate)) {
                    $car_license_plate = __('inspection_cars.engine_no') . ' ' . $accident_informs->car->engine_no;
                }

                $car_brand_name = $accident_informs->car && $accident_informs->car->carBrand ? $accident_informs->car->carBrand->name : null;
                $accident_date = get_thai_date_format($accident_informs->accident_date, 'd/m/Y');
                $accident_time = get_thai_date_format($accident_informs->accident_date, 'H:i');
                $case = __('accident_informs.case_' . $accident_informs->case);
                $accident_description = $accident_informs->accident_description;
                $accident_place = $accident_informs->accident_place;

                $province = $accident_informs && $accident_informs->province ? $accident_informs->province->name_th : null;
                $district = $accident_informs && $accident_informs->district ? $accident_informs->district->name_th : null;
                $subdistrict = $accident_informs && $accident_informs->subDistrict ? $accident_informs->subDistrict->name_th : null;
                $current_place = $accident_informs->current_place;
                $is_parties = $accident_informs->is_parties;
                $amount_wounded_total = $accident_informs->amount_wounded_driver + $accident_informs->amount_wounded_parties;
                $amount_wounded_driver = $accident_informs->amount_wounded_driver;
                $amount_wounded_parties = $accident_informs->amount_wounded_parties;

                $amount_deceased_total = $accident_informs->amount_deceased_driver + $accident_informs->amount_deceased_parties;
                $amount_deceased_driver = $accident_informs->amount_deceased_driver;
                $amount_deceased_parties = $accident_informs->amount_deceased_parties;

                $slide_driver = null;
                $slide_tel = null;
                $first_lifting = false;
                if ($request->first_lifting) {
                    $first_lifting = true;
                    $slide_driver = $accident_slide_first->slide_driver;
                    $slide_tel = $accident_slide_first->slide_tel;
                }

                $slide_date = null;
                $slide_from = null;
                $slide_to = null;
                $need_folklift = false;
                if ($request->need_folklift) {
                    $need_folklift = true;
                    $slide_from = $accident_slide_second->slide_from;
                    $slide_to = $accident_slide_second->slide_to;
                    $slide_date = get_thai_date_format($accident_slide_second->slide_date, 'd/m/Y');
                }

                $mail_data = [
                    'url' => $url,
                    'dealer_name' => $dealer_name,
                    'image' => $image,
                    'car_license_plate' => $car_license_plate,
                    'car_brand_name' => $car_brand_name,
                    'accident_date' => $accident_date,
                    'accident_time' =>  $accident_time,
                    'case' => $case,
                    'accident_description' => $accident_description,
                    'accident_place' => $accident_place,
                    'province' => $province,
                    'district' => $district,
                    'subdistrict' => $subdistrict,
                    'current_place' => $current_place,
                    'is_parties' => $is_parties,
                    'amount_wounded_total' => $amount_wounded_total,
                    'amount_wounded_driver' => $amount_wounded_driver,
                    'amount_wounded_parties' => $amount_wounded_parties,

                    'amount_deceased_total' => $amount_deceased_total,
                    'amount_deceased_driver' => $amount_deceased_driver,
                    'amount_deceased_parties' => $amount_deceased_parties,

                    'slide_driver' => $slide_driver,
                    'slide_tel' => $slide_tel,
                    'first_lifting' => $first_lifting,
                    'slide_date' => $slide_date,
                    'slide_from' => $slide_from,
                    'slide_to' => $slide_to,
                    'need_folklift' => $need_folklift,
                ];

                AccidentJob::dispatch($mails, $mail_data);
            }
        }
        $redirect_route = route('admin.accident-informs.index');
        return $this->responseValidateSuccess($redirect_route);
    }


    public function show(Accident $accident_inform)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentInform);
        $car = Car::find($accident_inform->car_id);
        $car_license = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($accident_inform->car_id);
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident_inform);
        $need_list = AccidentTrait::getNeedList();
        $replace_list = AccidentTrait::getReplacementList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();

        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();

        $province = Province::find($accident_inform->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident_inform->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident_inform->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;

        $cradle = Cradle::find($accident_inform->cradle);

        $replacement_car_files = $accident_inform->getMedia('replacement_car_files');
        $replacement_car_files = get_medias_detail($replacement_car_files);

        $slide_list = $this->getSlideList($accident_inform);
        $cost_list = $this->getCostList($accident_inform);

        $replacement_list = $this->getReplacementList($accident_inform);
        $cost_list = $this->getCostList($accident_inform);
        $accident_slide_list = AccidentTrait::getAccidentSlideList();

        $receive_status_list = $this->getStatusReceiveList();

        $slide_worksheet_list = Slide::where('job_type', Accident::class)->where('job_id', $accident_inform->id)->select('id', 'worksheet_no as name')->get();

        $page_title = $page_title = __('lang.view') . __('accident_informs.page_title');
        return view('admin.accident-informs.form-accident-edit',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident_inform,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'cradle' => $cradle,
            'slide_list' => $slide_list,
            'cost_list' => $cost_list,
            'view' => true,
            'accident_slide_list' => $accident_slide_list,
            'replacement_list' => $replacement_list,
            'receive_status_list' => $receive_status_list,
            'slide_worksheet_list' => $slide_worksheet_list,
        ]);
    }

    public function edit(Accident $accident_inform)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentInform);
        $car = Car::find($accident_inform->car_id);
        $car_license = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($accident_inform->car_id);
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident_inform);
        $need_list = AccidentTrait::getNeedList();
        $replace_list = AccidentTrait::getReplacementList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();

        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();

        $province = Province::find($accident_inform->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident_inform->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident_inform->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;

        $cradle = Cradle::find($accident_inform->cradle);

        $replacement_car_files = $accident_inform->getMedia('replacement_car_files');
        $replacement_car_files = get_medias_detail($replacement_car_files);

        $slide_list = $this->getSlideList($accident_inform);
        $replacement_list = $this->getReplacementList($accident_inform);
        $cost_list = $this->getCostList($accident_inform);
        $accident_slide_list = AccidentTrait::getAccidentSlideList();
        $receive_status_list = $this->getStatusReceiveList();
        $slide_worksheet_list = Slide::where('job_type', Accident::class)->where('job_id', $accident_inform->id)->select('id', 'worksheet_no as name')->get();

        $page_title = $page_title = __('lang.edit') . __('accident_informs.page_title');
        return view('admin.accident-informs.form-accident-edit',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident_inform,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'cradle' => $cradle,
            'slide_list' => $slide_list,
            'cost_list' => $cost_list,
            'accident_slide_list' => $accident_slide_list,
            'replacement_list' => $replacement_list,
            'receive_status_list' => $receive_status_list,
            'slide_worksheet_list' => $slide_worksheet_list,
        ]);
    }

    function getDefaultCarByLicensePlate(Request $request)
    {
        $car_id = $request->license_plate;
        $job_id = $request->job_id;
        $job_type = $request->job_type;
        $data_import = [];

        $data = DB::table('cars')
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'cars.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'cars.oil_type',
                'cars.rental_type',
            )
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'cars.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'cars.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            // ->leftJoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->where('cars.id', $car_id)
            ->get()
            ->toArray();

        $job_type = null;
        $job_id = null;
        // rental
        $worksheet = RentalLine::where('car_id', $car_id)
            ->whereDate('pickup_date', '<=', Carbon::now())
            ->whereDate('return_date', '>=', Carbon::now())
            ->first();

        // lt rental
        if (is_null($worksheet)) {
            $worksheet = null;
            $lt_rental_car = LongTermRentalPRCar::where('car_id', $car_id)
                ->first();
            if ($lt_rental_car) {
                $lt_pr_line = LongTermRentalPRLine::where('id', $lt_rental_car->lt_rental_pr_line_id)->first();
                if ($lt_pr_line) {
                    $worksheet = LongTermRental::find($lt_pr_line->lt_rental_id);
                    $job_type = LongTermRental::class;
                    $job_id = $worksheet->id;
                }
            }
        } else {
            $worksheet = Rental::find($worksheet->rental_id);
            if ($worksheet) {
                $job_type = Rental::class;
                $job_id = $worksheet->id;
            }
        }

        return [
            'success' => true,
            'car_id' => $request->car_id,
            'data' => $data,
            'worksheet' => $worksheet,
            'job_type' => $job_type,
            'job_id' => $job_id,
        ];
    }

    public function storeEditAccident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accident_type' => [
                'required',
            ],
            // 'claim_type' => [
            //     'required',
            // ],
            // 'claim_by' => [
            //     'required',
            // ],
            'report_date' => [
                'required',
            ],
            'reporter' => [
                'required',
            ],
            'report_tel' => [
                'required', 'numeric', 'digits:10',
            ],
            // 'report_no' => [
            //     'required',
            // ],
            'accident_date' => [
                'required',
            ],
            'driver' => [
                'required',
            ],
            'main_area' => [
                'required',
            ],
            'case' => [
                'required',
            ],
            'accident_description' => [
                'required',
            ],
            'accident_place' => [
                'required',
            ],
            // 'current_place' => [
            //     'required',
            // ],
            'region' => [
                'required',
            ],
            'province' => [
                'required',
            ],
            // 'district' => [
            //     'required',
            // ],
            // 'subdistrict' => [
            //     'required',
            // ],

            // 'wrong_type' => [Rule::when($request->is_parties == STATUS_ACTIVE, ['required'])],
            'amount_wounded_driver' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],
            'amount_wounded_parties' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],

            'amount_deceased_driver' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            'amount_deceased_parties' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            // 'place_employee' => [Rule::when($request->is_driver_employee == STATUS_ACTIVE, ['required'])],
            'cradle' => [Rule::when($request->is_repair == STATUS_ACTIVE, ['required'])],

            'first_lifter' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_date' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_price' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_tel' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],

            'lift_date' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_from' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_to' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_price' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],

            'replacement_expect_date' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_type' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'is_driver_replacement' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_expect_place' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
        ], [], [
            'accident_type' => __('accident_informs.accident_type'),
            'claim_type' => __('accident_informs.claim_type'),
            'claim_by' => __('accident_informs.claim_by'),
            'report_date' => __('accident_informs.report_date'),
            'reporter' => __('accident_informs.reporter'),
            'report_tel' => __('accident_informs.report_tel'),
            'license_plate' => __('accident_informs.license_plate_chassis_engine'),
            'accident_date' => __('accident_informs.accident_date'),
            'driver' => __('accident_informs.driver'),
            'main_area' => __('accident_informs.main_area'),
            'case' => __('accident_informs.case'),
            'accident_description' => __('accident_informs.accident_description'),
            'accident_place' => __('accident_informs.accident_place'),
            'current_place' => __('accident_informs.current_place'),
            'region' => __('accident_informs.sector'),
            'province' => __('accident_informs.province'),
            'district' => __('accident_informs.amphure'),
            'subdistrict' => __('accident_informs.district'),
            'wrong_type' => __('accident_informs.wrong_type'),
            'amount_wounded_driver' => __('accident_informs.amount_wounded_driver'),
            'amount_wounded_parties' => __('accident_informs.amount_wounded_parties'),
            'amount_deceased_driver' => __('accident_informs.amount_deceased_driver'),
            'amount_deceased_parties' => __('accident_informs.amount_deceased_parties'),
            'cradle' => __('accident_informs.cradle_recommend'),

            'first_lifter' => __('accident_informs.first_lifter'),
            'first_lift_date' => __('accident_informs.first_lift_date'),
            'first_lift_price' => __('accident_informs.first_lift_price'),
            'first_lift_tel' => __('accident_informs.lift_tel'),

            'lift_date' => __('accident_informs.lift_date'),
            'lift_from' => __('accident_informs.lift_from'),
            'lift_to' => __('accident_informs.lift_to'),
            'lift_price' => __('accident_informs.lift_price'),

            'replacement_expect_date' => __('accident_informs.replacement_date'),
            'replacement_type' => __('accident_informs.replacement_type'),
            'is_driver_replacement' => __('accident_informs.need_driver_replacement'),
            'replacement_expect_place' => __('accident_informs.replacement_place'),

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $accident_informs = Accident::find($request->accident_id);
        if ($accident_informs) {
            $accident_informs->accident_type = $request->accident_type;
            $accident_informs->claim_type = $request->claim_type;
            $accident_informs->claim_by = $request->claim_by;
            $accident_informs->report_date = $request->report_date;
            $accident_informs->reporter = $request->reporter;
            $accident_informs->report_tel = $request->report_tel;
            $accident_informs->report_no = $request->report_no;
            $accident_informs->job_type = $request->job_type;
            $accident_informs->job_id = $request->job_id;
            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->driver = $request->driver;
            $accident_informs->main_area = $request->main_area;
            $accident_informs->case = $request->case;
            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->accident_description = $request->accident_description;
            $accident_informs->accident_place = $request->accident_place;
            $accident_informs->current_place = $request->current_place;
            $accident_informs->region = $request->region;
            $accident_informs->province_id = $request->province;
            $accident_informs->district_id = $request->district;
            $accident_informs->subdistrict_id = $request->subdistrict;
            $accident_informs->is_parties = $request->is_parties;
            $accident_informs->is_wounded = $request->is_wounded;
            $accident_informs->is_deceased = $request->is_deceased;
            $accident_informs->is_repair = $request->is_repair;
            $accident_informs->wrong_type = $request->wrong_type;
            $accident_informs->amount_wounded_driver = $request->amount_wounded_driver;
            $accident_informs->amount_wounded_parties = $request->amount_wounded_parties;
            $accident_informs->amount_deceased_driver = $request->amount_deceased_driver;
            $accident_informs->amount_deceased_parties = $request->amount_deceased_parties;
            $accident_informs->cradle = $request->cradle;
            $accident_informs->remark = $request->remark;

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_informs->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $image) {
                    if ($image->isValid()) {
                        $accident_informs->addMedia($image)->toMediaCollection('optional_files');
                    }
                }
            }

            // replacement_car
            $accident_informs->is_replacement = $request->is_replacement;
            $accident_informs->is_driver_replacement = $request->is_driver_replacement;
            $accident_informs->replacement_expect_date = $request->replacement_expect_date;
            $accident_informs->replacement_type = $request->replacement_type;
            $accident_informs->replacement_expect_place = $request->replacement_expect_place;

            if ($request->replacement_car_files__pending_delete_ids) {
                $pending_delete_ids = $request->replacement_car_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_informs->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('replacement_car_files')) {
                foreach ($request->file('replacement_car_files') as $image) {
                    if ($image->isValid()) {
                        $accident_informs->addMedia($image)->toMediaCollection('replacement_car_files');
                    }
                }
            }
            $accident_informs->status = AccidentStatusEnum::WAITING_CLAIM;
            $accident_informs->save();

            // delete replacement
            $delete_replacement_ids = $request->delete_replacement_ids;
            if ((!empty($delete_replacement_ids)) && (is_array($delete_replacement_ids))) {
                foreach ($delete_replacement_ids as $delete_id) {
                    $replacement_delete = ReplacementCar::find($delete_id);
                    $replacement_medias = $replacement_delete->getMedia('replacement_car_files');
                    foreach ($replacement_medias as $replacement_media) {
                        $replacement_media->delete();
                    }
                    $replacement_delete->delete();
                }
            }

            $slide = $this->saveSlide($request, $accident_informs);
            $cost = $this->saveCost($request, $accident_informs);
        }

        if ($request->tags != null) {
            $url = null;
            $dealer_name = null;
            $po_no = null;
            $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
            if (App::environment('production')) {
                $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
            }
            $tag = explode(',', $request->tags);
            $mails = $tag;

            $car_license_plate = $accident_informs->car->license_plate;
            if (!is_null($car_license_plate)) {
                $car_license_plate = __('inspection_cars.license_plate') . ' ' . $accident_informs->car->license_plate;
            }
            if (is_null($car_license_plate)) {
                $car_license_plate = __('inspection_cars.chassis_no') . ' ' . $accident_informs->car->chassis_no;
            }
            if (is_null($car_license_plate)) {
                $car_license_plate = __('inspection_cars.engine_no') . ' ' . $accident_informs->car->engine_no;
            }

            $car_brand_name = $accident_informs->car && $accident_informs->car->carBrand ? $accident_informs->car->carBrand->name : null;
            $accident_date = get_thai_date_format($accident_informs->accident_date, 'd/m/Y');
            $accident_time = get_thai_date_format($accident_informs->accident_date, 'H:i');
            $case = __('accident_informs.case_' . $accident_informs->case);
            $accident_description = $accident_informs->accident_description;
            $accident_place = $accident_informs->accident_place;

            $province = $accident_informs && $accident_informs->province ? $accident_informs->province->name_th : null;
            $district = $accident_informs && $accident_informs->district ? $accident_informs->district->name_th : null;
            $subdistrict = $accident_informs && $accident_informs->subDistrict ? $accident_informs->subDistrict->name_th : null;
            $current_place = $accident_informs->current_place;
            $is_parties = $accident_informs->is_parties;
            $amount_wounded_total = $accident_informs->amount_wounded_driver + $accident_informs->amount_wounded_parties;
            $amount_wounded_driver = $accident_informs->amount_wounded_driver;
            $amount_wounded_parties = $accident_informs->amount_wounded_parties;

            $amount_deceased_total = $accident_informs->amount_deceased_driver + $accident_informs->amount_deceased_parties;
            $amount_deceased_driver = $accident_informs->amount_deceased_driver;
            $amount_deceased_parties = $accident_informs->amount_deceased_parties;

            $slide_driver = null;
            $slide_tel = null;
            $first_lifting = false;
            // if ($request->first_lifting) {
            //     $first_lifting = true;
            //     $slide_driver = $accident_slide_first->slide_driver;
            //     $slide_tel = $accident_slide_first->slide_tel;
            // }

            $slide_date = null;
            $slide_from = null;
            $slide_to = null;
            $need_folklift = false;
            // if ($request->need_folklift) {
            //     $need_folklift = true;
            //     $slide_from = $accident_slide_second->slide_from;
            //     $slide_to = $accident_slide_second->slide_to;
            //     $slide_date = get_thai_date_format($accident_slide_second->slide_date, 'd/m/Y');
            // }

            $mail_data = [
                'url' => $url,
                'dealer_name' => $dealer_name,
                'image' => $image,
                'car_license_plate' => $car_license_plate,
                'car_brand_name' => $car_brand_name,
                'accident_date' => $accident_date,
                'accident_time' =>  $accident_time,
                'case' => $case,
                'accident_description' => $accident_description,
                'accident_place' => $accident_place,
                'province' => $province,
                'district' => $district,
                'subdistrict' => $subdistrict,
                'current_place' => $current_place,
                'is_parties' => $is_parties,
                'amount_wounded_total' => $amount_wounded_total,
                'amount_wounded_driver' => $amount_wounded_driver,
                'amount_wounded_parties' => $amount_wounded_parties,

                'amount_deceased_total' => $amount_deceased_total,
                'amount_deceased_driver' => $amount_deceased_driver,
                'amount_deceased_parties' => $amount_deceased_parties,

                'slide_driver' => $slide_driver,
                'slide_tel' => $slide_tel,
                'first_lifting' => $first_lifting,
                'slide_date' => $slide_date,
                'slide_from' => $slide_from,
                'slide_to' => $slide_to,
                'need_folklift' => $need_folklift,
            ];

            AccidentJob::dispatch($mails, $mail_data);
        }
        $redirect_route = route('admin.accident-informs.index');
        return $this->responseValidateSuccess($redirect_route);
    }


    private function saveSlide($request, $slide_model)
    {
        $delete_slide_ids = $request->delete_slide_ids;
        if ((!empty($delete_slide_ids)) && (is_array($delete_slide_ids))) {
            foreach ($delete_slide_ids as $delete_id) {
                $slide_delete = AccidentSlide::find($delete_id);
                $slide_medias = $slide_delete->getMedia('slide');
                foreach ($slide_medias as $slide_media) {
                    $slide_media->delete();
                }
                $slide_delete->delete();
            }
        }

        // $pending_delete_slide_files = $request->slide_file__pending_delete_ids;
        // if (!empty($request->slide)) {
        //     foreach ($request->slide as $key => $request_slide) {
        //         $acciden_slide = AccidentSlide::firstOrNew(['id' => $request_slide['id']]);
        //         if (!$acciden_slide->exists) {
        //             //
        //         }
        //         $acciden_slide->accident_id = $slide_model->id;
        //         $acciden_slide->slide_date = $request_slide['lift_date'];
        //         $acciden_slide->slide_driver = $request_slide['slide_driver'];
        //         $acciden_slide->slide_from = $request_slide['lift_from'];
        //         $acciden_slide->slide_to = $request_slide['lift_to'];

        //         $slide_price = str_replace(',', '', $request_slide['lift_price']);
        //         $acciden_slide->slide_price = $slide_price ? $slide_price : null;

        //         $acciden_slide->save();

        //         // delete file driver skill
        //         if ((!empty($pending_delete_slide_files)) && (sizeof($pending_delete_slide_files) > 0)) {
        //             foreach ($pending_delete_slide_files as $slide_media_id) {
        //                 $slide_media = Media::find($slide_media_id);
        //                 if ($slide_media && $slide_media->model_id) {
        //                     $skill_model = AccidentSlide::find($slide_media->model_id);
        //                     $skill_model->deleteMedia($slide_media->id);
        //                 }
        //             }
        //         }

        //         // insert + update driver skill
        //         if ((!empty($request->slide_file)) && (sizeof($request->slide_file) > 0)) {
        //             foreach ($request->slide_file as $table_row_index => $slide_files) {
        //                 foreach ($slide_files as $slide_file) {
        //                     if ($slide_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
        //                         $acciden_slide->addMedia($slide_file)->toMediaCollection('slide');
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        return true;
    }

    private function saveCost($request, $cost_model)
    {
        $delete_cost_ids = $request->delete_cost_ids;
        if ((!empty($delete_cost_ids)) && (is_array($delete_cost_ids))) {
            foreach ($delete_cost_ids as $delete_id) {
                $cost_delete = AccidentExpense::find($delete_id);
                $cost_delete->delete();
            }
        }
        if (!empty($request->cost)) {
            foreach ($request->cost as $key => $request_cost) {
                $acciden_cost = AccidentExpense::firstOrNew(['id' => $request_cost['id']]);
                if (!$acciden_cost->exists) {
                    //
                }
                $acciden_cost->accident_id = $cost_model->id;
                $acciden_cost->list = $request_cost['cost_name'];
                $cost_price = str_replace(',', '', $request_cost['cost_price']);
                $acciden_cost->price = $cost_price ? $cost_price : null;
                $acciden_cost->remark = $request_cost['cost_remark'];
                $acciden_cost->save();
            }
        }
        return true;
    }


    public function getSlideList($accident_model)
    {
        $slide_list = AccidentSlide::where('accident_id', $accident_model->id)->get();
        $slide_list->map(function ($item) {
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->slide_driver = ($item->slide_driver) ? $item->slide_driver : '';
            $item->lift_price = ($item->slide_price) ? $item->slide_price : '';
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->lift_from = ($item->slide_from) ? $item->slide_from : '';
            $item->lift_to = ($item->slide_to) ? $item->slide_to : '';

            $slide_medias = $item->getMedia('slide');
            $slide_medias = get_medias_detail($slide_medias);
            $slide_medias = collect($slide_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->slide_files = $slide_medias;
            $item->pending_delete_slide_files = [];

            if ($item->job_type == Slide::class) {
                $slide = Slide::find($item->job_id);
                $item->slide_id = $slide->id;
                $item->slide_worksheet = $slide->worksheet_no;
                $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE);
                $item->origin_place = $slide->origin_place;
                $item->origin_contact = $slide->origin_contact;
                $item->origin_tel = $slide->origin_tel;
                $item->destination_place = $slide->destination_place;
                $item->destination_contact = $slide->destination_contact;
                $item->destination_tel = $slide->destination_tel;
                $item->slide_type_id = AccidentSlideEnum::TLS_SLIDE;
            } else {
                $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE);
                $item->slide_type_id = AccidentSlideEnum::THIRD_PARTY_SLIDE;
            }

            return $item;
        });

        return $slide_list;
    }

    public function getReplacementList($accident_model)
    {
        $replacement_list = ReplacementCar::where('job_type', Accident::class)->where('job_id', $accident_model->id)->get();
        $replacement_list->map(function ($item) use ($accident_model) {
            $item->replacement_type = ($item->replacement_type) ? $item->replacement_type : '';
            $item->replacement_pickup_date = ($item->replacement_date) ? $item->replacement_date : '';
            $item->slide_worksheet = ($item->slide_id) ? $item->slide_id : '';
            $item->place = ($item->replacement_place) ? $item->replacement_place : '';
            $item->customer_receive = ($item->is_cust_receive_replace) ? $item->is_cust_receive_replace : STATUS_DEFAULT;
            $item->accident_id = $accident_model->id;
            $item->car_id = $accident_model->car_id;
            $item->id = $item->id;
            $item->worksheet = $item->worksheet_no;
            $main_car = Car::find($item->main_car_id);
            $item->main_car = $main_car->license_plate;
            $item->replacement_url = route('admin.replacement-cars.show', ['replacement_car' => $item->id]);
            $item->replacement_type_text = __('accident_informs.replace_type_' . $item->replacement_type);
            $slide = Slide::find($item->slide_id);
            if ($slide) {
                $slide_worksheet_no = $slide->worksheet_no;
            } else {
                $slide_worksheet_no = null;
            }
            $item->customer_receive_text = ($item->is_cust_receive_replace) && $item->is_cust_receive_replace == STATUS_ACTIVE ?  __('accident_informs.customer_receive_self') : 'รถสไลด์ : ' . $slide_worksheet_no;

            $replacement_medias = $item->getMedia('replacement_car_files');
            $replacement_medias = get_medias_detail($replacement_medias);
            $replacement_medias = collect($replacement_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->replacement_files = $replacement_medias;
            $item->pending_delete_replacement_files = [];

            // if($item->job_type == Slide::class){
            //     $slide = Slide::find($item->job_id);
            //     $item->slide_id = $slide->id;
            //     $item->slide_worksheet = $slide->worksheet_no;
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE);
            //     $item->origin_place = $slide->origin_place;
            //     $item->origin_contact = $slide->origin_contact;
            //     $item->origin_tel = $slide->origin_tel;
            //     $item->destination_place = $slide->destination_place;
            //     $item->destination_contact = $slide->destination_contact;
            //     $item->destination_tel = $slide->destination_tel;
            //     $item->slide_type_id = AccidentSlideEnum::TLS_SLIDE;

            // }else{
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE);
            //     $item->slide_type_id = AccidentSlideEnum::THIRD_PARTY_SLIDE;
            // }

            return $item;
        });

        return $replacement_list;
    }

    public function getCostList($cost_model)
    {
        $cost_list = AccidentExpense::where('accident_id', $cost_model->id)->get();
        $cost_list->map(function ($item) {
            $item->cost_name = ($item->list) ? $item->list : '';
            $item->cost_price = ($item->price) ? $item->price : '';
            $item->cost_remark = ($item->remark) ? $item->remark : '';
            $item->cost_date = ($item->created_at) ? $item->created_at : '';
            return $item;
        });

        return $cost_list;
    }

    public function editClaim(Accident $accident_inform)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentInform);
        $repair_list = [];
        $spare_part_list = $this->getStatusList();
        $repair_list = $this->getRepairList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $getClaimList = $this->getClaimList($accident_inform);
        $claim_list_data = $getClaimList['claim_list'];
        $is_withdraw_true = $getClaimList['is_withdraw_true'];
        $tls_cost_total = $getClaimList['tls_cost_total'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = $this->getWoundList();
        $responsible_list = $this->getResponsibleList();
        $rights_list = $this->getRightsList();
        $page_title = $page_title = __('lang.edit') . __('accident_informs.page_title');
        return view('admin.accident-informs.form-claim-edit',  [
            'page_title' => $page_title,
            'd' => $accident_inform,
            'repair_list' => $repair_list,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'claim_list_data' => $claim_list_data,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total,
            'repair_list' => $repair_list,
            'claim_type_list' => $claim_type_list,
            'responsible_list' => $responsible_list,
            'rights_list' => $rights_list,
        ]);
    }

    public function showClaim(Accident $accident_inform)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentInform);
        $repair_list = [];
        $spare_part_list = $this->getStatusList();
        $repair_list = $this->getRepairList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $getClaimList = $this->getClaimList($accident_inform);
        $claim_list_data = $getClaimList['claim_list'];
        $is_withdraw_true = $getClaimList['is_withdraw_true'];
        $tls_cost_total = $getClaimList['tls_cost_total'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = $this->getWoundList();
        $responsible_list = $this->getResponsibleList();
        $rights_list = $this->getRightsList();
        $page_title = $page_title = __('lang.edit') . __('accident_informs.page_title');
        return view('admin.accident-informs.form-claim-edit',  [
            'page_title' => $page_title,
            'd' => $accident_inform,
            'repair_list' => $repair_list,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'claim_list_data' => $claim_list_data,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total,
            'repair_list' => $repair_list,
            'claim_type_list' => $claim_type_list,
            'responsible_list' => $responsible_list,
            'rights_list' => $rights_list,
            'view' => true,
        ]);
    }

    public static function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.spare_part_status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.spare_part_status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public static function getStatusReceiveList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.receive_status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.receive_status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public static function getWoundList()
    {
        return collect([
            (object)[
                'id' => WoundType::A,
                'value' => WoundType::A,
                'name' => WoundType::A,
            ],
            (object)[
                'id' => WoundType::B,
                'value' => WoundType::B,
                'name' => WoundType::B,
            ],
            (object)[
                'id' => WoundType::C,
                'value' => WoundType::C,
                'name' => WoundType::C,
            ],
            (object)[
                'id' => WoundType::D,
                'value' => WoundType::D,
                'name' => WoundType::D,
            ],
            (object)[
                'id' => WoundType::OLD_SPARE_PART,
                'value' => WoundType::OLD_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::OLD_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::REPAIR_SPARE_PART,
                'value' => WoundType::REPAIR_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::REPAIR_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::NO_CHANGE_SPARE_PART,
                'value' => WoundType::NO_CHANGE_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::NO_CHANGE_SPARE_PART),
            ],

        ]);
    }

    public static function getRepairList()
    {
        return collect([
            (object)[
                'id' => RepairClaimEnum::HARD_BUMP,
                'value' => RepairClaimEnum::HARD_BUMP,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::HARD_BUMP),
            ],
            (object)[
                'id' => RepairClaimEnum::SOFT_BUMP,
                'value' => RepairClaimEnum::SOFT_BUMP,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::SOFT_BUMP),
            ],
            (object)[
                'id' => RepairClaimEnum::TTL,
                'value' => RepairClaimEnum::TTL,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::TTL),
            ],

        ]);
    }

    public static function getResponsibleList()
    {
        return collect([
            (object)[
                'id' => ResponsibleEnum::INSURANCE_ACCEPT,
                'value' => ResponsibleEnum::INSURANCE_ACCEPT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::INSURANCE_ACCEPT),
            ],
            (object)[
                'id' => ResponsibleEnum::INSURANCE_REJECT,
                'value' => ResponsibleEnum::INSURANCE_REJECT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::INSURANCE_REJECT),
            ],
            (object)[
                'id' => ResponsibleEnum::TLS_ACCEPT,
                'value' => ResponsibleEnum::TLS_ACCEPT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::TLS_ACCEPT),
            ],

        ]);
    }

    public static function getRightsList()
    {
        return collect([
            (object)[
                'id' => RightsEnum::USE_RIGHTS,
                'value' => RightsEnum::USE_RIGHTS,
                'name' => __('accident_informs.rights_' . RightsEnum::USE_RIGHTS),
            ],
            (object)[
                'id' => RightsEnum::NOT_USE_RIGHTS,
                'value' => RightsEnum::NOT_USE_RIGHTS,
                'name' => __('accident_informs.rights_' . RightsEnum::NOT_USE_RIGHTS),
            ],

        ]);
    }


    public function storeEditClaim(Request $request)
    {
        $accident_informs = Accident::find($request->id);
        $validator = Validator::make($request->all(), [
            'amount_claim_customer' => [
                'required',
            ],
            'amount_claim_tls' => [
                'required',
            ],
            'compensation' => [
                'required',
            ],
            'repair_type' => [
                'required',
            ],
            'report_no' => [
                'required',
            ],
            'claim_no' => [
                'required',
            ],
            'claim_type_id' => [
                'required',
            ],
            'responsible' => [
                'required',
            ],
            'is_except_deductible' => [
                'required',
            ],
            'reason_except_deductible' => [
                'required',
            ],
            'deductible' => [Rule::when($accident_informs->wrong_type == MistakeTypeEnum::FALSE, ['required'])],
        ], [], [
            'amount_claim_customer' => __('accident_informs.customer_claim_amount'),
            'amount_claim_tls' => __('accident_informs.tls_claim_amount'),
            'compensation' => __('accident_informs.compensation_payment'),
            'repair_type' => __('accident_informs.repair'),
            'report_no' => __('accident_informs.inform_no'),
            'claim_no' => __('accident_informs.claim_no'),
            'claim_type_id' => __('accident_informs.claim_type'),
            'responsible' => __('accident_informs.responsible_person'),
            'is_except_deductible' => __('accident_informs.except_damages'),
            'reason_except_deductible' => __('accident_informs.right_reason'),
            'deductible' => __('accident_informs.first_damage_cost'),

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($accident_informs) {
            $accident_informs->amount_claim_customer = $request->amount_claim_customer;
            $accident_informs->amount_claim_tls = $request->amount_claim_tls;
            $compensation = str_replace(',', '', $request->compensation);
            $accident_informs->compensation = $compensation ? $compensation : null;
            $accident_informs->repair_type = $request->repair_type;
            $accident_informs->report_no = $request->report_no;
            $accident_informs->claim_no = $request->claim_no;
            $accident_informs->claim_type = $request->claim_type_id;
            $accident_informs->responsible = $request->responsible;
            $accident_informs->is_except_deductible = $request->is_except_deductible;
            $accident_informs->reason_except_deductible = $request->reason_except_deductible;
            $deductible = str_replace(',', '', $request->deductible);
            $accident_informs->deductible = ($accident_informs->wrong_type == MistakeTypeEnum::FALSE) ? $deductible : null;
            $accident_informs->save();

            $claim = $this->saveClaim($request, $accident_informs);
        }

        $redirect_route = route('admin.accident-informs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveClaim($request, $claim_model)
    {
        $delete_claim_ids = $request->delete_claim_ids;
        if ((!empty($delete_claim_ids)) && (is_array($delete_claim_ids))) {
            foreach ($delete_claim_ids as $delete_id) {
                $claim_delete = AccidentClaimLines::find($delete_id);
                $claim_delete->delete();
            }
        }

        $pending_delete_before_files = $request->before_files__pending_delete_ids;
        $pending_delete_after_files = $request->after_files__pending_delete_ids;
        if (!empty($request->repair)) {
            foreach ($request->repair as $key => $request_repair) {
                $acciden_claim_line = AccidentClaimLines::firstOrNew(['id' => $request_repair['id']]);
                if (!$acciden_claim_line->exists) {
                    //
                }
                $acciden_claim_line->accident_id = $claim_model->id;
                $acciden_claim_line->accident_claim_list_id = $request_repair['accident_claim_id'];
                $acciden_claim_line->supplier = $request_repair['supplier'];
                $acciden_claim_line->is_withdraw_true = filter_var($request_repair['is_withdraw_true'], FILTER_VALIDATE_BOOLEAN);
                $acciden_claim_line->wound_characteristics = $request_repair['wound_characteristics_id'];
                $tls_cost = str_replace(',', '', $request_repair['tls_cost']);
                $acciden_claim_line->cost = $tls_cost ? $tls_cost : null;
                $acciden_claim_line->save();

                if ((!empty($pending_delete_before_files)) && (sizeof($pending_delete_before_files) > 0)) {
                    foreach ($pending_delete_before_files as $before_media_id) {
                        $before_media = Media::find($before_media_id);
                        if ($before_media && $before_media->model_id) {
                            $before_media->delete();
                        }
                    }
                }

                // insert + update before
                if ((!empty($request->before_files)) && (sizeof($request->before_files) > 0)) {
                    foreach ($request->before_files as $table_row_index => $before_files) {
                        foreach ($before_files as $before_file) {
                            if ($before_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $acciden_claim_line->addMedia($before_file)->toMediaCollection('before_file');
                            }
                        }
                    }
                }

                // delete file after
                if ((!empty($pending_delete_after_files)) && (sizeof($pending_delete_after_files) > 0)) {
                    foreach ($pending_delete_after_files as $after_media_id) {
                        $after_media = Media::find($after_media_id);
                        if ($after_media && $after_media->model_id) {
                            $after_media->delete();
                        }
                    }
                }

                // insert + update after
                if ((!empty($request->after_files)) && (sizeof($request->after_files) > 0)) {
                    foreach ($request->after_files as $table_row_index => $after_files) {
                        foreach ($after_files as $after_file) {
                            if ($after_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $acciden_claim_line->addMedia($after_file)->toMediaCollection('after_file');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getClaimList($claim_model)
    {
        $claim_list = AccidentClaimLines::where('accident_id', $claim_model->id)->get();
        $is_withdraw_true = 0;
        $tls_cost_total = 0;
        $claim_list->map(function ($item) use (&$is_withdraw_true, &$tls_cost_total) {
            if ($item->accident_claim_list_id) {
                $accident_claim_text = ClaimList::find($item->accident_claim_list_id);
                $item->accident_claim_text = $accident_claim_text->name;
            }
            $item->accident_claim_id = ($item->accident_claim_list_id) ? $item->accident_claim_list_id : '';
            $item->wound_characteristics_text = ($item->wound_characteristics) ? __('accident_informs.wound_type_' . $item->wound_characteristics) : '';
            $item->wound_characteristics = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->wound_characteristics_id = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->supplier_text = (!is_null($item->supplier)) ? __('accident_informs.spare_part_status_' . $item->supplier) : '';
            $item->tls_cost = ($item->cost) ? $item->cost : '';

            $before_medias = $item->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->before_files = $before_medias;

            $after_medias = $item->getMedia('after_file');
            $after_medias = get_medias_detail($after_medias);
            $after_medias = collect($after_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->after_files = $after_medias;

            $item->pending_delete_slide_files = [];

            if ($item->is_withdraw_true == 1) {
                $is_withdraw_true += 1;
                $tls_cost_total = $tls_cost_total + intval($item->tls_cost);
            }

            return $item;
        });

        return [
            'claim_list' => $claim_list,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total
        ];
    }

    public function createCarSlide($accident, $request_data = null)
    {
        $slide = new Slide();
        $slide_count = DB::table('slides')->count() + 1;
        $prefix = 'SL-';
        $slide->worksheet_no = generateRecordNumber($prefix, $slide_count);
        $slide->origin_place = $request_data->lift_from;
        $slide->origin_date = $request_data->lift_date;
        // $slide->origin_contact = $request_data->original_contact;
        // $slide->origin_tel = $request_data->original_tel;
        $slide->destination_place = $request_data->lift_to;
        $slide->destination_date = $request_data->destination_date;
        // $slide->destination_contact = $request_data->destination_contact;
        // $slide->destination_tel = $request_data->destination_tel;
        $slide->type = SlideTypeEnum::CAR;
        $slide->job_type = Accident::class;
        $slide->job_id = $accident->id;
        $slide->save();

        // $slide_line = SlideLine::where('slide_id', $slide->id)->delete();
        // $slide_line = new SlideLine();
        // $slide_line->slide_id = $slide->id;
        // $slide_line->car_id = $car_auction->car_id;
        // $slide_line->type = SlideLineTypeEnum::PICKUP;
        // $slide_line->save();

        return $slide;
    }

    public function createCarReplacement($accident, $request)
    {
        $user = Auth::user();
        $replacement_car_count = DB::table('replacement_cars')->count() + 1;
        $replacement_car = new ReplacementCar();
        $prefix = 'RC-';
        $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
        $replacement_car->replacement_type = $request->replacement_type;
        $replacement_car->job_type = Accident::class;
        $replacement_car->job_id = $accident->id;
        $replacement_car->branch_id = $user ? $user->branch_id : null;
        $replacement_car->main_car_id = $accident->car_id;
        $replacement_car->replacement_expect_date = $request->replacement_expect_date;
        $replacement_car->is_need_driver = $request->is_driver_replacement;
        $replacement_car->is_need_slide = STATUS_DEFAULT;
        $replacement_car->replacement_expect_place = $request->replacement_expect_place;
        $replacement_car->customer_name = $request->customer_name;
        $replacement_car->tel = $request->tel;
        $replacement_car->remark = $request->remark;

        if ($request->replacement_car_files__pending_delete_ids) {
            $pending_delete_ids = $request->replacement_car_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $replacement_car->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('replacement_car_files')) {
            foreach ($request->file('replacement_car_files') as $image) {
                if ($image->isValid()) {
                    $replacement_car->addMedia($image)->toMediaCollection('replacement_car_files');
                }
            }
        }

        $replacement_car->save();

        return $replacement_car;
    }

    public function createCarReplacementEdit(Request $request)
    {
        if (!$request->id) {
            $user = Auth::user();
            $replacement_car_count = DB::table('replacement_cars')->count() + 1;
            $replacement_car = new ReplacementCar();
            $prefix = 'RC-';
            $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
            $replacement_car->replacement_type = $request['replacement_type'];
            $replacement_car->job_type = Accident::class;
            $replacement_car->job_id = $request['accident_id'];
            $replacement_car->branch_id = $user ? $user->branch_id : null;
            $replacement_car->main_car_id = $request['car_id'];
            $replacement_car->replacement_expect_date = isset($request['replacement_expect_date']) ? $request['replacement_expect_date'] : null;
            $replacement_car->replacement_date = isset($request['replacement_pickup_date']) ? $request['replacement_pickup_date'] : null;
            $replacement_car->is_need_driver = isset($request['is_driver_replacement']) ? $request['is_driver_replacement'] : STATUS_DEFAULT;
            $replacement_car->is_need_slide = STATUS_DEFAULT;
            $replacement_car->customer_name = isset($request['customer_name']) ? $request['customer_name'] : null;
            $replacement_car->tel = isset($request['tel']) ? $request['tel'] : null;
            $replacement_car->remark = isset($request['remark']) ? $request['remark'] : null;
            $replacement_car->slide_id = isset($request['slide_worksheet']) ? $request['slide_worksheet'] : null;
            $replacement_car->replacement_place = isset($request['place']) ? $request['place'] : null;
            $replacement_car->is_cust_receive_replace = isset($request['customer_receive']) ? $request['customer_receive'] : null;

            if ($request->replacment_file__pending_delete_ids) {
                $pending_delete_ids = $request->replacment_file__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $replacement_car->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('replacment_file')) {
                foreach ($request->file('replacment_file') as $image) {
                    if ($image->isValid()) {
                        $replacement_car->addMedia($image)->toMediaCollection('replacement_car_files');
                    }
                }
            }

            $replacement_car->save();
        } else {
            $user = Auth::user();
            // $replacement_car_count = DB::table('replacement_cars')->count() + 1;
            $replacement_car = ReplacementCar::find($request->id);
            // $prefix = 'RC-';
            // $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
            $replacement_car->replacement_type = $request['replacement_type'];
            // $replacement_car->job_type = Accident::class;
            // $replacement_car->job_id = $request['accident_id'];
            $replacement_car->branch_id = $user ? $user->branch_id : null;
            $replacement_car->main_car_id = $request['car_id'];
            $replacement_car->replacement_expect_date = isset($request['replacement_expect_date']) ? $request['replacement_expect_date'] : null;
            $replacement_car->replacement_date = isset($request['replacement_pickup_date']) ? $request['replacement_pickup_date'] : null;
            $replacement_car->is_need_driver = isset($request['is_driver_replacement']) ? $request['is_driver_replacement'] : STATUS_DEFAULT;
            $replacement_car->is_need_slide = STATUS_DEFAULT;
            $replacement_car->customer_name = isset($request['customer_name']) ? $request['customer_name'] : null;
            $replacement_car->tel = isset($request['tel']) ? $request['tel'] : null;
            $replacement_car->remark = isset($request['remark']) ? $request['remark'] : null;
            $replacement_car->slide_id = isset($request['slide_worksheet']) ? $request['slide_worksheet'] : null;
            $replacement_car->replacement_place = isset($request['place']) ? $request['place'] : null;
            $replacement_car->is_cust_receive_replace = isset($request['customer_receive']) ? $request['customer_receive'] : null;

            if ($request->replacment_file__pending_delete_ids) {
                $pending_delete_ids = $request->replacment_file__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $replacement_car->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('replacment_file')) {
                foreach ($request->file('replacment_file') as $image) {
                    if ($image->isValid()) {
                        $replacement_car->addMedia($image)->toMediaCollection('replacement_car_files');
                    }
                }
            }

            $replacement_car->save();
        }

        $redirect_route = route('admin.accident-informs.edit', ['accident_inform' => $request['accident_id']]);
        return $this->responseValidateSuccess($redirect_route);
    }



    public function saveSlideAccident(Request $request)
    {
        if ($request->id == null) {
            if ($request->slide_type == AccidentSlideEnum::TLS_SLIDE) {
                $slide = new Slide();
                $slide_count = DB::table('slides')->count() + 1;
                $prefix = 'SL-';
                $slide->worksheet_no = generateRecordNumber($prefix, $slide_count);
                $slide->origin_place = $request->lift_from;
                $slide->origin_date = $request->lift_date;
                $slide->origin_contact = $request->original_contact;
                $slide->origin_tel = $request->original_tel;
                $slide->destination_place = $request->lift_to;
                $slide->destination_date = $request->lift_date_to;
                $slide->destination_contact = $request->destination_contact;
                $slide->destination_tel = $request->destination_tel;
                $slide->type = SlideTypeEnum::CAR;
                $slide->job_type = Accident::class;
                $slide->job_id = $request->accident_id;
                $slide->save();
            }

            $accident_slide_second = new AccidentSlide();
            $accident_slide_second->accident_id = $request->accident_id;
            if ($request->slide_type == AccidentSlideEnum::TLS_SLIDE) {
                $accident_slide_second->job_type = Slide::class;
                $accident_slide_second->job_id = $slide->id;
            }
            $accident_slide_second->slide_date = $request->lift_date;
            $accident_slide_second->slide_from = $request->lift_from;
            $accident_slide_second->slide_to = $request->lift_to;
            $slide_price = str_replace(',', '', $request->lift_price);
            $accident_slide_second->slide_price = $slide_price ? $slide_price : null;
            $accident_slide_second->slide_driver = "TRUE LEASING";
            $accident_slide_second->save();
        } else {
            $accident_slide_second = AccidentSlide::find($request->id);
            $accident_slide_second->accident_id = $request->accident_id;
            $accident_slide_second->slide_date = $request->lift_date;
            $accident_slide_second->slide_from = $request->lift_from;
            $accident_slide_second->slide_to = $request->lift_to;
            $slide_price = str_replace(',', '', $request->lift_price);
            $accident_slide_second->slide_price = $slide_price ? $slide_price : null;
            $accident_slide_second->slide_driver = "TRUE LEASING";
            $accident_slide_second->save();

            if ($accident_slide_second->job_type == Slide::class) {
                $slide = Slide::find($accident_slide_second->job_id);
                $slide->origin_place = $request->lift_from;
                $slide->origin_date = $request->lift_date;
                $slide->origin_contact = $request->original_contact;
                $slide->origin_tel = $request->original_tel;
                $slide->destination_place = $request->lift_to;
                $slide->destination_date = $request->lift_date_to;
                $slide->destination_contact = $request->destination_contact;
                $slide->destination_tel = $request->destination_tel;
                $slide->type = SlideTypeEnum::CAR;
                $slide->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
        ]);
    }
}
