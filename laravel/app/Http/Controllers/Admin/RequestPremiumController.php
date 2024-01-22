<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\RequestPremiumEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\InsuranceCompanies;
use App\Models\InsurancePackage;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalType;
use App\Models\RequestPremium;
use App\Models\RequestPremiumCarclassLine;
use App\Models\RequestPremiumPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestPremiumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //        $rq_pr = new RequestPremium;
        //        $rq_pr->job_type = LongTermRental::class;
        //        $rq_pr->status = RequestPremiumEnum::WAIT_PREMIUM;
        //        $rq_pr->job_id = '99861221-4edf-4635-a021-2b6058d92104';
        //        $rq_pr->save();
        //        dd('success');
        $this->authorize(Actions::View . '_' . Resources::RequestPremium);
        $worksheet_no = $request->job_id;
        $job_type = $request->job_type;
        $customer_id = $request->customer_id;
        $request_status = $request->request_status;
        //        return RequestPremium::with('getLongTermRental')->with('getLongTermRentalLine')->get();
        $premium_list = RequestPremium::whereHas('getLongTermRental', function ($query) use ($worksheet_no, $job_type, $customer_id, $request_status) {
            if (!empty($worksheet_no)) {
                $query->where('id', $worksheet_no);
            }
            if (!empty($customer_id)) {
                $query->where('customer_id', $customer_id);
            }
            if (!empty($request_status)) {
                $query->where('request_premiums.status', $request_status);
            }
        })->whereHas('getLongTermRental.rentalType', function ($query) use ($job_type) {
            if (!empty($job_type)) {
                $query->where('job_type', $job_type);
            }
        })
            ->sortable(['created' => 'desc'])->paginate(PER_PAGE);
        if (!empty($premium_list)) {
            foreach ($premium_list as $key_premium => $value_premium) {
                $month_data = $value_premium->getLongTermRental->getLongTermRentalMonth->pluck('month')->toArray();
                $value_premium->month = !empty($month_data) ? implode(',', $month_data) : '-';
                $menuDropdown = [
                    'view_route' => route('admin.request-premium.show', ['request_premium' => $value_premium]),
                    'edit_route' => route('admin.request-premium.edit', ['request_premium' => $value_premium]),
                    'view_permission' => Actions::View . '_' . Resources::RequestPremium,
                    'manage_permission' => Actions::Manage . '_' . Resources::RequestPremium,
                ];
                if ($value_premium->status == RequestPremiumEnum::COMPLETE_PREMIUM) {
                    unset($menuDropdown['edit_route']);
                    unset($menuDropdown['manage_permission']);
                }
                $value_premium->dropdown = $menuDropdown;
            }
        }

        //        ->map(function ($item) {
        //        $month_data = $item->getLongTermRental->getLongTermRentalMonth->pluck('month')->toArray();
        //        $item->month = !empty($month_data) ? implode(',', $month_data) : '-';
        //        return $item;
        //    })

        $request_premium_list = $this->getRequestPremiumList();
        $request_premium_status_list = $this->getRequestPremiumStatus();
        $longter_reantal_type_list = $this->getLongterRentalList();
        $customer_list = $this->getCustomerList();
        $customer_code = null;
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $customer_code = $customer->customer_code . ' - ' . $customer->name;
        }
        return view('admin.request-premium.index', [
            'premium_list' => $premium_list,
            'request_premium_list' => $request_premium_list,
            'longter_reantal_type_list' => $longter_reantal_type_list,
            'request_premium_status_list' => $request_premium_status_list,
            'customer_list' => $customer_list,
            'worksheet_no' => $worksheet_no,
            'job_type' => $job_type,
            'customer_id' => $customer_id,
            'customer_code' => $customer_code,
            'request_status' => $request_status,
        ]);
    }

    public function getRequestPremiumList()
    {
        $request_premium_list = RequestPremium::get()->map(function ($item) {
            $item->id = $item->getLongTermRental?->id;
            $item->name = $item->getLongTermRental?->worksheet_no;
            $item->value = $item->getLongTermRental?->id;
            return $item;
        });
        if (!empty($request_premium_list)) {
            return $request_premium_list;
        } else {
            return [];
        }
    }

    public function getRequestPremiumStatus(): object
    {
        $collection = collect([
            [
                'id' => RequestPremiumEnum::WAIT_PREMIUM,
                'value' => RequestPremiumEnum::WAIT_PREMIUM,
                'name' => 'ขอราคาค่าเบี้ย',
            ],
            [
                'id' => RequestPremiumEnum::COMPLETE_PREMIUM,
                'value' => RequestPremiumEnum::COMPLETE_PREMIUM,
                'name' => 'ยืนยันราคาค่าเบี้ย',
            ],
        ]);
        $objectCollection = $collection->map(function ($item) {
            return (object)$item;
        });

        return $objectCollection;
    }

    public function getLongterRentalList()
    {
        $long_term_rental_list = LongTermRentalType::get()->map(function ($item) {
            $item->id = $item->job_type;
            $item->value = $item->name;
            return $item;
        });
        if (!empty($long_term_rental_list)) {
            return $long_term_rental_list;
        } else {
            return [];
        }
    }

    public function getCustomerList()
    {
        $customer_list = CustomerGroup::get()->map(function ($item) {
            $item->id = $item->id;
            $item->name = $item->name;
            $item->value = $item->id;
            return $item;
        });

        if (!empty($customer_list)) {
            return $customer_list;
        } else {
            return [];
        }
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

        $this->authorize(Actions::Manage . '_' . Resources::RequestPremium);
        $rq_pr_data = RequestPremium::where('id', $request?->lt_rental_id)->first();
        $rq_pr_id = $rq_pr_data->id;
        //    Store Data
        $this->storeRequestPremiumCarclassLine($request['car']['data'], $rq_pr_id);
        $this->checkRequestPremiumStatus($request['car']['data'], $rq_pr_data);
        //        if(!empty($premium_status)){
        //            $rq_pr_data->status = $premium_status;
        //            $rq_pr_data->save();
        //        }
        $redirect_route = route('admin.request-premium.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeRequestPremiumCarclassLine($car_data = null, $rq_pr_id = null)
    {
        if (!empty($car_data) && !empty($rq_pr_id)) {
            foreach ($car_data as $key_car => $value_car) {
                $car_id = $value_car['id'];
                $rq_pr_l = RequestPremiumCarclassLine::firstOrNew(['id' => $car_id]);
                $rq_pr_l->request_premium_id = $rq_pr_id;
                $rq_pr_l->lt_rental_line_id = $value_car['lt_rental_line_id'];
                $rq_pr_l->sum_insured_car = !empty($value_car['sum_insured_car']) ? str_replace(',', '', $value_car['sum_insured_car']) : null;
                $rq_pr_l->sum_insured_accessories = !empty($value_car['sum_insured_accessories']) ? str_replace(',', '', $value_car['sum_insured_accessories']) : null;
                $rq_pr_l->sum_insured = !empty($value_car['sum_insured']) ? str_replace(',', '', $value_car['sum_insured']) : null;
                $rq_pr_l->insurer_id = !empty($value_car['insurer_id']) ? $value_car['insurer_id'] : null;
                $rq_pr_l->insurance_package_id = !empty($value_car['insurance_package_id']) ? $value_car['insurance_package_id'] : null;
                $rq_pr_l->tpbi_person = !empty($value_car['tpbi_person']) ? str_replace(',', '', $value_car['tpbi_person']) : null;
                $rq_pr_l->tpbi_aggregate = !empty($value_car['tpbi_aggregate']) ? str_replace(',', '', $value_car['tpbi_aggregate']) : null;
                $rq_pr_l->tppd_aggregate = !empty($value_car['tppd_aggregate']) ? str_replace(',', '', $value_car['tppd_aggregate']) : null;
                $rq_pr_l->deductible = !empty($value_car['deductible']) ? str_replace(',', '', $value_car['deductible']) : null;
                $rq_pr_l->own_damage = !empty($value_car['own_damage']) ? str_replace(',', '', $value_car['own_damage']) : null;
                $rq_pr_l->fire_and_theft = !empty($value_car['fire_and_theft']) ? str_replace(',', '', $value_car['fire_and_theft']) : null;
                $rq_pr_l->deductible_car = !empty($value_car['deductible_car']) ? str_replace(',', '', $value_car['deductible_car']) : null;
                $rq_pr_l->pa_driver = !empty($value_car['pa_driver']) ? str_replace(',', '', $value_car['pa_driver']) : null;
                $rq_pr_l->pa_passenger = !empty($value_car['pa_passenger']) ? str_replace(',', '', $value_car['pa_passenger']) : null;
                $rq_pr_l->medical_exp = !empty($value_car['medical_exp']) ? str_replace(',', '', $value_car['medical_exp']) : null;
                $rq_pr_l->bailbond = !empty($value_car['bailbond']) ? str_replace(',', '', $value_car['bailbond']) : null;

                $rq_pr_l->save();
                $rq_pr_l_id = $rq_pr_l->id;
                $this->storeRequestPremiumPrice($value_car['premium'], $rq_pr_l_id);
            }
        }
    }

    public function storeRequestPremiumPrice($car_data_premium = null, $rq_pr_l_id = null)
    {
        if (!empty($car_data_premium) && !empty($rq_pr_l_id)) {
            foreach ($car_data_premium as $key_car_preium => $value_car_premium) {
                $car_premium_id = $value_car_premium['id'];
                $rq_p_p = RequestPremiumPrice::firstOrNew(['id' => $car_premium_id]);
                $rq_p_p->request_premium_car_line_id = $rq_pr_l_id;
                $rq_p_p->lt_rental_month_id = $value_car_premium['lt_rental_month_id'];
                $rq_p_p->premium_year_one = !empty($value_car_premium['premium_year_one']) ? str_replace(',', '', $value_car_premium['premium_year_one']) : null;
                $rq_p_p->premium_all_year = !empty($value_car_premium['premium_all_year']) ? str_replace(',', '', $value_car_premium['premium_all_year']) : null;
                $rq_p_p->premium_cmi = !empty($value_car_premium['premium_cmi']) ? str_replace(',', '', $value_car_premium['premium_cmi']) : null;
                $rq_p_p->premium_year_one_plus_cmi = !empty($value_car_premium['premium_year_one_plus_cmi']) ? str_replace(',', '', $value_car_premium['premium_year_one_plus_cmi']) : null;
                $rq_p_p->premium_cmi_plus_all_year = !empty($value_car_premium['premium_cmi_plus_all_year']) ? str_replace(',', '', $value_car_premium['premium_cmi_plus_all_year']) : null;
                $rq_p_p->save();
            }
        }
    }

    public function checkRequestPremiumStatus($car_data = null, $RequestPremium)
    {
        if (!empty($car_data)) {
            $validateUser = Validator::make(
                $car_data,
                [
                    '*.premium.*.premium_year_one' => 'required',
                    '*.premium.*.premium_all_year' => 'required',
                    '*.premium.*.premium_cmi' => 'required',
                    '*.premium.*.premium_year_one_plus_cmi' => 'required',
                    '*.premium.*.premium_cmi_plus_all_year' => 'required',
                ]
            );
            $status = RequestPremiumEnum::COMPLETE_PREMIUM;
            if ($validateUser->fails()) {
                $status = RequestPremiumEnum::WAIT_PREMIUM;
            }
        } else {
            $status = RequestPremiumEnum::WAIT_PREMIUM;
        }
        $RequestPremium->status = $status;
        $RequestPremium->save();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(RequestPremium $request_premium)
    {
        $this->authorize(Actions::View . '_' . Resources::RequestPremium);
        $lt_id = $request_premium?->getLongTermRental?->worksheet_no;
        $page_title = __('lang.edit') . __('request_premium.page_title');
        $insurance_package = InsurancePackage::getPackageList();
        $insurance_companies = InsuranceCompanies::getInsuranceCompaniesListAll();
        $rq_pr_data = $request_premium;
        $car_list = $rq_pr_data?->getLongTermRentalLine->map(function ($item) {
            $item->name = $item?->carClass?->name;
            $item->car_class_full_name = $item?->carClass?->full_name;
            $item->class_name = $item?->carClass?->full_name;
            $item->GPS = $this->getAccessoryStatus($item->id, 'GPS');
            $item->CCTV = $this->getAccessoryStatus($item->id, 'CCTV');
            $item->accessories_price = $this->getAccessoryPriceTotal($item->id);
            $item->created_date = (!empty($item->created_at)) ? date('d-m-Y', strtotime($item->created_at)) : date('d-m-Y');
            $item->car_premium = $item?->getRequestCarClassLine?->getRequestPremiumPrice;
            return $item;
        });
        $d = $rq_pr_data->where('id', $request_premium?->id)->get()->map(function ($item) {
            $item->customer_group = $item?->getLongTermRental?->customer->getCustomerGroupArray();
            $month = LongTermRentalMonth::where('lt_rental_id', $item?->getLongTermRental?->id)->pluck('month')->toArray();
            $month = implode(',', $month);
            $item->month = $month;
            $tor_files = $item?->getLongTermRental->getMedia('tor_file');
            $tor_files = get_medias_detail($tor_files);
            if (!empty($tor_files)) {
                $item->tor_file = $tor_files[0]['url'];
                $item->tor_file_name = $tor_files[0]['file_name'];
            }
            $item->premium = $item?->getLongTermRental?->getLongTermRentalMonth;
            return $item;
        });
        $premium_month = $d[0]->premium;
        $customer_group_list = CustomerGroup::all();
        return view('admin.request-premium.form', [
            'view' => true,
            'lt_id' => $lt_id,
            'page_title' => $page_title,
            'insurance_package' => $insurance_package,
            'insurance_companies' => $insurance_companies,
            'car_list' => $car_list,
            'd' => $d[0],
            'premium_month' => $premium_month,
            'customer_group_list' => $customer_group_list
        ]);
    }

    public function getAccessoryStatus($lt_l_id = null, $type = null)
    {
        if (!empty($lt_l_id)) {
            $AccessoryList = LongTermRentalLineAccessory::where('lt_rental_line_id', $lt_l_id)
                ->leftjoin('accessories', 'lt_rental_line_accessories.accessory_id', 'accessories.id')
                ->pluck('accessory_type')
                ->toArray();
            if (empty($AccessoryList)) {
                return false;
            } else {
                if (in_array($type, $AccessoryList)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function getAccessoryPriceTotal($lt_l_id = null)
    {
        if (!empty($lt_l_id)) {
            $AccessoryList = LongTermRentalLineAccessory::selectraw('IFNULL(sum(accessories.price*amount),0) as total_price')
                ->where('lt_rental_line_id', $lt_l_id)
                ->leftjoin('accessories', 'lt_rental_line_accessories.accessory_id', 'accessories.id')
                ->get()
                ->toArray();
            if (!empty($AccessoryList)) {
                return $AccessoryList[0]['total_price'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestPremium $request_premium)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RequestPremium);
        $lt_id = $request_premium?->getLongTermRental?->worksheet_no;
        $page_title = __('lang.edit') . __('request_premium.page_title');
        $insurance_package = InsurancePackage::getPackageList();
        $insurance_companies = InsuranceCompanies::getInsuranceCompaniesListAll();
        $rq_pr_data = $request_premium;

        $car_list = $rq_pr_data?->getLongTermRentalLine->map(function ($item) {
            $item->name = $item?->carClass?->name;
            $item->car_class_full_name = $item?->carClass?->full_name;
            $item->class_name = $item?->carClass?->full_name;
            $item->is_check_gps = $this->getAccessoryStatus($item->id, 'GPS');
            $item->is_check_cctv = $this->getAccessoryStatus($item->id, 'CCTV');
            $item->accessories_price = $this->getAccessoryPriceTotal($item->id);
            $item->created_date = (!empty($item->created_at)) ? date('d-m-Y', strtotime($item->created_at)) : date('d-m-Y');
            $item->car_premium = $item?->getRequestCarClassLine?->getRequestPremiumPrice;
            return $item;
        });
        $d = $rq_pr_data->where('id', $request_premium?->id)->get()->map(function ($item) {
            $item->customer_group = $item?->getLongTermRental?->customer->getCustomerGroupArray();
            $month = LongTermRentalMonth::where('lt_rental_id', $item?->getLongTermRental?->id)->pluck('month')->toArray();
            $month = implode(',', $month);
            $item->month = $month;
            $tor_files = $item?->getLongTermRental->getMedia('tor_file');
            $tor_files = get_medias_detail($tor_files);
            if (!empty($tor_files)) {
                $item->tor_file = $tor_files[0]['url'];
                $item->tor_file_name = $tor_files[0]['file_name'];
            }
            $item->premium = $item?->getLongTermRental?->getLongTermRentalMonth;
            return $item;
        });
        $premium_month = $d[0]->premium;
        $customer_group_list = CustomerGroup::all();
        return view('admin.request-premium.form', [
            'edit' => true,
            'lt_id' => $lt_id,
            'page_title' => $page_title,
            'insurance_package' => $insurance_package,
            'insurance_companies' => $insurance_companies,
            'car_list' => $car_list,
            'd' => $d[0],
            'premium_month' => $premium_month,
            'customer_group_list' => $customer_group_list
        ]);
    }

    public function getAccessoryList(Request $request)
    {
        $lt_rental_line_id = $request->lt_rental_line_id;
        if (!empty($lt_rental_line_id)) {
            $accessory_list = LongTermRentalLineAccessory::where('lt_rental_line_id', $lt_rental_line_id)
                ->get()->map(function ($item) {
                    $item->price = $item->accessory->price;
                    $item->name = $item->accessory->name;
                    return $item;
                });
            if (!empty($accessory_list)) {
                $response = [
                    'res_code' => 200,
                    'res_data' => $accessory_list
                ];
            } else {
                $response = [
                    'res_code' => 500,
                    'res_data' => [],
                ];
            }
        } else {
            $response = [
                'res_code' => 500,
                'res_data' => [],
            ];
        }

        return $response;
    }

    public function getCarList()
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
}
