<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\GpsServiceCharge;
use App\Models\GpsServiceChargeLine;

use App\Traits\GpsTrait;

class GpsServiceChargeController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSServiceCharge);
        $year_tab = GpsServiceCharge::select('year', 'id')->orderBy('year', 'asc')->paginate(PER_PAGE);
        $fiscal_month_list = GpsTrait::fiscalMonth();
        if (count($year_tab) > 0) {
            $year_id = GpsServiceCharge::select('id')->orderBy('year', 'asc')->first();
            return redirect()->route('admin.gps-service-charges.index-service', ['id' => $year_id]);
        } else {
            return view('admin.gps-service-charges.index', [
                'fiscal_month_list' => $fiscal_month_list,
                'year_tab' => $year_tab,
                'data_months' => [],
                'service_charge' => [],
            ]);
        }
    }

    public function indexService(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSServiceCharge);
        $year_tab = GpsServiceCharge::select('year', 'id')->orderBy('year', 'asc')->get();
        $fiscal_month_list = GpsTrait::fiscalMonth();
        $data_months = GpsServiceChargeLine::where('gps_service_charge_id', $request->id)->get();
        $service_charge = GpsServiceCharge::find($request->id);

        return view('admin.gps-service-charges.index', [
            'fiscal_month_list' => $fiscal_month_list,
            'year_tab' => $year_tab,
            'data_months' => $data_months,
            'service_charge' => $service_charge
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSServiceCharge);
        $d = new GpsServiceCharge();
        $fiscal_year_list = GpsTrait::getFiscalYearList();
        $fiscal_month_list = GpsTrait::fiscalMonth();

        $page_title = __('lang.create') . __('gps.page_title_data');
        return view('admin.gps-service-charges.form', [
            'd' =>  $d,
            'page_title' => $page_title,
            'fiscal_month_list' => $fiscal_month_list,
            'fiscal_year_list' => $fiscal_year_list,
        ]);
    }

    public function edit(GpsServiceCharge $gps_service_charge)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSServiceCharge);
        $data_months = GpsServiceChargeLine::where('gps_service_charge_id', $gps_service_charge->id)->get();

        $page_title = __('lang.edit') . __('gps.page_title_data');
        return view('admin.gps-service-charges.form-view', [
            'd' =>  $gps_service_charge,
            'page_title' => $page_title,
            'data_months' => $data_months,
        ]);
    }

    public function show(GpsServiceCharge $gps_service_charge)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSServiceCharge);
        $data_months = GpsServiceChargeLine::where('gps_service_charge_id', $gps_service_charge->id)->get();

        $page_title = __('lang.view') . __('gps.page_title_data');
        return view('admin.gps-service-charges.form-view', [
            'd' =>  $gps_service_charge,
            'page_title' => $page_title,
            'data_months' => $data_months,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => [
                'required',
                Rule::unique('gps_service_charges', 'year')->whereNull('deleted_at')->ignore($request->id),
            ],
        ], [], [
            'year' => __('gps.year'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $count_null = 0;
        $custom_rules = [];
        foreach ($request->data_month as $key => $item) {
            if (!empty($item['budget_' . $key])) {
                $count_null += 1;
            }
            if (!empty($item['air_time_gps_' . $key])) {
                $count_null += 1;
            }
            if (!empty($item['air_time_dvr_' . $key])) {
                $count_null += 1;
            }
            if (!empty($item['total_' . $key])) {
                $count_null += 1;
            }
            if (!empty($item['actual_' . $key])) {
                $count_null += 1;
            }
        }

        if (($count_null == 0)) {
            $custom_rules = [
                'service_list' => [
                    'required'
                ],
            ];
        }
        $validator = Validator::make($request->all(), $custom_rules, [], [
            'service_list' => __('gps.service_list'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $service_charge = GpsServiceCharge::firstOrNew(['id' => $request->id]);
        $service_charge->year = $request->year;
        $service_charge->save();

        GpsServiceChargeLine::where('gps_service_charge_id', $service_charge->id)->delete();
        $total_budget = 0;
        $total_air_time_gps = 0;
        $total_air_time_dvr = 0;
        $total_line = 0;
        $total_actual = 0;
        if ($request->data_month) {
            foreach ($request->data_month as $key => $item_data) {
                $service_charge_line = new GpsServiceChargeLine();
                $service_charge_line->gps_service_charge_id = $service_charge->id;
                $service_charge_line->month = $key;

                $budget = str_replace(',', '', $item_data['budget_' . $key]);
                $service_charge_line->budget = $budget ? $budget : 0.00;

                $air_time_gps = str_replace(',', '', $item_data['air_time_gps_' . $key]);
                $service_charge_line->air_time_gps = $air_time_gps ? $air_time_gps : 0.00;

                $air_time_dvr = str_replace(',', '', $item_data['air_time_dvr_' . $key]);
                $service_charge_line->air_time_dvr = $air_time_dvr ? $air_time_dvr : 0.00;

                $total = str_replace(',', '', $item_data['total_' . $key]);
                $service_charge_line->total = $total ? $total : 0.00;

                $actual = str_replace(',', '', $item_data['actual_' . $key]);
                $service_charge_line->actual = $actual ? $actual : 0.00;
                $service_charge_line->save();

                $total_budget += $service_charge_line->budget;
                $total_air_time_gps += $service_charge_line->air_time_gps;
                $total_air_time_dvr += $service_charge_line->air_time_dvr;
                $total_line += $service_charge_line->total;
                $total_actual += $service_charge_line->actual;
            }
        }
        $service_charge->total_budget = $total_budget;
        $service_charge->total_air_time_gps = $total_air_time_gps;
        $service_charge->total_air_time_dvr = $total_air_time_dvr;
        $service_charge->total = $total_line;
        $service_charge->total_actual = $total_actual;
        $service_charge->save();

        $redirect_route = route('admin.gps-service-charges.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
