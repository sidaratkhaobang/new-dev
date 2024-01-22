<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\InsuranceCompanies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\PhoneRule;

class InsuranceCompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->authorize(Actions::View . '_' . Resources::InsuranceCompanies);
        $s = (!empty($request->insurance_name)) ? $request->insurance_name : null;
        $insurance_list = InsuranceCompanies::getInsuranceCompaniesList();

        $insurance_data_list = InsuranceCompanies::getinsuranceCompaniesDataList($s);

        return view('admin.insurance.index', [
            'insurance_list' => $insurance_list,
            'list' => $insurance_data_list,
            's' => $s
        ]);
    }


//    Get InsuranceCompanies Name

    public function GetInsuranceNameClass()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => ('insurance-1'),
            ],
            [
                'id' => 2,
                'value' => 2,
                'name' => ('insurance-2'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCompanies);
        $d = new InsuranceCompanies();
        $list_status = $this->getStatusList();
        $page_title = __('lang.add') . __('insurances.page_title');
        return view('admin.insurance.form', [
            'list_status' => $list_status,
            'd' => $d,
            'page_title' => $page_title
        ]);
    }


    function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('lang.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('insurances.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCompanies);
        $rules = [
            'insurance_id' => [Rule::when(is_null($request->id), Rule::unique('insurers', 'code')->whereNull('deleted_at')), 'required'],
            'insurance_th' => ['required'],
            'insurance_email' => ['email', 'nullable'],
            'insurance_web' => ['url', 'nullable'],
            'insurance_phone' => ['nullable', 'min:9'],
            'coordinator_phone' => ['nullable', 'min:9'],
            'coordinator_email' => ['email', 'nullable'],
        ];
//        if ($request->update != 1) {
//            $rules['insurance_id'][] = Rule::unique('insurers', 'code')
//                ->whereNull('deleted_at');
//        }
        $validator = Validator::make($request->all(), $rules, [], [
            'insurance_th' => __('insurances.insurance_name'),
            'insurance_id' => __('insurances.insurance_id'),
            'insurance_email' => __('insurances.insurance_email'),
            'insurance_web' => __('insurances.website'),
            'insurance_phone' => __('insurances.insurance_phone'),
            'coordinator_phone' => __('insurances.insurance_phone'),
            'coordinator_email' => __('insurances.insurance_email'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $insurancecompanies_id = $request->id;
        $insurancecompanies = InsuranceCompanies::firstOrNew(['id' => $insurancecompanies_id]);
        $insurancecompanies->code = $request->insurance_id;
        $insurancecompanies->insurance_name_th = $request->insurance_th;
        $insurancecompanies->insurance_name_en = $request->insurance_en;
        $insurancecompanies->insurance_web = $request->website;
        $insurancecompanies->insurance_tel = $request->insurance_phone;
        $insurancecompanies->insurance_fax = $request->insurance_fax;
        $insurancecompanies->insurance_email = $request->insurance_email;
        $insurancecompanies->insurance_address = $request->address;
        $insurancecompanies->contact_name = $request->coordinator_name;
        $insurancecompanies->contact_tel = $request->coordinator_phone;
        $insurancecompanies->contact_email = $request->coordinator_email;
        $insurancecompanies->remark = $request->remark;
        $insurancecompanies->status = $request->status;
        $insurancecompanies->save();

        $redirect_route = route('admin.insurances-companies.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(InsuranceCompanies $insurances_company)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCompanies);
        $list_status = $this->getStatusList();
        $page_title = __('insurances.page_title');
        $view = true;
        return view('admin.insurance.form', [
            'd' => $insurances_company,
            'list_status' => $list_status,
            'page_title' => $page_title,
            'view' => $view
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InsuranceCompanies $insurances_company)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCompanies);
        $list_status = $this->getStatusList();
        $page_title = __('lang.edit') . __('insurances.page_title');
        $edit = true;
        return view('admin.insurance.form', [
            'd' => $insurances_company,
            'list_status' => $list_status,
            'page_title' => $page_title,
            'edit' => $edit,
        ]);
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
    public function destroy(InsuranceCompanies $insurances_company)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCompanies);
        $insurances_company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
