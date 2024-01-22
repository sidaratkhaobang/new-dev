<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Car;
use App\Models\CustomerGroupRelation;
use App\Models\Insurer;
use App\Models\VMI;
use App\Traits\AccidentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InsuranceDeductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceDeduct);
        $searchLicensePlate = $request?->license_plate_chassis_no;
        $searchInsuranceCompany = $request?->insurance_company;
        $searchStatus = $request?->status;
        $searchPolicyReferenceName = $request?->policy_number;
        $labelLicensePlate = $this->getSelectLabelLicensePlate($searchLicensePlate);
        $labelInsuranceCompany = $this->getSelectLabelInsuranceCompany($searchInsuranceCompany);
        $listStatusJob = AccidentTrait::getStatusJobList();
        $page_title = __('insurance_deduct.page_title');
        $listAccident = Accident::where('wrong_type', MistakeTypeEnum::FALSE)
            ->with('car.vmi', function ($queryData) {
                $queryData->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
                    ->orderBy('created_at', 'DESC')
                    ->latest();
            })
            ->when(!empty($searchPolicyNumber), function ($querySearch) use ($searchLicensePlate) {
                $querySearch->where('car_id', $searchLicensePlate);
            })
            ->when(!empty($searchStatus), function ($querySearch) use ($searchStatus) {
                $querySearch->where('status', $searchStatus);
            })
            ->when(!empty($searchInsuranceCompany), function ($query) use ($searchInsuranceCompany) {
                $query->whereHas('car.vmi', function ($querySearch) use ($searchInsuranceCompany) {
                    $querySearch->where('insurer_id', $searchInsuranceCompany)
                        ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
                        ->orderBy('created_at', 'DESC')
                        ->take(1);
                });
            })->sortable('worksheet_no', 'car.license_plate', 'car.chassis_no', 'claim_no', 'accident_date')
            ->paginate(PER_PAGE)
            ->map(function ($item) use ($searchInsuranceCompany, $searchPolicyReferenceName) {
                //                $dataPolicyReferenceName = $this->getVmiPolicyReferenceName($item?->car_id, $searchPolicyReferenceName);
                //                $item->insurance_policy_reference_name = $dataPolicyReferenceName['namePolicyReference'];
                //                $dataInsuranceCompany = $this->getVmiInsuranceCompanyName($item?->car_id, $searchInsuranceCompany);
                //                $item->insurance_name = $dataInsuranceCompany['nameInsuranceCompany'];
                $dataCustomer = $this->getDataCustomer($item?->job_type, $item?->job_id);
                $item->customer_name = $dataCustomer['nameCustomer'];
                $item->customer_group = $dataCustomer['nameGroupCustomer'];
                $item->license_plate = $item?->car?->license_plate;
                $item->chassis_no = $item?->car?->chassis_no;
                //                if ($dataInsuranceCompany['searchInsuranceCompanyStatus'] == true && $dataPolicyReferenceName['searchPolicyReferenceNameStatus'] == true) {
                //                    return $item;
                //                }
                return $item;
            })->filter();
        return view(
            'admin.insurance-deducts.index',
            [
                'page_title' => $page_title,
                'listStatusJob' => $listStatusJob,
                'listAccident' => $listAccident,
                'searchLicensePlate' => $searchLicensePlate,
                'searchInsuranceCompany' => $searchInsuranceCompany,
                'searchStatus' => $searchStatus,
                'searchPolicyReferenceName' => $searchPolicyReferenceName,
                'labelLicensePlate' => $labelLicensePlate,
                'labelInsuranceCompany' => $labelInsuranceCompany,
            ]
        );
    }

    public function getDataCustomer($typeModel, $jobId)
    {
        $nameCustomer = null;
        $nameGroupCustomer = null;
        if (!empty($typeModel)) {
            $Model = $typeModel::where('id', $jobId)->first();
            $nameCustomer = $Model?->customer?->name;
            $nameGroupCustomer = $this->getNameGroupCustomer($Model?->customer_id);
            if (!empty($nameGroupCustomer)) {
                $nameGroupCustomer = implode(',', $nameGroupCustomer);
            }
        }
        $dataCustomer = [
            'nameCustomer' => $nameCustomer,
            'nameGroupCustomer' => $nameGroupCustomer,
        ];
        return $dataCustomer;
    }

    public function getNameGroupCustomer($customerId)
    {
        $nameGroupCustomer = null;
        if (!empty($customerId)) {
            $nameGroupCustomer =  CustomerGroupRelation::join('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
                ->select('customer_groups.id as id', 'customer_groups.name as name')
                ->where('customers_groups_relation.customer_id', $customerId)
                ->where('customer_groups.status', STATUS_ACTIVE)
                ->pluck('customer_groups.name')
                ->toArray();
        }
        return $nameGroupCustomer;
    }
    public function getSelectLabelLicensePlate($carId)
    {
        $text = null;
        if (!empty($carId)) {
            $dataCar = Car::where('id', $carId)->first();
            if ($dataCar?->car?->license_plate) {
                $text = $dataCar?->license_plate;
            } else if ($dataCar?->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $dataCar?->engine_no;
            } else if ($dataCar?->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $dataCar?->chassis_no;
            }
        }

        return $text;
    }

    public function getSelectLabelInsuranceCompany($insuranceId)
    {
        $text = null;
        if (!empty($insuranceId)) {
            $dataInsurance = Insurer::where('id', $insuranceId)->first();
            if (!empty($dataInsurance->insurance_name_th)) {
                $text = $dataInsurance->insurance_name_th;
            } else {
                $text = $dataInsurance->insurance_name_en;
            }
        }
        return $text;
    }

    public function getVmiPolicyReferenceName($carId, $searchPolicyReferenceName)
    {
        $searchPolicyReferenceNameStatus = true;
        $namePolicyReference = '-';
        $dataVmi = VMI::where('car_id', $carId)
            ->orderBy('created_at', 'Desc')
            ->first();
        if (!empty($dataVmi)) {
            if (!empty($dataVmi?->policy_reference_vmi)) {
                $namePolicyReference = $dataVmi?->policy_reference_vmi;
            }
            if (!empty($searchPolicyReferenceName) && $searchPolicyReferenceName != $namePolicyReference) {
                $searchPolicyReferenceNameStatus = false;
            }
        }
        return [
            'namePolicyReference' => $namePolicyReference,
            'searchPolicyReferenceNameStatus' => $searchPolicyReferenceNameStatus,
        ];
    }

    public function getVmiInsuranceCompanyName($carId, $searchInsuranceCompany)
    {
        $searchInsuranceCompanyStatus = true;
        $nameInsuranceCompany = '-';
        $dataVmi = VMI::where('car_id', $carId)
            ->orderBy('created_at', 'Desc')
            ->first();
        if (!empty($dataVmi)) {
            if (!empty($dataVmi?->insurer?->insurance_name_th)) {
                $nameInsuranceCompany = $dataVmi?->insurer?->insurance_name_th;
            } else if (!empty($dataVmi?->insurer?->insurance_name_en)) {
                $nameInsuranceCompany = $dataVmi?->insurer?->insurance_name_en;
            }
        }
        if (!empty($searchInsuranceCompany) && $dataVmi->insurer->id != $searchInsuranceCompany) {
            $searchInsuranceCompanyStatus = false;
        }
        return [
            'searchInsuranceCompanyStatus' => $searchInsuranceCompanyStatus,
            'nameInsuranceCompany' => $nameInsuranceCompany,
        ];
    }

    public function getWrongTypeName($wrongType)
    {
        $wrongTypeName = '-';
        if (!empty($wrongType)) {
            if ($wrongType == MistakeTypeEnum::FALSE) {
                $wrongTypeName = 'ฝ่ายผิด';
            } else
                if ($wrongType == MistakeTypeEnum::TRUE) {
                $wrongTypeName = 'ถูก';
            } else if ($wrongType == MistakeTypeEnum::BOTH) {
                $wrongTypeName = 'ผิดทั้งสองฝ่าย';
            }
        }
        return $wrongTypeName;
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
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceDeduct);
        $validator = Validator::make($request->all(), [
            'deduct_date' => [
                'date', 'nullable'
            ],
            'deduct_doc_dd' => [
                'string', 'max:255', 'nullable'
            ],
            'deduct_all_damage' => [
                'nullable'
            ],
            'deduct_deductible' => [
                'string', 'max:255', 'nullable'
            ]
        ], [], [
            'deduct_date' => 'DD',
            'deduct_doc_dd' => 'Doc DD',
            'deduct_all_damage' => 'OD (ค่าเสียหายทั้งหมด)',
            'deduct_deductible' => 'Deductible',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $dataDeductDate = $request?->deduct_date;
        $dataDeductDocument = $request?->deduct_doc_dd;
        $dataTotalDamage = $request?->deduct_all_damage ? str_replace(',', '', $request?->deduct_all_damage) : null;
        $dataDeDuctible = $request?->deduct_deductible;
        $dataAccidentId = $request?->accident_id;
        $modelAccident = Accident::findOrFail($dataAccidentId);
        $modelAccident->deductible = $dataDeDuctible;
        $modelAccident->date_deductible = $dataDeductDate;
        $modelAccident->doc_deductible = $dataDeductDocument;
        $modelAccident->total_damages = $dataTotalDamage;
        $modelAccident->save();
        $redirectRoute = route('admin.insurance-deducts.index');
        return $this->responseValidateSuccess($redirectRoute);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Accident $insurance_deduct)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceDeduct);
        $mode = MODE_VIEW;
        $page_title = __('insurance_deduct.accident_worksheet');
        $dataInsurance = $this->getDataInsurance($insurance_deduct?->car_id);
        $dataCustomer = $this->getDataCustomer($insurance_deduct?->job_type, $insurance_deduct?->job_id);
        return view('admin.insurance-deducts.form', [
            'view' => true,
            'mode' => $mode,
            'page_title' => $page_title,
            'd' => $insurance_deduct,
            'dataInsurance' => $dataInsurance,
            'dataCustomer' => $dataCustomer,
        ]);
    }

    public function getDataInsurance($carId)
    {
        $dataVmi = [];
        if (!empty($carId)) {
            $dataVmi = VMI::where('car_id', $carId)
                ->orderBy('created_at', 'Desc')
                ->first();
        }

        return $dataVmi;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Accident $insurance_deduct)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceDeduct);
        $mode = MODE_UPDATE;
        $page_title = __('lang.edit') . __('insurance_deduct.accident_worksheet');
        $dataInsurance = $this->getDataInsurance($insurance_deduct?->car_id);
        $dataCustomer = $this->getDataCustomer($insurance_deduct?->job_type, $insurance_deduct?->job_id);
        return view('admin.insurance-deducts.form', [
            'mode' => $mode,
            'page_title' => $page_title,
            'd' => $insurance_deduct,
            'dataInsurance' => $dataInsurance,
            'dataCustomer' => $dataCustomer,
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
    public function destroy($id)
    {
        //
    }
}
