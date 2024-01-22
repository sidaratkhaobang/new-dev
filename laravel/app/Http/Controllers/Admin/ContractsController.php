<?php

namespace App\Http\Controllers\Admin;

use App\Classes\ProductManagement;
use App\Enums\Actions;
use App\Enums\CalculateTypeEnum;
use App\Enums\ConditionGroupEnum;
use App\Enums\ContractEnum;
use App\Enums\ContractSignerSideEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ConditionGroup;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use App\Models\ContractForm;
use App\Models\ContractFormCheckList;
use App\Models\ContractLines;
use App\Models\ContractLogs;
use App\Models\Contracts;
use App\Models\ContractSigners;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\InspectionJob;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\VMI;
use App\Traits\ContractTrait;
use App\Traits\CustomerTrait;
use App\Traits\RentalTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContractsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractAllList);

        $worksheet_no = $request->worksheet_no;
        $contract_type = $request->contract_type;
        $car_id = $request->car_id;
        $customer_id = $request->customer_id;
        $branch_id = $request->branch_id;
        $contract_start_date = $request->contract_start_date;
        $contract_end_date = $request->contract_end_date;
        $status = $request->status;

        $statusRequestList = ContractTrait::getListStatusRequest();
        $statusList = ContractTrait::getListStatus();

        $lists = Contracts::withCount('contractline')
            ->whereHas('contractline', function ($query) use ($request, $contract_start_date, $contract_end_date) {
                if (!empty($request->car_id)) {
                    $query->where('car_id', $request->car_id);
                }

                if (!empty($contract_start_date)) {
                    $query->whereDate('pick_up_date', $contract_start_date);
                }

                if (!empty($contract_end_date)) {
                    $query->whereDate('return_date', $contract_end_date);
                }
            })
            ->whereHas('customer', function ($query) use ($branch_id) {
                if (!empty($branch_id)) {
                    $query->where('branch_id', $branch_id);
                }
            })
            //            ->whereHasMorph(
            //                'job',
            //                [Rental::class, LongTermRental::class],
            //                function (Builder $query, string $type) use ($branch_id, $customer_id) {
            //                    if (!empty($branch_id)) {
            //                        if (strcmp($type, Rental::class) === 0) {
            //                            $query->whereHas('customer', function ($subquery) use ($branch_id) {
            //                                $subquery->where('branch_id', $branch_id);
            //                            });
            //                        }
            //                        if (strcmp($type, LongTermRental::class) === 0) {
            //                            $query->whereHas('customer', function ($subquery) use ($branch_id) {
            //                                $subquery->where('branch_id', $branch_id);
            //                            });
            //                        }
            //                    }

            //                    if (!empty($customer_id)) {
            //                        if (strcmp($type, Rental::class) === 0) {
            //                            $query->where('customer_id', $customer_id);
            //                            $query->orWhere(function ($sub) use ($customer_id) {
            //                                $sub->whereIn(
            //                                    'contracts.id',
            //                                    ContractLogs::where('type_log', ContractEnum::REQUEST_TRANSFER_CONTRACT)
            //                                        ->where('status', ContractEnum::CONFIRM)
            //                                        ->where('old_value', $customer_id)
            //                                        ->pluck('contract_logs.contract_id')
            //                                        ->unique()
            //                                        ->toArray()
            //                                );
            //                            });
            //                        }
            //                        if (strcmp($type, LongTermRental::class) === 0) {
            //                            $query->where('customer_id', $customer_id);
            //                        }
            //                    }
            //                }
            //            )
            ->search($request)
            ->sortable(['created_at' => 'desc'])
            ->paginate(PER_PAGE);

        //        foreach ($lists as $item) {
        //            $customer = $item->job->customer;
        //            if ($item->status == ContractEnum::CANCEL_CONTRACT) {
        //                $contract_log = ContractLogs::where('contract_id', $item->id)->orderBy('id', 'DESC')->first();
        //                if (isset($contract_log) && $contract_log->type_log == ContractEnum::REQUEST_TRANSFER_CONTRACT && $contract_log->status == ContractEnum::CONFIRM) {
        //                    $customer = Customer::find($contract_log->old_value);
        //                }
        //            }
        //
        //            $item->customer_name = $customer->name;
        //        }

        $branch_list = Branch::get([
            'id',
            'name',
        ]);

        $worksheet_no_list = Contracts::select(['worksheet_no as name', 'id'])->get();
        $contract_type_list = ContractTrait::getListContrcatType();

        $customer_id_list = collect();
        $car_id_list = collect();
        Contracts::get()->map(function ($row, $key) use ($customer_id_list, $car_id_list) {
            $tempCustomer = [
                'id' => $row->customer->id,
                'name' => $row->customer->name,
            ];

            if (!$customer_id_list->where('id', $row->customer->id)->first()) {
                $customer_id_list->push((object)$tempCustomer);
            }

            foreach ($row->contractline as $line) {
                $tempCar = [
                    'id' => $line->car->id,
                    'name' => $line->car->engine_no,
                ];
                if (!$car_id_list->where('id', $line->car->id)->first()) {
                    $car_id_list->push((object)$tempCar);
                }
            }
        });

        return view('admin.contracts.index', [
            'lists' => $lists,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_list' => $worksheet_no_list,
            'contract_type' => $contract_type,
            'contract_type_list' => $contract_type_list,
            'car_id' => $car_id,
            'car_id_list' => $car_id_list,
            'customer_id' => $customer_id,
            'customer_id_list' => $customer_id_list,
            'branch_id' => $branch_id,
            'branch_list' => $branch_list,
            'contract_start_date' => $contract_start_date,
            'contract_end_date' => $contract_end_date,
            'status' => $status,
            'statusList' => $statusList,
            'statusRequestList' => $statusRequestList,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractAllList);
    }

    public function saveContractQuotation(Request $request, Contracts $contract)
    {
        if (!empty($request->contract_quotation)) {
            foreach ($request->contract_quotation as $data) {
                $contract_form = ContractForm::firstOrNew([
                    'id' => $data['id'],
                ]);
                $contract_form->name = $data['name'];
                $contract_form->seq = $data['seq'];
                $contract_form->contract_id = $contract->id;

                if ($data['status'] == 'false') {
                    $contract_form->status = STATUS_DEFAULT;
                } else {
                    $contract_form->status = $data['status'] ? STATUS_ACTIVE : STATUS_DEFAULT;
                }
                $contract_form->save();

                if (!empty($data['contract_form_check_lists'])) {
                    foreach ($data['contract_form_check_lists'] as $list) {
                        $contract_form_list = ContractFormCheckList::firstOrNew([
                            'id' => $list['id'],
                        ]);
                        $contract_form_list->name = $list['name'];
                        $contract_form_list->seq = $list['seq'];
                        $contract_form_list->contract_form_id = $contract_form->id;
                        if ($list['status'] == 'false') {
                            $contract_form_list->status = STATUS_DEFAULT;
                        } else {
                            $contract_form_list->status = $list['status'] ? STATUS_ACTIVE : STATUS_DEFAULT;
                        }
                        $contract_form_list->save();
                    }
                }
            }
        }

        if ($request->has('del_section')) {
            foreach ($request->del_section as $id) {
                ContractForm::find($id)->delete();
            }
        }

        if ($request->has('del_checklist')) {
            foreach ($request->del_checklist as $id) {
                ContractFormCheckList::find($id)->delete();
            }
        }

        $redirect_route = route('admin.contract-category.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractAllList);
        $contract = Contracts::find($request->contract_id);

        if ($contract->status == ContractEnum::CANCEL_CONTRACT || $contract->status == ContractEnum::CLOSE_CONTRACT) {
            return $this->responseValidateSuccess(route('admin.contracts.show', ['contract' => $contract]));
        }

        if (isset($contract->contract_type)) {
            $validator = Validator::make($request->all(), [
                'contract_quotation.*.seq' => [
                    'required',
                    'integer',
                    'distinct',
                ],
                'contract_quotation.*.name' => [
                    'required',
                    'string',
                ],
                'contract_quotation.*.contract_form_check_lists.*.seq' => [
                    'required',
                    'integer',
                ],
                'contract_quotation.*.contract_form_check_lists.*.name' => [
                    'required',
                    'string',
                ],
            ], [], [
                'contract_quotation.*.seq' => __('contract_category.form.table.seq'),
                'contract_quotation.*.name' => __('contract_category.form.table.name'),
                'contract_quotation.*.contract_form_check_lists.*.seq' => __('contract_category.form.table.sub.seq'),
                'contract_quotation.*.contract_form_check_lists.*.name' => __('contract_category.form.table.sub.name'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            foreach ($request->contract_quotation as $data) {
                if (!empty($data['contract_form_check_lists'])) {
                    $temp = array_unique(array_column($data['contract_form_check_lists'], 'seq'));
                    if (count($data['contract_form_check_lists']) != count($temp)) {
                        return $this->responseWithCode(false, __('contract_category.form.validate.seq', ['name' => $data['name']]), null, 422);
                    }
                }
            }
        }

        $this->saveContractQuotation($request, $contract);

        $contract->worksheet_no_customer = $request->worksheet_no_customer;
        $contract->date_document = $request->date_document;
        $contract->remark = $request->remark;
        $contract->start_rent = $request->start_rent;
        $contract->end_rent = $request->end_rent;

        if (isset($request->contract_type) and $contract->status == ContractEnum::REQUEST_CONTRACT) {
            $condition_qoutation_group = ConditionGroup::find($request->contract_type);
            if ($condition_qoutation_group) {
                $contract->contract_type = $condition_qoutation_group->name;
                $contract->status = ContractEnum::ACTIVE_CONTRACT;

                $condition_qoutation = ConditionQuotation::where('condition_group_id', $condition_qoutation_group->id)->get();

                foreach ($condition_qoutation as $condition) {
                    $contract_form = new ContractForm();
                    $contract_form->contract_id = $contract->id;
                    $contract_form->name = $condition->name;
                    $contract_form->seq = $condition->seq;
                    $contract_form->save();

                    $condition_qoutation_check_list = ConditionQuotationChecklist::where('condition_quotations_id', $condition->id)->get();
                    foreach ($condition_qoutation_check_list as $list) {
                        $contract_form_list = new ContractFormCheckList();
                        $contract_form_list->contract_form_id = $contract_form->id;
                        $contract_form_list->name = $list->name;
                        $contract_form_list->seq = $list->seq;
                        $contract_form_list->save();
                    }
                }
            }
        } elseif ($contract->status == ContractEnum::ACTIVE_CONTRACT) {
            if ($request->date_offer_sign) {
                $contract->date_offer_sign = $request->date_offer_sign;
                $contract->status = ContractEnum::SEND_OFFER_SIGN;
            }
        } elseif ($contract->status == ContractEnum::SEND_OFFER_SIGN) {
            if ($request->date_send_contract) {
                $contract->date_send_contract = $request->date_send_contract;
                $contract->status = ContractEnum::SEND_CUSTOMER_SIGN;
            }
        } elseif ($contract->status == ContractEnum::SEND_CUSTOMER_SIGN) {
            if ($request->date_return_contract) {
                $contract_attach_file = $contract->getMedia('contract_attach_file');
                if (!$request->hasFile('contract_attach_file') && sizeof($contract_attach_file) == 0) {
                    return $this->responseWithCode(false, 'กรุณาแนบไฟล์สัญญา (Final Version)', null, 422);
                }
                if ($request->hasFile('contract_attach_file')) {
                    foreach ($request->file('contract_attach_file') as $image) {
                        if ($image->isValid()) {
                            $contract->addMedia($image)->toMediaCollection('contract_attach_file');
                        }
                    }
                }
                $contract->date_return_contract = $request->date_return_contract;
                $contract->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
            }
        }

        $contract->save();
        $this->updateStatusContractLine($contract);

        if ($request->delete_media_file_ids) {
            $delete_media_file_ids = $request->delete_media_file_ids;
            if ((is_array($delete_media_file_ids)) && (sizeof($delete_media_file_ids) > 0)) {
                foreach ($delete_media_file_ids as $media_id) {
                    $contract->deleteMedia($media_id);
                }
            }
        }

        if ($request->contract_file) {
            foreach ($request->contract_file as $item) {
                if ($item['file']->isValid()) {
                    $contract->addMedia($item['file'])
                        ->usingName($item['file_name'])
                        ->toMediaCollection('contract_file');
                }
            }
        }

        if ($request->contract_attach_file__pending_delete_ids) {
            $pending_delete_ids = $request->contract_attach_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $contract->deleteMedia($media_id);
                }
            }
        }

        if ($request->contract_signers) {
            foreach ($request->contract_signers as $key => $item) {
                $contract_signers = new ContractSigners();
                $contract_signers->contract_id = $contract->id;
                $contract_signers->seq = $key + 1;
                $contract_signers->name = $item['sign_name'];
                $contract_signers->signer_type = $item['sign_type'];
                $contract_signers->contract_side = $item['contract_side'];
                $contract_signers->is_attorney = filter_var($item['is_attorney'], FILTER_VALIDATE_BOOLEAN);
                $contract_signers->save();

                if (isset($item['files'])) {
                    foreach ($item['files'] as $file) {
                        if ($file->isValid()) {
                            $contract_signers->addMedia($file)
                                ->toMediaCollection('contract_signer_file');
                        }
                    }
                }
            }
        }

        if ($request->delete_contract_signers_ids) {
            $pending_delete_ids = $request->delete_contract_signers_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $id) {
                    $signer = ContractSigners::find($id);
                    $signer->delete();
                }
            }
        }

        //update contract lines
        if ($contract->status == ContractEnum::ACTIVE_BETWEEN_CONTRACT) {
            if (isset($request->contract_cars) && sizeof($request->contract_cars) > 0) {
                $contract_cars = $request->contract_cars;
                foreach ($contract_cars as $line_id => $item) {
                    $contract_line = ContractLines::find($line_id);
                    $contract_line->return_date = $item['return_date'] ?? null;
                    $contract_line->is_fine = $item['is_fine'] ?? null;
                    $contract_line->percent_fine = isset($item['percent_fine']) ? str_replace(',', '', $item['percent_fine']) : null;
                    $contract_line->fine = isset($item['fine']) ? str_replace(',', '', $item['fine']) : null;
                    $contract_line->save();
                }
            }
        }


        // if ($request->hasFile('contract_attach_file')) {
        //     foreach ($request->file('contract_attach_file') as $image) {
        //         if ($image->isValid()) {
        //             $contract->addMedia($image)->toMediaCollection('contract_attach_file');
        //             $contract->status = ContractEnum::CLOSE_CONTRACT;
        //             $contract->save();
        //         }
        //     }
        // }


        if ($request->hasFile('contract_attach_file')) {
            foreach ($request->file('contract_attach_file') as $image) {
                if ($image->isValid()) {
                    $contract->addMedia($image)->toMediaCollection('contract_attach_file');
                    $contract->status = ContractEnum::CLOSE_CONTRACT;
                    $contract->save();
                }
            }
        }


        return $this->responseValidateSuccess(route('admin.contracts.edit', ['contract' => $contract]));
    }

    function updateStatusContractLine(Contracts $contracts)
    {
        $contract_list = ContractLines::where('contract_id', $contracts->id)->get();
        if ($contract_list) {
            foreach ($contract_list as $index => $line) {
                $line->status = $contracts->status;
                $line->save();
            }
        }
    }

    function getMediaFile($model, $collection_name)
    {
        $media = $model->getMedia($collection_name);
        $media = get_medias_detail($media);
        $media = collect($media)->map(function ($item) {
            $item['formated'] = true;
            $item['saved'] = true;
            $item['raw_file'] = null;
            return $item;
        })->toArray();
        return $media;
    }

    public function show($id)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractAllList);

        $contract = Contracts::find($id);
        $conditionStartList = ContractTrait::getListConditionStartStatusRadio();
        $conditionEndList = ContractTrait::getListConditionEndStatusRadio();
        $have_fine_list = ContractTrait::getListHaveFine();
        $contractCategoryList = ConditionGroup::where('condition_group', ConditionGroupEnum::CONTRACT)->get(['id', 'name']);
        $customerGroupList = CustomerGroup::all();
        $contract_signer_side_list = ContractTrait::getContractSignerSideList();

        $data = Contracts::with([
            'contractline.car',
            'contract_forms.contract_form_check_lists',
            'contract_log',
        ])
            ->where('contracts.id', $contract->id)
            ->first();

        $data->contract_start_date = null;
        $data->contract_end_date = null;
        if (strcmp($data->job_type, LongTermRental::class) === 0) {
            $data->contract_start_date = $data->job?->contract_start_date;
            $data->contract_end_date = $data->job?->contract_end_date;
        }

        if (strcmp($data->job_type, Rental::class) === 0) {
            $data->contract_start_date = $data->job?->pickup_date;
            $data->contract_end_date = $data->job?->return_date;
        }

        foreach ($data->contractline as $item) {
            $cmi = CMI::where('car_id', $item->car_id)
                ->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY)
                ->orderBy('year', 'desc')
                ->first();
            $vmi = VMI::where('car_id', $item->car_id)
                ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
                ->orderBy('year', 'desc')
                ->first();
            $item->cmi = $cmi;
            $item->vmi = $vmi;
            $item->rental_price = 0;
            $item->purchase_option_preice = 0;
            if (strcmp($data->job_type, LongTermRental::class) === 0) {
                $lt_pr_line_car = LongTermRentalPRCar::where('car_id', $item->car_id)->first();
                if ($lt_pr_line_car) {
                    $pr_line = LongTermRentalPRLine::find($lt_pr_line_car->lt_rental_pr_line_id);
                    if ($pr_line) {
                        $lt_line_month = LongTermRentalLineMonth::where('lt_rental_line_id', $pr_line->lt_rental_line_id)
                            ->where('lt_rental_month_id', $pr_line->lt_rental_month_id)
                            ->first();
                        $item->rental_price = $lt_line_month->subtotal_price;
                    }
                }
            }

            if (strcmp($data->job_type, Rental::class) === 0) {
                $rental_id = $data->job_id;
                $rental_bill = RentalTrait::getRentalBillPrimaryByRentalId($rental_id);
                $item->rental_price = $rental_bill->total;
            }

            $item->inspection_job_deliver = InspectionJob::where('item_type', $data->job_type)
                ->where('item_id', $data->job_id)
                ->where('car_id', $item->car_id)
                ->where('transfer_type', STATUS_INACTIVE)->first();
            $item->inspection_job_receive = InspectionJob::where('item_type', $data->job_type)
                ->where('item_id', $data->job_id)
                ->where('car_id', $item->car_id)
                ->where('transfer_type', STATUS_ACTIVE)->first();
        }

        $quotation = Quotation::with(['quotation_forms.quotation_form_check_list'])
            ->where(function ($query) use ($contract) {
                $query->where('reference_type', $contract->job_type);
                $query->where('reference_id', $contract->job_id);
                if ($contract->job_type == Rental::class) {
                    $bill_primary = RentalBill::where('rental_id', $contract->job_id)->where('bill_type', 'PRIMARY')->first('id');
                    $query->where('rental_bill_id', $bill_primary->id);
                }
            })->first();

        $car_list = Contracts::join('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
            ->join('cars', 'cars.id', '=', 'contract_lines.car_id')
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id');

        $license_plate_list = $car_list->select(['cars.id', 'cars.license_plate as name'])->groupBy(['cars.id', 'cars.license_plate'])->get();
        $engine_no_list = $car_list->select(['cars.id', 'cars.engine_no as name'])->groupBy(['cars.id', 'cars.engine_no'])->get();
        $chassis_no_list = $car_list->select(['cars.id', 'cars.chassis_no as name'])->groupBy(['cars.id', 'cars.chassis_no'])->get();
        $car_class_list = $car_list->select(['cars.id', 'car_classes.name'])->groupBy(['cars.id', 'car_classes.name'])->get();

        $contract_file = $this->getMediaFile($contract, 'contract_file');

        $contract_attach_file = $contract->getMedia('contract_attach_file');
        $contract_attach_file = get_medias_detail($contract_attach_file);

        $contract_signer = ContractSigners::where('contract_id', $contract->id)->get();
        $arr_contract_signer = [];
        foreach ($contract_signer as $row) {
            $arr_contract_signer[] = [
                'saved' => true,
                'user_sign' => $row,
                'files' => $this->getMediaFile($row, 'contract_signer_file')
            ];
        }




        return view('admin.contracts.form', [
            'data' => $data,
            'quotation' => $quotation,
            'contractCategoryList' => $contractCategoryList,
            'conditionStartList' => $conditionStartList,
            'conditionEndList' => $conditionEndList,
            'customerGroupList' => $customerGroupList,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'car_class_list' => $car_class_list,
            'contract_file' => $contract_file,
            'contract_attach_file' => $contract_attach_file,
            'arr_contract_signer' => $arr_contract_signer,
            'contract_signer_side_list' => $contract_signer_side_list,
            'have_fine_list' => $have_fine_list,
            'view' => true,
        ]);
    }

    public function edit(Contracts $contract)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractAllList);
        if ($contract->status == ContractEnum::CANCEL_CONTRACT || $contract->status == ContractEnum::CLOSE_CONTRACT) {
            return redirect()->action(
                [ContractsController::class, 'show'],
                ['contract' => $contract]
            );
        }

        $conditionStartList = ContractTrait::getListConditionStartStatusRadio();
        $have_fine_list = ContractTrait::getListHaveFine();
        $conditionEndList = ContractTrait::getListConditionEndStatusRadio();
        $contract_signer_side_list = ContractTrait::getContractSignerSideList();
        $contractCategoryList = ConditionGroup::where('condition_group', ConditionGroupEnum::CONTRACT)->get(['id', 'name']);
        $customerGroupList = CustomerGroup::all();

        $data = Contracts::with([
            'contractline.car',
            'contract_forms.contract_form_check_lists',
            'contract_log',
        ])
            ->where('contracts.id', $contract->id)
            ->first();

        $data->contract_start_date = null;
        $data->contract_end_date = null;
        if (strcmp($data->job_type, LongTermRental::class) === 0) {
            $data->contract_start_date = $data->job?->contract_start_date;
            $data->contract_end_date = $data->job?->contract_end_date;
        }

        if (strcmp($data->job_type, Rental::class) === 0) {
            $data->contract_start_date = $data->job?->pickup_date;
            $data->contract_end_date = $data->job?->return_date;
        }

        foreach ($data->contractline as $item) {
            $cmi = CMI::where('car_id', $item->car_id)
                ->where('status_cmi', InsuranceCarStatusEnum::UNDER_POLICY)
                ->orderBy('year', 'desc')
                ->first();
            $vmi = VMI::where('car_id', $item->car_id)
                ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
                ->orderBy('year', 'desc')
                ->first();
            $item->cmi = $cmi;
            $item->vmi = $vmi;
            $item->rental_price = 0;
            $item->purchase_option_preice = 0;
            if (strcmp($data->job_type, LongTermRental::class) === 0) {
                $lt_pr_line_car = LongTermRentalPRCar::where('car_id', $item->car_id)->first();
                if ($lt_pr_line_car) {
                    $pr_line = LongTermRentalPRLine::find($lt_pr_line_car->lt_rental_pr_line_id);
                    if ($pr_line) {
                        $lt_line_month = LongTermRentalLineMonth::where('lt_rental_line_id', $pr_line->lt_rental_line_id)
                            ->where('lt_rental_month_id', $pr_line->lt_rental_month_id)
                            ->first();
                        $item->rental_price = $lt_line_month->subtotal_price;
                        $item->purchase_option_preice = $lt_line_month->total_purchase_options;
                    }
                }
            }

            if (strcmp($data->job_type, Rental::class) === 0) {
                $rental_id = $data->job_id;
                $rental_bill = RentalTrait::getRentalBillPrimaryByRentalId($rental_id);
                $item->rental_price = $rental_bill->total;
            }

            $item->inspection_job_deliver = InspectionJob::where('item_type', $data->job_type)
                ->where('item_id', $data->job_id)
                ->where('car_id', $item->car_id)
                ->where('transfer_type', STATUS_INACTIVE)->first();
            $item->inspection_job_receive = InspectionJob::where('item_type', $data->job_type)
                ->where('item_id', $data->job_id)
                ->where('car_id', $item->car_id)
                ->where('transfer_type', STATUS_ACTIVE)->first();
        }

        $quotation = Quotation::with(['quotation_forms.quotation_form_check_list'])
            ->where(function ($query) use ($contract) {
                $query->where('reference_type', $contract->job_type);
                $query->where('reference_id', $contract->job_id);
                if ($contract->job_type == Rental::class) {
                    $bill_primary = RentalBill::where('rental_id', $contract->job_id)->where('bill_type', 'PRIMARY')->first('id');
                    $query->where('rental_bill_id', $bill_primary->id);
                }
            })->first();

        $car_list = Contracts::join('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
            ->join('cars', 'cars.id', '=', 'contract_lines.car_id')
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id');

        $license_plate_list = $car_list->select(['cars.id', 'cars.license_plate as name'])->groupBy(['cars.id', 'cars.license_plate'])->get();
        $engine_no_list = $car_list->select(['cars.id', 'cars.engine_no as name'])->groupBy(['cars.id', 'cars.engine_no'])->get();
        $chassis_no_list = $car_list->select(['cars.id', 'cars.chassis_no as name'])->groupBy(['cars.id', 'cars.chassis_no'])->get();
        $car_class_list = $car_list->select(['cars.id', 'car_classes.name'])->groupBy(['cars.id', 'car_classes.name'])->get();

        $contract_file = $this->getMediaFile($contract, 'contract_file');

        $contract_attach_file = $contract->getMedia('contract_attach_file');
        $contract_attach_file = get_medias_detail($contract_attach_file);

        $contract_signer = ContractSigners::where('contract_id', $contract->id)->get();
        $arr_contract_signer = [];
        foreach ($contract_signer as $row) {
            $arr_contract_signer[] = [
                'saved' => true,
                'user_sign' => $row,
                'files' => $this->getMediaFile($row, 'contract_signer_file')
            ];
        }
        return view('admin.contracts.form', [
            'data' => $data,
            'quotation' => $quotation,
            'contractCategoryList' => $contractCategoryList,
            'conditionStartList' => $conditionStartList,
            'conditionEndList' => $conditionEndList,
            'customerGroupList' => $customerGroupList,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'car_class_list' => $car_class_list,
            'contract_file' => $contract_file,
            'contract_attach_file' => $contract_attach_file,
            'arr_contract_signer' => $arr_contract_signer,
            'contract_signer_side_list' => $contract_signer_side_list,
            'have_fine_list' => $have_fine_list
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractAllList);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractAllList);
    }

    public static function createAutoContract(Rental $rental)
    {
        $rental_lines = RentalTrait::getRentalLineCars($rental->id);
        if (sizeof($rental_lines) > 0) {
            $contract = new Contracts();
            $contract_count = DB::table('contracts')->count() + 1;
            $contract->worksheet_no = generateRecordNumber('D-', $contract_count);
            $contract->job_type = Rental::class;
            $contract->job_id = $rental->id;
            $contract->customer_id = $rental->customer_id;
            $contract->start_rent = ContractEnum::START_RENT_PICKUP_DATE;
            $contract->end_rent = ContractEnum::END_RENT_EXPIRE_DATE;
            $contract->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
            $contract->save();

            foreach ($rental_lines as $index => $rental_line) {
                $contract_list = new ContractLines();
                $contract_list->contract_id = $contract->id;
                $contract_list->car_id = $rental_line->car_id;
                $contract_list->pick_up_date = $rental_line->pickup_date;
                $contract_list->return_date = $rental_line->return_date;
                $contract_list->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
                $contract_list->save();
            }
        }
    }

    public function mockupContract($id)
    {
        $rental = Rental::where('id', $id)->first();
        return $this->createAutoContract($rental);
    }

    public function updateApproveStatusContractLog(Request $request)
    {
        $contract = Contracts::find($request->contract_id);
        if ($contract) {
            if ($request->approveStatus == 1) {
                if ($contract->status == ContractEnum::REQUEST_CHANGE_ADDRESS) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_CHANGE_ADDRESS)->whereNull('status')->first();
                    $customer = Customer::find($contract->customer_id);

                    $customer->address = $contract_log->new_value;
                    $customer->save();

                    $contract->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
                    $contract->save();

                    $contract_log->approved_by = \Auth::id();
                    $contract_log->approved_at = now();
                    $contract_log->status = ContractEnum::CONFIRM;
                    $contract_log->save();
                } elseif ($contract->status == ContractEnum::REQUEST_CHANGE_USER_CAR) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_CHANGE_USER_CAR)->whereNull('status')->get();
                    foreach ($contract_log as $log) {
                        $contract_line = ContractLines::where('contract_id', $contract->id)
                            ->where('car_id', $log->car_id)
                            ->first();

                        $new_value = json_decode($log->new_value, true);

                        $contract_line->car_user = $new_value['car_user'];
                        $contract_line->tell = $new_value['car_phone'];
                        $contract_line->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
                        $contract_line->save();

                        $log->approved_by = \Auth::id();
                        $log->approved_at = now();
                        $log->status = ContractEnum::CONFIRM;
                        $log->save();
                    }

                    $contract->status = ContractEnum::ACTIVE_BETWEEN_CONTRACT;
                    $contract->save();
                } elseif ($contract->status == ContractEnum::REQUEST_TRANSFER_CONTRACT) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_TRANSFER_CONTRACT)->whereNull('status')->first();
                    $new_customer = null;
                    if ($contract_log) {
                        $new_customer = Customer::find($contract_log->new_value);
                    }

                    $model = null;
                    if ($contract->job_type == Rental::class) {
                        $model = Rental::find($contract->job_id);
                    } elseif ($contract->job_type == LongTermRental::class) {
                        $model = LongTermRental::find($contract->job_id);
                    }

                    if ($model) {
                        $model->customer_id = $new_customer?->id;
                        $model->customer_name = $new_customer?->name;
                        $model->customer_address = $new_customer?->address;
                        $model->customer_email = $new_customer?->email;
                        $model->customer_province_id = $new_customer?->province_id;
                        $model->customer_tel = $new_customer?->tel;
                        $model->customer_zipcode = null; //TODO รหัสไปรษณีย์หาจากไหน
                        $model->save();
                    }

                    $contract_log->approved_by = \Auth::id();
                    $contract_log->approved_at = now();
                    $contract_log->status = ContractEnum::CONFIRM;
                    $contract_log->save();

                    $contractList = Contracts::where('job_type', $contract->job_type)->where('job_id', $contract->job_id)->get();
                    foreach ($contractList as $model) {
                        $contract_line = ContractLines::where('contract_id', $model->id)->get();
                        foreach ($contract_line as $line) {
                            $line->status = ContractEnum::CANCEL_CONTRACT;
                            $line->save();
                        }

                        $model->status = ContractEnum::CANCEL_CONTRACT;
                        $model->save();

                        //clone contract

                        $newContract = new Contracts();
                        $newContract->worksheet_no = ContractTrait::getWorkSheetNumber();
                        $newContract->job_type = $model->job_type;
                        $newContract->job_id = $model->job_id;
                        $newContract->customer_id = $new_customer->id;
                        $newContract->contract_type = $model->contract_type;
                        $newContract->start_rent = ContractEnum::START_RENT_PICKUP_DATE;
                        $newContract->end_rent = ContractEnum::END_RENT_EXPIRE_DATE;
                        $newContract->status = ContractEnum::ACTIVE_CONTRACT;
                        $newContract->save();

                        foreach ($contract_line as $line) {
                            $newContractLine = new ContractLines();
                            $newContractLine->contract_id = $newContract->id;
                            $newContractLine->car_id = $line->car_id;
                            $newContractLine->pick_up_date = $line->pick_up_date;
                            $newContractLine->return_date = $line->return_date;
                            $newContractLine->car_user = $line->car_user;
                            $newContractLine->tell = $line->tell;
                            $newContractLine->status = ContractEnum::ACTIVE_CONTRACT;
                            $newContractLine->save();
                        }

                        $condition_qoutation = ContractForm::where('contract_id', $model->id)->get();

                        if ($condition_qoutation) {
                            foreach ($condition_qoutation as $condition) {
                                $contract_form = new ContractForm();
                                $contract_form->contract_id = $newContract->id;
                                $contract_form->name = $condition->name;
                                $contract_form->seq = $condition->seq;
                                $contract_form->save();

                                $condition_qoutation_check_list = ContractFormCheckList::where('contract_form_id', $contract_form->id)->get();
                                if ($condition_qoutation_check_list) {
                                    foreach ($condition_qoutation_check_list as $list) {
                                        $contract_form_list = new ContractFormCheckList();
                                        $contract_form_list->contract_form_id = $contract_form->id;
                                        $contract_form_list->name = $list->name;
                                        $contract_form_list->seq = $list->seq;
                                        $contract_form_list->save();
                                    }
                                }
                            }
                        }

                        $oldMediaContractFiles = $model->getMedia('contract_file');
                        if ($oldMediaContractFiles) {
                            foreach ($oldMediaContractFiles as $media) {
                                try {
                                    $media->copy($newContract, 'contract_file');
                                } catch (Exception $e) {
                                    //
                                }
                            }
                        }

                        $oldMediaContractAttachFiles = $model->getMedia('contract_attach_file');
                        if ($oldMediaContractAttachFiles) {
                            foreach ($oldMediaContractAttachFiles as $media) {
                                try {
                                    $media->copy($newContract, 'contract_attach_file');
                                } catch (Exception $e) {
                                    //
                                }
                            }
                        }
                    }
                }
            } else {
                if ($contract->status == ContractEnum::REQUEST_CHANGE_ADDRESS) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_CHANGE_ADDRESS)->whereNull('status')->first();

                    $contract_log->approved_by = \Auth::id();
                    $contract_log->approved_at = now();
                    $contract_log->status = ContractEnum::REJECT;
                    $contract_log->reason = $request->reason;
                    $contract_log->save();

                    $contract->status = ContractEnum::REJECT_REQUEST;
                    $contract->save();
                } elseif ($contract->status == ContractEnum::REQUEST_CHANGE_USER_CAR) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_CHANGE_USER_CAR)->whereNull('status')->get();
                    foreach ($contract_log as $log) {
                        $log->status = ContractEnum::REJECT;
                        $log->reason = $request->reason;
                        $log->approved_by = \Auth::id();
                        $log->approved_at = now();
                        $log->save();
                    }

                    $contract->status = ContractEnum::REJECT_REQUEST;
                    $contract->save();
                } elseif ($contract->status == ContractEnum::REQUEST_TRANSFER_CONTRACT) {
                    $contract_log = ContractLogs::where('contract_id', $contract->id)->where('type_log', ContractEnum::REQUEST_TRANSFER_CONTRACT)->whereNull('status')->first();

                    $contract_log->approved_by = \Auth::id();
                    $contract_log->approved_at = now();
                    $contract_log->status = ContractEnum::REJECT;
                    $contract_log->reason = $request->reason;
                    $contract_log->save();

                    $contract->status = ContractEnum::REJECT_REQUEST;
                    $contract->save();
                }
            }
        }

        return $this->responseValidateSuccess(route('admin.contracts.index'));
    }

    public function getContractLogAndMedia(Request $request)
    {
        $contract = Contracts::find($request->contract_id);
        if ($contract) {
            $media_file = $this->getMediaFile($contract, 'contract_file');
            $contract_log = ContractLogs::where('contract_id', $contract->id)->whereNull('status');
            $customer = null;
            if ($request->status == ContractEnum::REQUEST_CHANGE_USER_CAR) {
                $contract_log = $contract_log->get();
            } else {
                $contract_log = $contract_log->first();
                if ($contract_log->type_log == ContractEnum::REQUEST_TRANSFER_CONTRACT) {
                    $customer = Customer::find($contract_log->new_value);
                }
            }
            $result = [
                'media_file' => $media_file,
                'contract_log' => $contract_log,
                'customer' => $customer,
            ];
            return $this->responseWithCode(true, DATA_SUCCESS, $result, 200);
        } else {
            return $this->responseWithCode(true, DATA_NOT_FOUND, null, 404);
        }
    }

    public function getContractMediaFile(Request $request)
    {
        $contract = Contracts::find($request->contract_id);
        if ($contract) {
            $contract_file = $this->getMediaFile($contract, 'contract_file');
            return $this->responseWithCode(true, DATA_SUCCESS, $contract_file, 200);
        } else {
            return $this->responseWithCode(true, DATA_NOT_FOUND, null, 404);
        }
    }

    public function printContractPdf(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractAllList);
        $contract_id = $request->contract;
        $contract = Contracts::find($contract_id);
        if (!$contract) {
            return false;
        }

        if (strcmp($contract->job_type, LongTermRental::class) === 0) {
            $pdf = $this->getLongTermRentalPdf($contract);
            if ($pdf) {
                return $pdf->stream('contract-' . $contract_id . '.pdf');
            }
        }

        if (strcmp($contract->job_type, Rental::class) === 0) {
            $pdf = $this->getShortTermRentalPdf($contract);
            if ($pdf) {
                return $pdf->stream('contract-' . $contract_id . '.pdf');
            }
        }
        return false;
    }

    public function getShortTermRentalPdf($contract)
    {
        $rental = Rental::find($contract->job_id);
        if (!$rental) {
            return false;
        }
        $product = Product::find($rental->product_id);
        if (strcmp($product->calculate_type, CalculateTypeEnum::DAILY) == 0) {
            $pm = new ProductManagement($rental->service_type_id);
            $pm->setDates($rental->pickup_date, $rental->return_date);
            $rental->rental_date = $pm->order_days;
        } else {
            $rental_date = getDaysTimesBetweenDate($rental->pickup_date, $rental->return_date);
            $rental_date_parts = explode(" ", $rental_date);
            $rental->rental_date = $rental_date_parts[0] ?? '';
        }

        $customer = Customer::find($rental->customer_id);
        $branches = Branch::all();
        $customer_type_list = CustomerTrait::getCustomerType();
        $rental_bill = RentalTrait::getRentalBillPrimaryByRentalId($rental->id);
        if (!$rental_bill) {
            return false;
        }
        $rental_cars = RentalTrait::getRentalLineCars($rental->id);
        $cars = RentalTrait::getRentalCarsByIds($rental_cars->pluck('car_id'));
        $car = $cars->first();
        if (!$car) {
            return false;
        }

        $contract_forms = ContractForm::with(['contract_form_check_lists'])
            ->where('contract_id', $contract->id)
            ->orderBy('seq')
            ->get();
        $pdf = Pdf::loadView(
            'admin.contracts.pdfs.short-term-rental',
            [
                'branches' => $branches,
                'customer' => $customer,
                'rental' => $rental,
                'rental_bill' => $rental_bill,
                'car' => $car,
                'customer_type_list' => $customer_type_list,
                'contract_forms' => $contract_forms,
                'contract' => $contract
            ]
        );
        return $pdf;
    }

    public function getLongTermRentalPdf($contract)
    {
        $lt_rental = LongTermRental::find($contract->job_id);
        if (!$lt_rental) {
            return false;
        }
        $contract_forms = ContractForm::with(['contract_form_check_lists'])
            ->where('contract_id', $contract->id)
            ->orderBy('seq')
            ->get();
        // $array = str_split($contract_forms[0]->contract_form_check_lists[0]->name, 10);
        $contract_signers = ContractSigners::where('contract_id', $contract->id)->get();
        $signer_host_list = $contract_signers->where('contract_side', ContractSignerSideEnum::HOST);
        $signer_renter_list = $contract_signers->where('contract_side', ContractSignerSideEnum::RENTER);
        $attorney_host_list = $signer_host_list->where('is_attorney', true);
        $attorney_renter_list = $signer_renter_list->where('is_attorney', true);
        $car_list = Car::leftjoin('contract_lines', 'contract_lines.car_id', '=', 'cars.id')
            ->where('contract_lines.contract_id', $contract->id)
            ->select('cars.*')
            ->get();

        $pdf = Pdf::loadView(
            'admin.contracts.pdfs.long-term-rental',
            [
                'lt_rental' => $lt_rental,
                'contract' => $contract,
                'contract_forms' => $contract_forms,
                'contract_signers' => $contract_signers,
                'signer_host_list' => $signer_host_list,
                'signer_renter_list' => $signer_renter_list,
                'attorney_host_list' => $attorney_host_list,
                'attorney_renter_list' => $attorney_renter_list,
                'car_list' => $car_list
            ]
        );
        return $pdf;
    }

    public function getExpiredContractPdf($contract)
    {
        $today = Carbon::now();
        $month = $today->format('m');
        $year = $today->format('Y') + 543;

        $customer = Customer::find($contract->customer_id);
        if (!$customer) {
            return false;
        }
        $cars = Car::leftjoin('contract_lines', 'contract_lines.car_id', '=', 'cars.id')
            ->where('contract_lines.contract_id', $contract->id)
            ->select('cars.*')
            ->get();
        $pdf = Pdf::loadView(
            'admin.contracts.pdfs.contract-expired',
            [
                'contract' => $contract,
                'today' => $today,
                'customer' => $customer,
                'month' => $month,
                'year' => $year,
                'cars' => $cars
            ]
        );
        return $pdf;
    }
}
