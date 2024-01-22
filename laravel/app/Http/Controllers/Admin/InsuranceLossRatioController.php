<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentClaimLines;
use App\Models\Car;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Models\Insurer;
use App\Models\VMI;
use App\Traits\AccidentTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsuranceLossRatioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceLossRatio);
        $page_title = __('insurance_loss_ratios.page_title');
        $listStatusJob = AccidentTrait::getStatusJobList();
        $searchLicensePlate = $request?->license_plate_chassis_no;
        $searchInsuranceCompany = $request?->insurance_company;
        $searchStatus = $request?->status;
        $searchPolicyReferenceName = $request?->policy_number;
        $searchCustomer = $request?->customer;
        $searchCustomerGroup = $request?->customer_group;
        $labelLicensePlate = $this->getSelectLabelLicensePlate($searchLicensePlate);
        $labelInsuranceCompany = $this->getSelectLabelInsuranceCompany($searchInsuranceCompany);
        $labelCustomer = $this->getSelectLabelCustomer($searchCustomer);
        $labelCustomerGroup = $this->getSelectLabelCustomerGroup($searchCustomerGroup);
        $totalCarTrueCase = $this->getDataTotalTrueClaimLine();
        $totalCarFalseCase = $this->getDataTotalFalseClaimLine();
        $totalAccidentTrueCar = $this->getTotalAccidentCar(MistakeTypeEnum::TRUE);
        $totalAccidentFalseCar = $this->getTotalAccidentCar(MistakeTypeEnum::FALSE);
        $totalCarAccident = Accident::distinct('car_id')->count();
        $totalLossFlase = $this->getTotalLoss(MistakeTypeEnum::FALSE);
        $totalLossTrue = $this->getTotalLoss(MistakeTypeEnum::TRUE);
        //        $totalDeductFlase = $this->getTotalDeduct($totalLossFlase,);
        $listAccident = Accident::where('wrong_type', MistakeTypeEnum::FALSE)
            ->with('vmiUnderPolicy')
            ->when(!empty($searchLicensePlate), function ($querySearch) use ($searchLicensePlate) {
                $querySearch->where('car_id', $searchLicensePlate);
            })
            ->when(!empty($searchStatus), function ($querySearch) use ($searchStatus) {
                $querySearch->where('status', $searchStatus)->latest();
            })
            ->when(!empty($searchInsuranceCompany) || !empty($searchPolicyReferenceName), function ($query) use ($searchPolicyReferenceName, $searchInsuranceCompany) {
                $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($searchPolicyReferenceName, $searchInsuranceCompany) {
                    if (!empty($searchPolicyReferenceName)) {
                        $querySearch->where('policy_reference_vmi', $searchPolicyReferenceName);
                    }
                    if (!empty($searchInsuranceCompany)) {
                        $querySearch->where('insurer_id', $searchInsuranceCompany);
                    }
                });
            })
            ->sortable('worksheet_no', 'car.license_plate', 'car.chassis_no', 'claim_no', 'accident_date')
            ->paginate(PER_PAGE)
            ->map(function ($item) use ($searchCustomer, $searchCustomerGroup, $searchInsuranceCompany, $searchPolicyReferenceName) {
                $filterCustomer = [];
                $dataCustomer = $this->getDataCustomer($item?->job_type, $item?->job_id);
                $dataCustomerGroup = explode(',', $dataCustomer['nameGroupCustomer']);
                $item->customer_group = $dataCustomer['nameGroupCustomer'];
                $item->license_plate = $item?->car?->license_plate;
                $item->chassis_no = $item?->car?->chassis_no;
                if (!empty($searchCustomer) && strcmp($searchCustomer, $item?->rental?->customer?->id)  !== 0) {
                    $filterCustomer[] = false;
                }
                if (!empty($searchCustomerGroup) && !in_array($searchCustomerGroup, $dataCustomerGroup)) {
                    $filterCustomer[] = false;
                }
                if (!in_array(false, $filterCustomer)) {
                    return $item;
                }
            })->filter();
        return view('admin.insurance-loss-ratios.index', [
            'page_title' => $page_title,
            'listStatusJob' => $listStatusJob,
            'listAccident' => $listAccident,
            'totalCarTrueCase' => $totalCarTrueCase,
            'totalCarFalseCase' => $totalCarFalseCase,
            'totalAccidentTrueCar' => $totalAccidentTrueCar,
            'totalAccidentFalseCar' => $totalAccidentFalseCar,
            'totalCarAccident' => $totalCarAccident,
            'searchLicensePlate' => $searchLicensePlate,
            'searchInsuranceCompany' => $searchInsuranceCompany,
            'searchStatus' => $searchStatus,
            'searchCustomer' => $searchCustomer,
            'searchCustomerGroup' => $searchCustomerGroup,
            'searchPolicyReferenceName' => $searchPolicyReferenceName,
            'labelLicensePlate' => $labelLicensePlate,
            'labelInsuranceCompany' => $labelInsuranceCompany,
            'labelCustomer' => $labelCustomer,
            'labelCustomerGroup' => $labelCustomerGroup,
            'totalLossFlase' => $totalLossFlase,
            'totalLossTrue' => $totalLossTrue,
        ]);
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

    public function getSelectLabelCustomer($customerId)
    {
        $nameCustomer = null;
        if (!empty($customerId)) {
            $nameCustomer = Customer::where('id', $customerId)->first()?->name;
        }

        return $nameCustomer;
    }

    public function getSelectLabelCustomerGroup($customerGroupId)
    {
        $nameCustomerGroup = null;
        if (!empty($customerGroupId)) {
            $nameCustomerGroup = CustomerGroup::where('id', $customerGroupId)->first()?->name;
        }
        return $nameCustomerGroup;
    }

    public function getDataTotalTrueClaimLine($insurerId = null)
    {
        $totalClaimLine = null;
        $dataAccident = Accident::where('wrong_type', MistakeTypeEnum::TRUE)
            ->when(!empty($insurerId), function ($query) use ($insurerId) {
                $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($insurerId) {
                    $querySearch->where('insurer_id', $insurerId);
                });
            })
            ->pluck('id')
            ->toArray();
        $totalClaimLine = AccidentClaimLines::wherein('accident_id', $dataAccident)->count();
        return $totalClaimLine;
    }

    public function getDataTotalFalseClaimLine($insurerId = null)
    {
        $totalClaimLine = null;
        $dataAccident = Accident::where('wrong_type', MistakeTypeEnum::FALSE)
            ->when(!empty($insurerId), function ($query) use ($insurerId) {
                $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($insurerId) {
                    $querySearch->where('insurer_id', $insurerId);
                });
            })
            ->pluck('id')
            ->toArray();
        $totalClaimLine = AccidentClaimLines::wherein('accident_id', $dataAccident)->count();
        return $totalClaimLine;
    }

    public function getTotalAccidentCar($type, $insurerId = null)
    {
        $totalAccidentCar = 0;
        if ($type == MistakeTypeEnum::FALSE) {
            $totalAccidentCar = Accident::where('wrong_type', MistakeTypeEnum::FALSE)
                ->when(!empty($insurerId), function ($query) use ($insurerId) {
                    $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($insurerId) {
                        $querySearch->where('insurer_id', $insurerId);
                    });
                })
                ->distinct('car_id')
                ->count();
        }
        if ($type == MistakeTypeEnum::TRUE) {
            $totalAccidentCar = Accident::where('wrong_type', MistakeTypeEnum::TRUE)
                ->distinct('car_id')
                ->count();
        }
        return $totalAccidentCar;
    }

    public function getTotalLoss($type, $insurerId = null)
    {
        $totalLoss = null;
        $totalLoss = Accident::select(DB::raw('sum(aro.wage)+sum(aro.spare_parts)-sum(aro.discount_spare_parts) as total_loss'))
            ->leftJoin('accident_repair_orders as aro', 'aro.accident_id', 'accidents.id')
            ->when(!empty($insurerId), function ($query) use ($insurerId) {
                $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($insurerId) {
                    $querySearch->where('insurer_id', $insurerId);
                });
            })
            ->where('accidents.wrong_type', $type)
            ->get();
        $totalSlide = Accident::select(DB::raw('sum(slide_price) as total_slide'))
            ->leftjoin('accident_slides', 'accident_slides.accident_id', 'accidents.id')
            ->when(!empty($insurerId), function ($query) use ($insurerId) {
                $query->whereHas('vmiUnderPolicy', function ($querySearch) use ($insurerId) {
                    $querySearch->where('insurer_id', $insurerId);
                });
            })
            ->where('accidents.wrong_type', $type)
            ->get();
        $totalLoss = $totalLoss[0]?->total_loss ?: 0;
        $totalSlide = $totalSlide[0]->total_slide ?: 0;
        $total = $totalLoss + $totalSlide;
        $total = $this->getTotalDeduct($total, $type);

        return $total;
    }

    public function getTotalDeduct($totalLossFlase, $type)
    {
        $totalDeduct = 0;
        if (!empty($totalLossFlase)) {
            $totalSum = Accident::select(DB::raw('sum(total_damages) as total_deduct'), DB::raw('sum(carcass_cost) as total_lose'))
                ->where('wrong_type', $type)
                ->get();
            $totalDeduct = $totalSum[0]->total_deduct ?: 0;
            $totalLose = $totalSum[0]->total_lose ?: 0;
            $totalDeduct = $totalDeduct + $totalLose;
            $totalDeduct = $totalLossFlase - $totalDeduct;
        }
        return $totalDeduct;
    }

    public function getDataCustomer($typeModel, $jobId)
    {
        $nameCustomer = null;
        $nameGroupCustomer = null;
        $idCustomer = null;
        if (!empty($typeModel) && !empty($jobId)) {
            $Model = $typeModel::where('id', $jobId)->first();
            if (!empty($Model)) {
                $nameCustomer = $Model?->customer?->name;
                $idCustomer = $Model?->customer?->id;
                $nameGroupCustomer = $this->getNameGroupCustomer($Model?->customer_id);
                if (!empty($nameGroupCustomer)) {
                    $nameGroupCustomer = implode(',', $nameGroupCustomer);
                }
            }
        }

        $dataCustomer = [
            'idCustomer' => $idCustomer,
            //            'idCustomerGroup' => ,
            'nameCustomer' => $nameCustomer,
            'nameGroupCustomer' => $nameGroupCustomer,
        ];
        return $dataCustomer;
    }

    public function getNameGroupCustomer($customerId)
    {
        $nameGroupCustomer = null;
        if (!empty($customerId)) {
            $nameGroupCustomer = CustomerGroupRelation::join('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
                ->select('customer_groups.id as id', 'customer_groups.name as name')
                ->where('customers_groups_relation.customer_id', $customerId)
                ->where('customer_groups.status', STATUS_ACTIVE)
                ->pluck('customer_groups.name')
                ->toArray();
        }
        return $nameGroupCustomer;
    }

    public function generatePdf(Request $request)
    {
        $searchInsuranceCompany = $request?->insurance_company;
        $dataInsurance = Accident::select('insurer_id')
            ->join('voluntary_motor_insurances as vmi', 'vmi.car_id', 'accidents.car_id')
            ->where('vmi.status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
            ->distinct('insurer_id')
            ->get();
        $dataTable = [];
        foreach ($dataInsurance as $keyInsurance => $valueInsurance) {
            $insurerId = $valueInsurance?->insurer_id;
            $nameInsurance = Insurer::where('id', $insurerId)->first()?->insurance_name_th;
            $totalInsuranceCar = Accident::whereHas('vmiUnderPolicy', function ($query) use ($insurerId) {
                $query->where('insurer_id', $insurerId);
            })->distinct('car_id')->count();
            $totalLossFlase = $this->getTotalLoss(MistakeTypeEnum::FALSE, $insurerId);
            $totalLossTrue = $this->getTotalLoss(MistakeTypeEnum::TRUE, $insurerId);
            $totalCarTrueCase = $this->getDataTotalTrueClaimLine($insurerId);
            $totalCarFalseCase = $this->getDataTotalFalseClaimLine($insurerId);
            $dataTable[] = [
                'name_insurance' => $nameInsurance,
                'total_insurance_car' => $totalInsuranceCar,
                'total_false_car' => $this->getTotalAccidentCar(MistakeTypeEnum::FALSE, $insurerId),
                'total_true_car' => $this->getTotalAccidentCar(MistakeTypeEnum::TRUE, $insurerId),
                'total_false_loss' => $totalLossFlase,
                'total_true_loss' => $totalLossTrue,
                'total_claim_false' => $totalCarFalseCase,
                'total_claim_true' => $totalCarTrueCase,
            ];
        }

        $page_title = 'Loss Ratio ของแต่ละบริษัท';
        $pdf = PDF::loadView(
            'admin.insurance-loss-ratios.insurance-loss-ratios-pdf.pdf',
            [
                'page_title' => $page_title,
                'dataTable' => $dataTable,
            ]
        );
        return $pdf->stream();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Accident $insurance_loss_ratio)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceLossRatio);
        $mode = MODE_VIEW;
        $page_title = __('insurance_deduct.accident_worksheet');
        $dataInsurance = $this->getDataInsurance($insurance_loss_ratio?->car_id);
        $dataCustomer = $this->getDataCustomer($insurance_loss_ratio?->job_type, $insurance_loss_ratio?->job_id);
        return view('admin.insurance-loss-ratios.form', [
            'view' => true,
            'mode' => $mode,
            'page_title' => $page_title,
            'd' => $insurance_loss_ratio,
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
    public function edit(Accident $insurance_loss_ratio)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceLossRatio);
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
