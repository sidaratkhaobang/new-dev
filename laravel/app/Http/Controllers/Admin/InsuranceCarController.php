<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceRegistrationEnum;
use App\Enums\InsuranceStatusEnum;
use App\Enums\InsuranceCarEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\CMIStatusEnum;
use App\Enums\CMITypeEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\MITypeListEnum;
use App\Models\Car;
use App\Models\CMI;
use App\Models\VMI;
use App\Models\ImportCarLine;
use App\Models\InsuranceCompanies;
use App\Models\InsuranceLot;
use App\Models\PurchaseOrder;
use App\Traits\InsuranceTrait;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InsuranceCarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCar);
        $car_plate = $request?->car_plate;
        $policy_number = $request?->policy_number;
        $insurance_company = $request?->insurance_company;
        $term_start_date = $request?->term_start_date;
        $term_end_date = $request?->term_end_date;
        $job_type = $request?->job_type;
        $status = $request?->status;
        $car_plate_list = $this->getCarPlateList();

        $policy_reference_cmi_list = $this->getReferencePolicyList();
        $job_type_list = $this->getJobType();
        //        Sorting Data
        $car_list = Car::when(empty($job_type) || $job_type == InsuranceCarEnum::CMI, function ($query_data) use ($policy_number, $insurance_company, $term_start_date, $term_end_date, $status, $car_plate) {
                $query_data->whereHas('cmi', function ($query) use ($policy_number, $insurance_company, $term_start_date, $term_end_date, $status, $car_plate) {
                    $query->when(!empty($car_plate), function ($query_cmi) use ($car_plate) {
                        $query_cmi->where('cars.id', $car_plate);
                    });
                    $query->when($status == InsuranceCarEnum::CMI, function ($query_cmi) {
                        $query_cmi->has('cmi', '>=', 1);
                    });
                    $query->when(!empty($status), function ($query_cmi) use ($status) {
                        $query_cmi->where('status_cmi', $status);
                    });
                    $query->when(!empty($policy_number), function ($query_cmi) use ($policy_number) {
                        $query_cmi->where('policy_reference_cmi', $policy_number);
                    });
                    $query->when(!empty($insurance_company), function ($query_cmi) use ($insurance_company) {
                        $query_cmi->where('insurer_id', $insurance_company);
                    });
                    $query->when(!empty($term_start_date), function ($query_cmi) use ($term_start_date) {
                        $query_cmi->whereDate('term_start_date', '>=', $term_start_date);
                    });
                    $query->when(!empty($term_end_date), function ($query_cmi) use ($term_end_date) {
                        $query_cmi->whereDate('term_end_date', '<=', $term_end_date);
                    });
                });
            })
            ->when(empty($job_type) || $job_type == InsuranceCarEnum::VMI, function ($query_data) use ($policy_number, $insurance_company, $term_start_date, $term_end_date, $status, $car_plate) {
                $query_data->orwhereHas('vmi', function ($query) use ($policy_number, $insurance_company, $term_start_date, $term_end_date, $status, $car_plate) {
                    $query->when(!empty($car_plate), function ($query_vmi) use ($car_plate) {
                        $query_vmi->where('cars.id', $car_plate);
                    });
                    $query->when($status == InsuranceCarEnum::VMI, function ($query_vmi) {
                        $query_vmi->has('vmi', '>=', 1);
                    });
                    $query->when(!empty($status), function ($query_vmi) use ($status) {
                        $query_vmi->where('status_vmi', $status);
                    });
                    $query->when(!empty($policy_number), function ($query_vmi) use ($policy_number) {
                        $query_vmi->where('policy_reference_vmi', $policy_number);
                    });
                    $query->when(!empty($insurance_company), function ($query_vmi) use ($insurance_company) {
                        $query_vmi->where('insurer_id', $insurance_company);
                    });
                    $query->when(!empty($term_start_date), function ($query_vmi) use ($term_start_date) {
                        $query_vmi->whereDate('term_start_date', '>=', $term_start_date);
                    });
                    $query->when(!empty($term_end_date), function ($query_vmi) use ($term_end_date) {
                        $query_vmi->whereDate('term_end_date', '<=', $term_end_date);
                    });
                });
            })
            ->paginate(3);
        //        สร้าง paginate
        $car_list->getCollection()->transform(function ($item) use ($policy_number, $insurance_company, $term_start_date, $term_end_date, $job_type, $status) {
            $item->cmi_data = $this->getCarCmiData($item, $policy_number, $insurance_company, $term_start_date, $term_end_date, $job_type, $status);
            $item->vmi_data = $this->getCarVmiData($item, $policy_number, $insurance_company, $term_start_date, $term_end_date, $job_type, $status);
            $item->disabled_cmi = false;
            $item->disabled_vmi = false;
            if (sizeof($item->cmi_data) <= 0) {
                $item->disabled_cmi = true;
            }
            if (sizeof($item->vmi_data) <= 0) {
                $item->disabled_vmi = true;
            }
            $_car_id = $item->id;
            $cmi_under_policy = $this->getCurrentCMICar($_car_id);
            $vmi_under_policy = $this->getCurrentVMICar($_car_id);
            if (empty($cmi_under_policy)) {
                $item->disabled_cmi = true;
            }
            if (empty($vmi_under_policy)) {
                $item->disabled_vmi = true;
            }
            $item->have_gps = !empty($item->have_gps);
            $item->class_name = $item->carClass?->full_name;
            return $item;
        });
        $insurer_list = InsuranceTrait::getInsurerList();
        $insurance_job_type_list = $this->getCmiInsuranceJobType();
        $status_list = $this->getStatusType();
        $year_list = $this->getRenewYearList();

        return view('admin.insurance-car.index', [
            'car_list' => $car_list,
            'insurer_list' => $insurer_list,
            'insurance_job_type_list' => $insurance_job_type_list,
            'car_plate_list' => $car_plate_list,
            'policy_reference_cmi_list' => $policy_reference_cmi_list,
            'job_type_list' => $job_type_list,
            'car_plate' => $car_plate,
            'policy_number' => $policy_number,
            'insurance_company' => $insurance_company,
            'term_start_date' => $term_start_date,
            'term_end_date' => $term_end_date,
            'job_type' => $job_type,
            'status_list' => $status_list,
            'status' => $status,
            'year_list' => $year_list,
        ]);
    }

    public function getCarPlateList()
    {
        $cmiQuery = CMI::select('car_id')
            ->groupBy('car_id');

        $vmiQuery = VMI::select('car_id')
            ->groupBy('car_id');

        $car_id = $cmiQuery->union($vmiQuery)
            ->groupby('car_id')
            ->pluck('car_id');
        if (!empty($car_id)) {
            $car_plate_list =  Car::wherein('id', $car_id)
                ->get()
                ->map(function ($item) {
                    $license_plate = $item->license_plate ?: '-';
                    $chassis_no = $item->chassis_no ?: '-';
                    $item->name = $license_plate . '/' . $chassis_no;
                    $item->id = $item->id;
                    $item->value = $item->id;
                    return $item;
                });
        }
        $car_plate_list = $car_plate_list ?: [];
        return $car_plate_list;
    }
    public function getReferencePolicyList()
    {
        $firstQuery = CMI::select('policy_reference_cmi as name')
            ->whereNotnull('status_cmi');

        $secondQuery = VMI::select('policy_reference_vmi as name')
            ->whereNotnull('status_vmi');

        $reference_policy_List = $firstQuery->union($secondQuery)
            ->groupBy('name')
            ->get()->map(function ($item) {
                $item->name = $item->name;
                $item->value = $item->name;
                $item->id = $item->name;
                return $item;
            });

        $reference_policy_List = $reference_policy_List ?: [];
        return $reference_policy_List;
    }
    public function getRenewYearList()
    {
        $renew_year = collect([
            (object)[
                'id' => 1,
                'name' => '1 ปี',
                'value' => 1,
            ]
        ]);

        return $renew_year;
    }

    public function getStatusType()
    {
        $status = collect([
            (object)[
                'id' => InsuranceCarStatusEnum::UNDER_POLICY,
                'name' => __('insurance_car.class_' . InsuranceCarStatusEnum::UNDER_POLICY),
                'value' => InsuranceCarStatusEnum::UNDER_POLICY,
            ], (object)[
                'id' => InsuranceCarStatusEnum::RENEW_POLICY,
                'name' => __('insurance_car.class_' . InsuranceCarStatusEnum::RENEW_POLICY),
                'value' => InsuranceCarStatusEnum::RENEW_POLICY,
            ], (object)[
                'id' => InsuranceCarStatusEnum::END_POLICY,
                'name' => __('insurance_car.class_' . InsuranceCarStatusEnum::END_POLICY),
                'value' => InsuranceCarStatusEnum::END_POLICY,
            ], (object)[
                'id' => InsuranceCarStatusEnum::CANCEL_POLICY,
                'name' => __('insurance_car.class_' . InsuranceCarStatusEnum::CANCEL_POLICY),
                'value' => InsuranceCarStatusEnum::CANCEL_POLICY,
            ], (object)[
                'id' => InsuranceCarStatusEnum::REQUEST_CANCEL,
                'name' => __('insurance_car.class_' . InsuranceCarStatusEnum::REQUEST_CANCEL),
                'value' => InsuranceCarStatusEnum::REQUEST_CANCEL,
            ]
        ]);

        return $status;
    }

    public function getCarCmiData($item, $policy_number, $insurance_company, $term_start_date, $term_end_date, $job_type, $status)
    {
        if (!empty($job_type) && $job_type != InsuranceCarEnum::CMI) {
            return [];
        }
        $cmi = $item->cmi->whereNotNull('status_cmi')->sortByDesc('created_at');
        if (!empty($policy_number)) {
            $cmi = $cmi->where('policy_reference_cmi', $policy_number);
        }
        if (!empty($insurance_company)) {
            $cmi = $cmi->where('insurer_id', $insurance_company);
        }
        if (!empty($term_start_date)) {
            $cmi = $cmi->filter(function ($cmiItem) use ($term_start_date) {
                $cmi_date = Carbon::parse($cmiItem->term_start_date)->format('Y-m-d');
                return $cmi_date >= $term_start_date;
            });
        }
        if (!empty($term_end_date)) {
            $cmi = $cmi->filter(function ($cmiItem) use ($term_end_date) {
                $cmi_date = Carbon::parse($cmiItem->term_end_date)->format('Y-m-d');
                return $cmi_date <= $term_end_date;
            });
        }
        if (!empty($status)) {
            $cmi = $cmi->where('status_cmi', $status);
        }

        if (!empty($cmi)) {
            $cmi = $cmi->map(function ($item) {
                $total_renew = CMI::where('car_id', $item->car_id)
                    ->where('status_cmi', InsuranceCarStatusEnum::RENEW_POLICY)
                    ->count();

                $time_now = Carbon::now()->format('Y-m-d H:i:s');
                if ($item->term_end_date > $time_now && $total_renew == 0 && $item->status_cmi == InsuranceCarStatusEnum::UNDER_POLICY) {
                    $item->renew_status = 1;
                } else {
                    $item->renew_status = 0;
                }
                return $item;
            });
            return $cmi;
        } else {
            return [];
        }
    }

    public function getCarVmiData($item, $policy_number, $insurance_company, $term_start_date, $term_end_date, $job_type, $status)
    {
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        if (!empty($job_type) && $job_type != InsuranceCarEnum::VMI) {
            return [];
        }
        $vmi = $item->vmi->whereNotNull('status_vmi')->sortByDesc('created_at');
        if (!empty($policy_number)) {
            $vmi = $vmi->where('policy_reference_vmi', $policy_number);
        }
        if (!empty($insurance_company)) {
            $vmi = $vmi->where('insurer_id', $insurance_company);
        }
        if (!empty($term_start_date)) {
            $vmi = $vmi->filter(function ($vmiItem) use ($term_start_date) {
                $vmi_date = Carbon::parse($vmiItem->term_start_date)->format('Y-m-d');
                return $vmi_date >= $term_start_date;
            });
        }
        if (!empty($term_end_date)) {
            $vmi = $vmi->filter(function ($vmiItem) use ($term_end_date) {
                $vmi_date = Carbon::parse($vmiItem->term_end_date)->format('Y-m-d');
                return $vmi_date <= $term_end_date;
            });
        }
        if (!empty($status)) {
            $vmi = $vmi->where('status_vmi', $status);
        }

        if (!empty($vmi)) {
            $vmi = $vmi->map(function ($item) use ($insurance_type_list) {
                $total_renew = VMI::where('car_id', $item->car_id)
                    ->where('status_vmi', InsuranceCarStatusEnum::RENEW_POLICY)
                    ->count();
                $time_now = Carbon::now()->format('Y-m-d H:i:s');
                $filteredItem = $insurance_type_list->firstWhere('id', $item->insurance_type);
                $item->insurance_type = $filteredItem->name;
                if ($item->term_end_date > $time_now && $total_renew == 0 && $item->status_vmi == InsuranceCarStatusEnum::UNDER_POLICY) {
                    $item->renew_status = 1;
                } else {
                    $item->renew_status = 0;
                }
                return $item;
            });
            return $vmi;
        } else {
            return [];
        }
    }

    public function getCmiInsuranceJobType()
    {
        $car_statues = collect([
            (object)[
                'id' => 'RenewCmi',
                'name' => 'ต่ออายุ',
                'value' => 'ต่ออายุ',
            ],
        ]);
        return $car_statues;
    }

    public function getCarData(Request $request)
    {
        $car_data = [];
        $car_id = $request?->car_id ?: [];
        $renew_type = $request?->renew_type ?: null;
        $insurance_type = $request?->type ?: null;
        $type = $request?->type ?: null;
        $insurance_id = $request?->insurance_id ?: null;
        $term_start_date = $request?->term_start_date ?: null;
        $term_end_date = $request?->term_end_date ?: null;
        if (!empty($car_id) || $renew_type == "ALL") {
            $car_data_query = Car::when(empty($renew_type), function ($query) use ($car_id) {
                $query->wherein('id', $car_id)->get();
            })
                ->when(!empty($renew_type) && $renew_type == "ALL", function ($query) use ($insurance_type, $term_start_date, $term_end_date) {
                    if ($insurance_type == InsuranceCarEnum::CMI) {
                        $query->whereHas('cmi', function ($query_cmi) use ($term_start_date, $term_end_date) {
                            $query_cmi->whereDate('term_end_date', '>=', Carbon::now()->toDateString());
                            $query_cmi->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY);
                            $query_cmi->when(!empty($term_start_date), function ($query_date) use ($term_start_date) {
                                $query_date->where('term_start_date', '>=', $term_start_date);
                            });
                            $query_cmi->when(!empty($term_end_date), function ($query_date) use ($term_end_date) {
                                $query_date->where('term_end_date', '>=', $term_end_date);
                            });
                        })
                            ->whereDoesntHave('cmi', function ($query_cmi) {
                                $query_cmi->where('status_cmi', InsuranceCarStatusEnum::RENEW_POLICY);
                            });
                    }
                    if ($insurance_type == InsuranceCarEnum::VMI) {
                        $query->whereHas('vmi', function ($query_vmi) use ($term_start_date, $term_end_date) {
                            $query_vmi->whereDate('term_end_date', '>=', Carbon::now()->toDateString());
                            $query_vmi->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY);
                            $query_vmi->when(!empty($term_start_date), function ($query_date) use ($term_start_date) {
                                $query_date->where('term_start_date', '>=', $term_start_date);
                            });
                            $query_vmi->when(!empty($term_end_date), function ($query_date) use ($term_end_date) {
                                $query_date->where('term_end_date', '>=', $term_end_date);
                            });
                        })
                            ->whereDoesntHave('vmi', function ($query_vmi) {
                                $query_vmi->where('status_vmi', InsuranceCarStatusEnum::RENEW_POLICY);
                            });
                    }
                })
                ->get();

            if (!empty($car_data_query)) {
                $car_data = [
                    'car_id' => $car_data_query,
                    'year' => $this->getYearInsurance($type, $insurance_id),
                ];
            }
        }
        return $car_data;
    }

    public function getYearInsurance($type = null, $insurance_id)
    {
        $year = null;
        if (!empty($type) && $type == InsuranceCarEnum::CMI) {
            $cmi_year =  CMI::where('id', $insurance_id)->get()->toArray();

            if (!empty($cmi_year)) {
                $year = $cmi_year[0]['year'] + 1;
            }
        }
        if (!empty($type) && $type == InsuranceCarEnum::VMI) {
            $vmi_year =  VMI::where('id', $insurance_id)->get()->toArray();
            if (!empty($vmi_year)) {
                $year = $vmi_year[0]['year'] + 1;
            }
        }
        return $year;
    }
    public function getJobType()
    {
        $job_type_list = collect([
            (object)[
                'id' => InsuranceCarEnum::CMI,
                'name' => 'พรบ',
                'value' => InsuranceCarEnum::CMI,
            ],
            (object)[
                'id' => 'VMI',
                'name' => 'ประกันภัย',
                'value' => 'VMI',
            ],
        ]);
        return $job_type_list;
    }

    public function getLot()
    {
        $insurance_lot_count = DB::table('insurance_lots')->count() + 1;
        $prefix = 'Lot-';
        $lot = generateRecordNumber($prefix, $insurance_lot_count);
        return $lot;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        $validate_data = [
            'car_id' => 'required',
            //            'modal_renew_cmi_year' => 'required',
            'modal_renew_cmi_startdate' => 'required',
            'modal_renew_cmi_enddate' => 'required|after:modal_renew_cmi_start',
        ];
        $validator = Validator::make($request->all(), $validate_data, [], [
            'car_id' => __('insurance_car.cars_require'),
            //            'modal_renew_cmi_year' => __('insurance_car.year_require'),
            'modal_renew_cmi_startdate' => __('insurance_car.insurance_start_date'),
            'modal_renew_cmi_enddate' => __('insurance_car.insurance_end_date'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Validation Insurance company
        if (!array_key_exists('modal-renew-cmi-insurance-status', $request->all())) {
            $validate_data = [
                'modal_renew_insurance_company' => 'required',
            ];
            $validator = Validator::make($request->all(), $validate_data, [], [
                'modal_renew_insurance_company' => __('insurance_car.insurance_company'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $insurance_company = $request?->modal_renew_insurance_company;
        } else {
            $insurance_company = null;
        }
        $response_status = $this->responseWithCode(false, __('insurance_car.cmi_data_not_found'), null, 404);
        $job_type = $request?->type;
        $term_start_date = $request?->modal_renew_cmi_startdate;
        $term_end_date = $request?->modal_renew_cmi_enddate;
        if (!empty($job_type)) {
            $car_id = json_decode($request->car_id, true);
            $car_id = collect($car_id)->pluck('id')->toArray();
            if ($job_type == InsuranceCarEnum::CMI) {
                if (!empty($car_id)) {
                    $response_status = $this->renewCmiAll($car_id, $term_start_date, $term_end_date, $insurance_company, $request?->insurance_cmi_id);
                }
            }
            if ($job_type == InsuranceCarEnum::VMI) {
                if (!empty($car_id)) {
                    $response_status = $this->renewVmiAll($car_id, $term_start_date, $term_end_date, $insurance_company, $request?->insurance_vmi_id);
                }
            }
        }

        return $response_status;
    }


    public function getLotData()
    {
        $lot = new InsuranceLot();
        $insurance_lot_count = DB::table('insurance_lots')->count() + 1;
        $prefix = 'Lot-';
        $lot->lot_no = generateRecordNumber($prefix, $insurance_lot_count);
        $lot->year = 1;
        $lot->save();

        return $lot->id;
    }

    public function renewCmiAll($cmi_id, $term_start_date, $term_end_date, $insurance_company, $cmi_data_id)
    {

        $response_status = $this->responseWithCode(false, __('insurance_car.cmi_data_not_found'), null, 404);
        $redirect_route = route('admin.insurance-car.index');
        if (!empty($cmi_id)) {

            $lot = $this->getLotData();
            foreach ($cmi_id as $key_cmi_id => $value_cmi_id) {

                $cmi_data = CMI::where('car_id', $value_cmi_id)
                    ->select(
                        DB::raw('(SELECT count(*) from compulsory_motor_insurances as tbl1 where tbl1.car_id = "' . $value_cmi_id . '" and tbl1.status_cmi = "RENEW_POLICY") as total_renew'),
                        DB::raw('TIMESTAMPDIFF(MONTH, term_end_date, CURDATE()) as month_diff'),
                        DB::raw('if(term_end_date < CURRENT_TIMESTAMP(),0,1) as renew_status'),
                        'id',
                        'car_id'
                    )
                    ->when(!empty($cmi_data_id), function ($query) use ($cmi_data_id) {
                        $query->where('id', $cmi_data_id);
                    })
                    ->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY)
                    ->whereDate('term_end_date', '>=', Carbon::now()->toDateString())
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()->map(function ($item) {
                        if ($item->total_renew == 0) {
                            return $item;
                        }
                    })->toArray();

                $cmi_data = array_filter($cmi_data);
                if (!empty($cmi_data)) {
                    foreach ($cmi_data as $key_cmi => $value_cmi) {

                        $cmi = CMI::findOrFail($value_cmi['id']);
                        $cmi_new_data = new CMI;
                        $cmi_new_data->car_id = $cmi->car_id;
                        $cmi_new_data->job_type = $cmi->job_type;
                        $cmi_new_data->job_id = $cmi->job_id;
                        $cmi_new_data->car_class_insurance_id = $cmi->car_class_insurance_id;
                        $cmi_new_data->type_vmi = $cmi->type_vmi;
                        $cmi_new_data->type_cmi = $cmi->type_cmi;
                        $cmi_new_data->sum_insured_car = $cmi->sum_insured_car;
                        $cmi_new_data->sum_insured_accessory = $cmi->sum_insured_accessory;
                        $cmi_count = DB::table('compulsory_motor_insurances')->count() + 1;
                        $prefix = 'CMI-';
                        //    Save New Data
                        $cmi_new_data->worksheet_no = generateRecordNumber($prefix, $cmi_count);
                        $cmi_new_data->term_start_date = $term_start_date;
                        $cmi_new_data->term_end_date = $term_end_date;
                        $cmi_new_data->insurer_id = $cmi->insurer_id;
                        $cmi_new_data->beneficiary_id = $cmi->beneficiary_id;
                        if (!empty($insurance_company)) {
                            $cmi_new_data->insurer_id = $insurance_company;
                        }
                        $cmi_new_data->status_cmi = InsuranceCarStatusEnum::RENEW_POLICY;
                        $cmi_new_data->status = InsuranceStatusEnum::IN_PROCESS;
                        $cmi_new_data->type = InsuranceRegistrationEnum::RENEW;
                        $cmi_new_data->lot_id = $lot;
                        $cmi_new_data->year = $cmi->year + 1;
                        $cmi_new_data->save();
                    }
                }
                $response_status = $this->responseValidateSuccess($redirect_route);
            }
        }

        return $response_status;
    }

    public function renewVmiAll($vmi_id, $term_start_date, $term_end_date, $insurance_company, $vmi_data_id)
    {

        $response_status = $this->responseWithCode(false, __('insurance_car.cmi_data_not_found'), null, 404);
        $redirect_route = route('admin.insurance-car.index');
        if (!empty($vmi_id)) {
            $lot = $this->getLotData();
            foreach ($vmi_id as $key_vmi_id => $value_vmi_id) {
                $vmi_data = VMI::where('car_id', $value_vmi_id)
                    ->select(
                        DB::raw('(SELECT count(*) from voluntary_motor_insurances as tbl1 where  tbl1.car_id = "' . $value_vmi_id . '" and tbl1.status_vmi = "RENEW_POLICY") as total_renew'),
                        DB::raw('TIMESTAMPDIFF(MONTH, term_end_date, CURDATE()) as month_diff'),
                        DB::raw('if(term_end_date < CURRENT_TIMESTAMP(),0,1) as renew_status'),
                        'id',
                        'car_id'
                    )
                    ->when(!empty($vmi_data_id), function ($query) use ($vmi_data_id) {
                        $query->where('id', $vmi_data_id);
                    })
                    ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
                    ->whereDate('term_end_date', '>=', Carbon::now()->toDateString())
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->map(function ($item) {
                        if ($item->total_renew == 0) {
                            return $item;
                        }
                    })->toArray();
                $vmi_data = array_filter($vmi_data);
                if (!empty($vmi_data)) {
                    foreach ($vmi_data as $key_vmi => $value_vmi) {
                        $vmi = VMI::findOrFail($value_vmi['id']);
                        $vmi_new_data = $vmi->replicate();
                        $vmi_new_data->send_date = null;
                        $vmi_new_data->receive_date = null;
                        $vmi_new_data->check_date = null;
                        $vmi_new_data->policy_reference_vmi = null;
                        $vmi_new_data->endorse_vmi = null;
                        $vmi_new_data->policy_reference_child_vmi = null;
                        $vmi_new_data->premium = null;
                        $vmi_new_data->discount = null;
                        $vmi_new_data->stamp_duty = null;
                        $vmi_new_data->tax = null;
                        $vmi_new_data->statement_no = null;
                        $vmi_new_data->tax_invoice_no = null;
                        $vmi_new_data->statement_date = null;
                        $vmi_new_data->account_submission_date = null;
                        $vmi_new_data->operated_date = null;
                        $vmi_new_data->status_pay_premium = null;
                        $vmi_count = DB::table('voluntary_motor_insurances')->count() + 1;
                        $prefix = 'VMI-';
                        //    Save New Data
                        $vmi_new_data->worksheet_no = generateRecordNumber($prefix, $vmi_count);
                        $vmi_new_data->term_start_date = $term_start_date;
                        $vmi_new_data->term_end_date = $term_end_date;
                        if (!empty($insurance_company)) {
                            $vmi_new_data->insurer_id = $insurance_company;
                        }
                        $vmi_new_data->status_vmi = InsuranceCarStatusEnum::RENEW_POLICY;
                        $vmi_new_data->type = InsuranceRegistrationEnum::RENEW;
                        $vmi_new_data->status = InsuranceStatusEnum::IN_PROCESS;
                        $vmi_new_data->lot_id = $lot;
                        $vmi_new_data->year = $vmi_new_data->year + 1;
                        $vmi_new_data->save();
                    }
                }
                $response_status = $this->responseValidateSuccess($redirect_route);
            }
        }

        return $response_status;
    }

    public function InsuranceCarRenew(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        $type = $request?->type;
        $id = $request?->id;
        if (!empty($id)) {
            if ($type == InsuranceCarEnum::CMI) {
                $response_status = $this->RenewCarCmi($request);
            } else
                if ($type == InsuranceCarEnum::VMI) {
                $response_status = $this->RenewCarVmi($request);
            }
        }
        return $response_status;
    }

    public function RenewCarCmi($request)
    {
        $validate_data = [
            'id' => 'required',
            //            'modal_renew_cmi_year' => 'required',
            'modal_renew_cmi_startdate' => 'required',
            'modal_renew_cmi_enddate' => 'required',
        ];
        $validator = Validator::make($request->all(), $validate_data, [], [
            'id' => __('insurance_car.cars_require'),
            //            'modal_renew_cmi_year' => __('insurance_car.year_require'),
            'modal_renew_cmi_startdate' => __('insurance_car.insurance_start_date'),
            'modal_renew_cmi_enddate' => __('insurance_car.insurance_end_date'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Validation Insurance company
        if (!array_key_exists('modal-renew-cmi-insurance-status', $request->all())) {
            $validate_data = [
                'modal_renew_insurance_company' => 'required',
            ];
            $validator = Validator::make($request->all(), $validate_data, [], [
                'modal_renew_insurance_company' => __('insurance_car.insurance_company'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $insurance_company = $request?->modal_renew_insurance_company;
        } else {
            $insurance_company = null;
        }

        //        Setting Worksheets no
        $cmi_count = DB::table('compulsory_motor_insurances')->count() + 1;
        $prefix = 'CMI-';
        //        Copy Old Data
        $cmi_old_data = CMI::findOrFail($request->id);
        //        Check Cmi
        $cmi_data_renew = CMI::where('car_id', $cmi_old_data->car_id)
            ->where('status_cmi', InsuranceCarStatusEnum::RENEW_POLICY)
            ->get()
            ->toArray();

        if (empty($cmi_data_renew)) {
            $cmi_new_data = new CMI;
            $cmi_new_data->job_type = $cmi_old_data->job_type;
            $cmi_new_data->job_id = $cmi_old_data->job_id;
            $cmi_new_data->car_class_insurance_id = $cmi_old_data->car_class_insurance_id;
            $cmi_new_data->type_vmi = $cmi_old_data->type_vmi;
            $cmi_new_data->type_cmi = $cmi_old_data->type_cmi;
            $cmi_new_data->sum_insured_car = $cmi_old_data->sum_insured_car;
            $cmi_new_data->sum_insured_accessory = $cmi_old_data->sum_insured_accessory;
            //    Save New Data
            $cmi_new_data->worksheet_no = generateRecordNumber($prefix, $cmi_count);
            $cmi_new_data->term_start_date = $request?->modal_renew_cmi_startdate;
            $cmi_new_data->term_end_date = $request?->modal_renew_cmi_enddate;
            if (!empty($insurance_company)) {
                $cmi_new_data->insurer_id = $insurance_company;
            }
            $cmi_new_data->status_cmi = InsuranceCarStatusEnum::RENEW_POLICY;
            $cmi_new_data->type = InsuranceRegistrationEnum::RENEW;
            $cmi_new_data->status = InsuranceStatusEnum::IN_PROCESS;
            $cmi_new_data->lot_id = $this->getLotData();
            $cmi_new_data->year = $cmi_old_data->year + 1;
            $cmi_new_data->save();
            $redirect_route = route('admin.insurance-car.index');
            return $this->responseValidateSuccess($redirect_route);
        } else {
            return $this->responseWithCode(false, 'พบ พรบ ที่ต่ออายุล่วงหน้า', null, 404);
        }
    }

    public function RenewCarVmi($request)
    {
        $validate_data = [
            'id' => 'required',
            //            'modal_renew_cmi_year' => 'required',
            'modal_renew_cmi_startdate' => 'required',
            'modal_renew_cmi_enddate' => 'required',
        ];
        $validator = Validator::make($request->all(), $validate_data, [], [
            'id' => __('insurance_car.cars_require'),
            //            'modal_renew_cmi_year' => __('insurance_car.year_require'),
            'modal_renew_cmi_startdate' => __('insurance_car.insurance_start_date'),
            'modal_renew_cmi_enddate' => __('insurance_car.insurance_end_date'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Validation Insurance company
        if (!array_key_exists('modal-renew-cmi-insurance-status', $request->all())) {
            $validate_data = [
                'modal_renew_insurance_company' => 'required',
            ];
            $validator = Validator::make($request->all(), $validate_data, [], [
                'modal_renew_insurance_company' => __('insurance_car.insurance_company'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $insurance_company = $request?->modal_renew_insurance_company;
        } else {
            $insurance_company = null;
        }
        $vmi_count = DB::table('voluntary_motor_insurances')->count() + 1;
        $prefix = 'VMI-';
        $vmi = new VMI();
        //        $vmi->
        $vmi_old_data = VMI::findOrFail($request->id);
        //        Check Cmi
        $vmi_data_renew = VMI::where('car_id', $vmi_old_data->car_id)
            ->where('status_vmi', InsuranceCarStatusEnum::RENEW_POLICY)
            ->get()
            ->toArray();
        if (empty($vmi_data_renew)) {
            $vmi_new_data = $vmi_old_data->replicate();
            $vmi_new_data->send_date = null;
            $vmi_new_data->receive_date = null;
            $vmi_new_data->check_date = null;
            $vmi_new_data->policy_reference_vmi = null;
            $vmi_new_data->endorse_vmi = null;
            $vmi_new_data->policy_reference_child_vmi = null;
            $vmi_new_data->premium = null;
            $vmi_new_data->discount = null;
            $vmi_new_data->stamp_duty = null;
            $vmi_new_data->tax = null;
            $vmi_new_data->statement_no = null;
            $vmi_new_data->tax_invoice_no = null;
            $vmi_new_data->statement_date = null;
            $vmi_new_data->account_submission_date = null;
            $vmi_new_data->operated_date = null;
            $vmi_new_data->status_pay_premium = null;
            //    Save New Data
            $vmi_new_data->worksheet_no = generateRecordNumber($prefix, $vmi_count);
            $vmi_new_data->term_start_date = $request?->modal_renew_cmi_startdate;
            $vmi_new_data->term_end_date = $request?->modal_renew_cmi_enddate;
            if (!empty($insurance_company)) {
                $vmi_new_data->insurer_id = $insurance_company;
            }
            $vmi_new_data->status_vmi = InsuranceCarStatusEnum::RENEW_POLICY;
            $vmi_new_data->type = InsuranceRegistrationEnum::RENEW;
            $vmi_new_data->status = InsuranceStatusEnum::IN_PROCESS;
            $vmi_new_data->lot_id = $this->getLotData();
            $vmi_new_data->year = $vmi_new_data->year + 1;
            $vmi_new_data->save();
            $redirect_route = route('admin.insurance-car.index');
            return $this->responseValidateSuccess($redirect_route);
        } else {
            return $this->responseWithCode(false, 'พบ ประกัน ที่ต่ออายุล่วงหน้า', null, 404);
        }
    }

    public function getCarAccessoryListData(Request $request)
    {
        $car_accessory = [];
        $type = $request?->type;
        if ($type == InsuranceCarEnum::CMI) {
            $cmi_data = CMI::where('id', $request?->id)?->first();
            $car_accessory = $cmi_data?->car?->accessories?->map(function ($item) {
                $item->name = $item?->carAccessory?->name;
                $item->price = $item?->carAccessory?->price;
                return $item;
            });
        } else
            if ($type == InsuranceCarEnum::VMI) {
            $vmi_data = VMI::where('id', $request?->id)?->first();
            $car_accessory = $vmi_data?->car?->accessories?->map(function ($item) {
                $item->name = $item?->carAccessory?->name;
                $item->price = $item?->carAccessory?->price;
                return $item;
            });
        }
        $car_accessory = $car_accessory ?: [];
        return $car_accessory;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function getCurrentCMICar($car_id)
    {
        $cmi_data = CMI::where('car_id', $car_id)
            ->select(
                DB::raw('(SELECT count(*) from compulsory_motor_insurances as tbl1 where tbl1.car_id = "' . $car_id . '" and tbl1.status_cmi = "RENEW_POLICY") as total_renew'),
                DB::raw('TIMESTAMPDIFF(MONTH, term_end_date, CURDATE()) as month_diff'),
                DB::raw('if(term_end_date < CURRENT_TIMESTAMP(),0,1) as renew_status'),
                'id',
                'car_id'
            )
            // ->when(!empty($cmi_data_id), function ($query) use ($cmi_data_id) {
            //     $query->where('id', $cmi_data_id);
            // })
            ->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY)
            ->whereDate('term_end_date', '>=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()->map(function ($item) {
                if ($item->total_renew == 0) {
                    return $item;
                }
            })->toArray();
        $cmi_data = array_filter($cmi_data);
        return $cmi_data;
    }

    public function getCurrentVMICar($car_id)
    {
        $vmi_data = VMI::where('car_id', $car_id)
            ->select(
                DB::raw('(SELECT count(*) from voluntary_motor_insurances as tbl1 where  tbl1.car_id = "' . $car_id . '" and tbl1.status_vmi = "RENEW_POLICY") as total_renew'),
                DB::raw('TIMESTAMPDIFF(MONTH, term_end_date, CURDATE()) as month_diff'),
                DB::raw('if(term_end_date < CURRENT_TIMESTAMP(),0,1) as renew_status'),
                'id',
                'car_id'
            )
            // ->when(!empty($vmi_data_id), function ($query) use ($vmi_data_id) {
            //     $query->where('id', $vmi_data_id);
            // })
            ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
            ->whereDate('term_end_date', '>=', Carbon::now()->toDateString())
            ->orderBy('created_at', 'DESC')
            ->limit(1)
            ->get()
            ->map(function ($item) {
                if ($item->total_renew == 0) {
                    return $item;
                }
            })->toArray();
        $vmi_data = array_filter($vmi_data);
        return $vmi_data;
    }
}
