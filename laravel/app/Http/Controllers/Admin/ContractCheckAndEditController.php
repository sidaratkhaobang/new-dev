<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ContractEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ContractLines;
use App\Models\ContractLogs;
use App\Models\Contracts;
use App\Traits\ContractTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractCheckAndEditController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckAndEdit);

        $worksheet_no = $request->worksheet_no;
        $contract_type = $request->contract_type;
        $car_id = $request->car_id;
        $customer_id = $request->customer_id;
        $contract_start_date = $request->contract_start_date;
        $contract_end_date = $request->contract_end_date;

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
            ->search($request)
            ->sortable(['worksheet_no' => 'desc'])
            ->paginate(PER_PAGE);

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

        $statusRequestList = ContractTrait::getListStatusRequest();

//        $car_list = Contracts::join('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
//            ->join('cars', 'cars.id', '=', 'contract_lines.car_id')
//            ->select(['cars.id', 'cars.license_plate as name'])->get();

        return view('admin.contract-check-and-edit.index', [
            'lists' => $lists,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_list' => $worksheet_no_list,
            'contract_type' => $contract_type,
            'contract_type_list' => $contract_type_list,
            'car_id' => $car_id,
            'car_id_list' => $car_id_list,
            'customer_id' => $customer_id,
            'customer_id_list' => $customer_id_list,
            'contract_start_date' => $contract_start_date,
            'contract_end_date' => $contract_end_date,
            'statusRequestList' => $statusRequestList,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckAndEdit);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckAndEdit);

        $contract = Contracts::find($request->contract_id);

        $validator = Validator::make($request->all(), [
            'status_request' => 'required',
        ], [], [
            'status_request' => __('ประเภทการขอเปลี่ยนแปลง'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->status_request == ContractEnum::REQUEST_CHANGE_ADDRESS) {
            $validator = Validator::make($request->all(), [
                'change_address_new_address' => 'required',
            ], [], [
                'change_address_new_address' => __('ที่อยู่ใหม่'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $contract_log = new ContractLogs();
            $contract_log->contract_id = $contract->id;
            $contract_log->type_log = $request->status_request;
            $contract_log->remark = $request->change_address_description;
            $contract_log->new_value = $request->change_address_new_address;
            $contract_log->old_value = $contract->customer->address;
            $contract_log->save();

            $contract->status = ContractEnum::REQUEST_CHANGE_ADDRESS;
            $contract->save();

        } elseif ($request->status_request == ContractEnum::REQUEST_CHANGE_USER_CAR) {
            $validator = Validator::make($request->all(), [
                'change_user_car' => 'required',
                'change_user_car.*.car_license_plate' => 'required',
                'change_user_car.*.car_user' => 'required',
                'change_user_car.*.car_phone' => 'required',
            ], [], [
                'change_user_car' => __('ข้อมูลผู้ใช้'),
                'change_user_car.*.car_license_plate' => __('ทะเบียนรถ'),
                'change_user_car.*.car_user' => __('ชื่อผู้ใช้'),
                'change_user_car.*.car_phone' => __('เบอร์โทร'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            foreach ($request->change_user_car as $item) {
                $contract_line = ContractLines::where('contract_id', $contract->id)->where('car_id', $item['car_id'])->first();

                $old_value = [
                    "car_id" => $contract_line?->id,
                    "car_license_plate" => $contract_line?->license_plate,
                    "car_user" => $contract_line?->car_user,
                    "car_phone" => $contract_line?->tell,
                ];

                $contract_log = new ContractLogs();
                $contract_log->contract_id = $contract->id;
                $contract_log->car_id = $contract_line->car_id;
                $contract_log->type_log = $request->status_request;
                $contract_log->remark = $request->change_user_car_description;
                $contract_log->new_value = json_encode($item);
                $contract_log->old_value = json_encode($old_value);
                $contract_log->save();

                $contract_line->status = ContractEnum::REQUEST_CHANGE_USER_CAR;
                $contract_line->save();
            }
            $contract->status = ContractEnum::REQUEST_CHANGE_USER_CAR;
            $contract->save();
        } elseif ($request->status_request == ContractEnum::REQUEST_TRANSFER_CONTRACT) {
            $validator = Validator::make($request->all(), [
                'transfer_customer' => 'required',
            ], [], [
                'transfer_customer' => __('ข้อมูลลูกค้า'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $contract_log = new ContractLogs();
            $contract_log->contract_id = $contract->id;
            $contract_log->type_log = $request->status_request;
            $contract_log->remark = $request->transfer_description;
            $contract_log->new_value = $request->transfer_customer;
            $contract_log->old_value = $contract->customer->id;
            $contract_log->save();

            $contract->status = ContractEnum::REQUEST_TRANSFER_CONTRACT;
            $contract->save();
        }

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

        return $this->responseValidateSuccess(route('admin.contract-check-and-edit.index', ['contract' => $contract]));
    }

    public function show($id)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckAndEdit);
    }

    public function edit($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckAndEdit);
    }

    public function update(Request $request, $id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckAndEdit);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckAndEdit);
    }
}
