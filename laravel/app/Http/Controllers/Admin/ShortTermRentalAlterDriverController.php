<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Enums\InspectionStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Models\InspectionJob;
use App\Models\InspectionJobStep;
use App\Models\CustomerDriver;
use App\Models\RentalDriver;
use App\Traits\RentalTrait;
use App\Traits\RentalDriverTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShortTermRentalAlterDriverController extends Controller
{
    use RentalTrait, RentalDriverTrait;
    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        $customer_id = $rental?->customer_id;
        $rental_driver_list = [];
        $product_additional_list = [];
        $product_transport_list = [];
        $service_type = ($rental?->serviceType) ? $rental?->serviceType?->service_type : null;
        $allow_driver = RentalTrait::canAddDriver($service_type);
        $allow_product_additional = RentalTrait::canAddProductAdditional($service_type);
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);
        if ($allow_driver) {
            $rental_driver_list = RentalTrait::getRentalDriverList($rental?->id);
            if (sizeof($rental_driver_list) === 0) {
                $this->copyCustomerDriverToRentalDriver($customer_id, $rental?->id);
            }
        }
        if ($allow_product_additional) {
            //$product_additional_list = RentalTrait::getRentalLineProductAdditionalList($rental?->id);
        }
        $product_transport_list = null;
        $product_transport_return_list = null;
        if ($allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($rental?->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($rental?->id, TransferTypeEnum::IN);
            $product_transport_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }
        $cars = RentalTrait::getRentalLineCarList($rental_id);
        $product_additional_price_list = ProductAdditional::select('id', 'price')->pluck('price', 'id')->toArray();
        $driver_list = RentalTrait::getRentalDriverList($rental->id);
        return view('admin.short-term-rental-alter-driver.form', [
            'rental_id' => $rental_id,
            'd' => $rental,
            //'rental_bill_id' => $rental_bill->id,
            //'driver_list' => $new_rental_driver_list,
            'cars' => $cars,
            'rental' => $rental,
            'allow_product_additional' => $allow_product_additional,
            'allow_driver' => $allow_driver,
            'product_additional_list' => $product_additional_list,
            'allow_product_transport' => $allow_product_transport,
            'product_transport_list' => $product_transport_list,
            'product_transport_return_list' => $product_transport_return_list,
            'product_additional_price_list' => $product_additional_price_list,
            'driver_list' => $driver_list
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $product_additionals = $request->product_additionals;
        $new_cars = $request->new_cars;
        $bill_subtotal = 0;
        $bill_total = 0;
        $rental = Rental::find($rental_id);

        $validator = Validator::make($request->all(), [
            'product_additionals.*.product_additional_id' => 'required',
            'product_additionals.*.amount' => 'required|not_in:0',
        ], [], [
            'product_additionals.*.product_additional_id' => 'ชื่อออฟชั่นเสริม',
            'product_additionals.*.amount' => 'จำนวนออฟชั่นเสริม'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //dd($request->all());
        if (!$rental->serviceType) {
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }
        if (RentalTrait::canAddDriver($rental->serviceType->service_type)) {
            $car_id = $request->car_id;
            $pending_delete_license_files = $request->pending_delete_license_files;
            $pending_delete_citizen_files = $request->pending_delete_citizen_files;
            $this->deleteRentalDriverFiles($pending_delete_license_files);
            $this->deleteRentalDriverFiles($pending_delete_citizen_files);

            $this->saveRentalDriver($request, $rental_id);
        }
        //$rental_bill = RentalBill::where('rental_id', $rental_id)->first();

        if (RentalTrait::canAddProductAdditional($rental->serviceType->service_type)) {
            $this->saveRentalProductAdditional($request, $rental_id, $rental);
        }

        if (RentalTrait::canAddProductTransport($rental->serviceType->service_type)) {
            if (!isset($request->product_transport)) {
                return $this->responseWithCode(false, 'กรุณากรอกข้อมูลสินค้านำส่งอย่างน้อยหนึ่งสินค้า', null, 422);
            } else if (!isset($request->product_transport_return)) {
                return $this->responseWithCode(false, 'กรุณากรอกข้อมูลสินค้านำกลับอย่างน้อยหนึ่งสินค้า', null, 422);
            } else {
                $this->saveRentalProductTransport($request, $rental_id);
            }
        }

        $rental->objective = $request->objective;
        $rental->remark = $request->rental_remark;
        $rental->save();
        // $require_sap_inform = false;
        /* if ($new_cars && sizeof($new_cars) > 0) {
            $sum_product_total = RentalLine::where('rental_id', $rental->id)
                ->where('item_type', Product::class)
                ->sum('total');
            foreach ($new_cars as $key => $new_car) {
                $car = new RentalLine;
                $car->rental_id = $rental_id;
                $car->item_type = Product::class;
                $car->item_id = $rental->product_id;
                $car->car_id = $new_car['id'];
                $car->amount = 1;
                $car->pickup_date = $rental->pickup_date;
                $car->return_date = $rental->return_date;
                $car->former_car_id = $new_car['former_car_id'];
                $car->save();

                $car->name = in_array($car->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $car->name : $car->summary_display_name;
                $car->description = in_array($car->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $car->description : $car->summary_description;
                $car->save();

                $pm = new ProductManagement($rental->service_type_id);
                $pm->setBranchId($rental->branch_id);
                $pm->setDates($rental->pickup_date, $rental->return_date);
                $price = $pm->findPrice($car->item_id, $car);
                $car->subtotal = $price;
                $car->total = $price;
                $car->save();

                $bill_subtotal += $price;
                $bill_total += $price;

                $former_car = RentalLine::where('rental_id', $rental->id)
                    ->where('car_id', $new_car['former_car_id'])
                    ->first();
                $former_car->status = STATUS_DEFAULT;
                $former_car->save();

                $car->rental_bill_id = $former_car->rental_bill_id;
                $car->save();

                if ($bill_total > $sum_product_total) {
                    $secondary_bill = RentalBill::where('rental_id', $rental->id)
                        ->where('bill_type', RentalBillTypeEnum::SECONDARY)
                        ->where('status', RentalStatusEnum::PENDING)
                        ->first();
                    if (!$secondary_bill) {
                        // $require_sap_inform = true;
                        $secondary_bill = new RentalBill;
                        $secondary_bill->rental_id = $rental_id;
                        $secondary_bill->status = RentalStatusEnum::PENDING;
                        $secondary_bill->bill_type = RentalBillTypeEnum::SECONDARY;
                        $secondary_bill->subtotal = 0;
                        $secondary_bill->vat = 0;
                        $secondary_bill->total = 0;
                        $secondary_bill->save();
                    }
                    $diff_total = floatval($bill_total) - floatval($sum_product_total);
                    $diff_vat = calculateVat($diff_total);
                    $diff_subtotal = $diff_total - $diff_vat;

                    $secondary_bill->total = $diff_total;
                    $secondary_bill->vat = $diff_vat;
                    $secondary_bill->subtotal = $diff_subtotal;
                    $secondary_bill->save();

                    $rental_line = RentalLine::where('rental_id', $rental->id)
                        ->where('rental_bill_id', $secondary_bill->id)
                        ->where('item_type', OrderLineTypeEnum::ADDITIONAL_PRODUCT_DIFF_COST)
                        ->first();
                    if (!$rental_line) {
                        $rental_line = new RentalLine;
                    }
                    $rental_line->rental_id = $rental_id;
                    $rental_line->rental_bill_id = $secondary_bill->id;
                    $rental_line->item_type = OrderLineTypeEnum::PRODUCT_DIFF;
                    $rental_line->item_id = (string) Str::orderedUuid();
                    $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::PRODUCT_DIFF);
                    $rental_line->description = '';
                    $rental_line->subtotal = $diff_subtotal;
                    $rental_line->vat = $diff_vat;
                    $rental_line->total = $diff_total;
                    $rental_line->amount = 1;
                    $rental_line->pickup_date = $rental->pickup_date;
                    $rental_line->return_date = $rental->return_date;
                    $rental_line->save();
                    RentalTrait::createRentalQuotation($rental, $secondary_bill);
                }
            }
        }

        if ($request->drivers && sizeof($request->drivers) > 0) {
            $rental_driver = new ShortTermRentalDriverController();
            $rental_driver->saveRentalDriver($request, $rental_id);
        }

        if ($product_additionals && sizeof($product_additionals) > 0) {
            $total = 0;
            $amount = 0;
            $total = 0;
            $amount = 0;
            $product_add_arr = [];
            $secondary_bill = RentalBill::where('rental_id', $rental->id)
                ->where('bill_type', RentalBillTypeEnum::SECONDARY)
                ->where('status', RentalStatusEnum::PENDING)
                ->first();
            $temp_secondary_bill = false;
            if (!$secondary_bill) {
                $temp_secondary_bill = true;
                $secondary_bill = new RentalBill;
                $secondary_bill->rental_id = $rental_id;
                $secondary_bill->status = RentalStatusEnum::PENDING;
                $secondary_bill->bill_type = RentalBillTypeEnum::SECONDARY;
                $secondary_bill->subtotal = 0;
                $secondary_bill->vat = 0;
                $secondary_bill->total = 0;
                $secondary_bill->save();
            }
            foreach ($request->product_additionals as $key => $item) {
                if (isset($item['id'])) {
                    $rental_pa = RentalProductAdditional::find($item['id']);
                } else {
                    $rental_pa = new RentalProductAdditional;
                    $rental_pa->rental_bill_id = $secondary_bill->id;
                }
                $rental_pa->rental_id = $rental_id;
                $rental_pa->product_additional_id = $item['product_additional_id'];
                $rental_pa->car_id = $item['car_id'];
                $rental_pa->name = $item['name'];
                $rental_pa->price = $item['price'];
                $rental_pa->amount = $item['amount'];
                $rental_pa->is_free = filter_var($item['is_free'], FILTER_VALIDATE_BOOLEAN);
                $rental_pa->is_from_product = filter_var($item['is_from_product'], FILTER_VALIDATE_BOOLEAN);
                $rental_pa->save();
                $is_free = $rental_pa->is_free;
                $_bill = RentalBill::find($rental_pa->rental_bill_id);
                if ($_bill && strcmp($_bill->status, RentalStatusEnum::PENDING) === 0) {
                    if (!$is_free) {
                        $total += floatval($item['price'] * $item['amount']);
                    }
                    $amount += intval($item['amount']);
                }
            }
            if (floatval($total) > 0) {
                $pending_product_cost_line = RentalLine::where('rental_bill_id', $secondary_bill->id)
                    ->whereIn('item_type', [OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST, OrderLineTypeEnum::ADDITIONAL_PRODUCT_DIFF_COST])
                    ->get()->last();
                if (!$pending_product_cost_line) {
                    $pending_product_cost_line = new RentalLine();
                    $pending_product_cost_line->item_type = OrderLineTypeEnum::ADDITIONAL_PRODUCT_DIFF_COST;
                }
                $pending_product_cost_line->rental_id = $rental_id;
                $pending_product_cost_line->rental_bill_id = $secondary_bill->id;
                $pending_product_cost_line->item_type = OrderLineTypeEnum::ADDITIONAL_PRODUCT_DIFF_COST;
                $pending_product_cost_line->item_id = (string) Str::orderedUuid();
                $pending_product_cost_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::ADDITIONAL_PRODUCT_DIFF_COST);
                $pending_product_cost_line->description = '';
                $pending_product_cost_line->subtotal = $total;
                $pending_product_cost_line->total = $total;
                $pending_product_cost_line->amount = $amount;
                $pending_product_cost_line->save();

                $rental_lines = RentalLine::where('rental_bill_id', $secondary_bill->id)->get();
                $subtotal_of_rental_lines = 0;
                $total_of_rental_lines = 0;
                foreach ($rental_lines as $key => $line) {
                    $subtotal_of_rental_lines += $line->subtotal;
                    $total_of_rental_lines += $line->total;
                }
                $secondary_bill->subtotal = $subtotal_of_rental_lines;
                $secondary_bill->vat = calculateVat($total_of_rental_lines);
                $secondary_bill->total = $total_of_rental_lines;
                $secondary_bill->save();
                RentalTrait::createRentalQuotation($rental, $secondary_bill);
            } else {
                if ($temp_secondary_bill) {
                    $secondary_bill->delete();
                }
            }
        }

        if (RentalTrait::canAddProductTransport($rental->serviceType->service_type)) {
            if ((isset($request->product_transport) || isset($request->product_transport_return)) && (count($request->product_transport) > 0 || count($request->product_transport_return) > 0)) {
                $rental_driver = new ShortTermRentalDriverController();
                $rental_driver->saveRentalProductTransport($request, $rental_id);
            } else {
                return $this->responseWithCode(false, 'กรุณากรอกข้อมูลสินค้า', null, 422);
            }
        } */
        $redirect_route = route('admin.short-term-rental.alter.edit-bill', [
            'rental_id' => $rental_id,
        ]);
        $redirect_route = null;
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental_bill = RentalBill::where('rental_id', $rental_id)->first();
        $rental_bill_primary = RentalTrait::getRentalBillPrimaryByRentalId($rental_id);
        $rental_line_ids = RentalLine::where('rental_id', $rental_id)->pluck('id');
        $rental = Rental::find($rental_id);
        $customer_id = $rental->customer_id;
        $rental_driver_list = [];
        $product_additional_list = [];

        $service_type = ($rental->serviceType) ? $rental->serviceType->service_type : null;
        $allow_driver = RentalTrait::canAddDriver($service_type);
        $allow_product_additional = RentalTrait::canAddProductAdditional($service_type);
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);

        if ($allow_driver) {
            $rental_driver_list = RentalTrait::getRentalDriverList($rental->id);
            // if (sizeof($rental_driver_list) === 0) {
            //     $this->copyCustomerDriverToRentalDriver($customer_id, $rental->id);
            // }
        }
        if ($allow_product_additional) {
            $product_additional_list = RentalTrait::getRentalProductAdditionalList($rental->id);
        }

        $product_transport_list = null;
        $product_transport_return_list = null;
        if ($allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($rental->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($rental->id, TransferTypeEnum::IN);

            $product_transport_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }

        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line_ids)
            ->when($rental_bill_primary, function ($query) use ($rental_bill_primary) {
                $query->where('rental_lines.rental_bill_id', $rental_bill_primary->id);
            })
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->get()->map(function ($car) use ($rental) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;

                $inspection_job = InspectionJob::where('car_id', $car->id)->where('item_id', $rental->id)->orderBy('transfer_type', 'desc')->get();
                if (is_countable($inspection_job)) {
                    $status_arr = [];
                    foreach ($inspection_job as $item2) {
                        $status = [];
                        $inspection_job_step = InspectionJobStep::where('inspection_job_id', $item2->id)->first();
                        if ($inspection_job_step) {
                            $inspection_job_status = InspectionJob::find($inspection_job_step->inspection_job_id);
                            $status['status_inspection'] = $inspection_job_step->inspection_status ? $inspection_job_step->inspection_status : '';
                            if ($inspection_job_step->inspection_status) {
                                $status['status_inspection_text'] = ($inspection_job_step->inspection_status == InspectionStatusEnum::NOT_PASS) ? __('inspection_cars.status_' . $inspection_job_step->inspection_status) : __('inspection_cars.status_' . $inspection_job_status->inspection_status);
                            } else {
                                $status['status_inspection_text'] = '';
                            }
                            if ($inspection_job_step->inspection_status) {
                                $status['status_inspection_class'] = ($inspection_job_step->inspection_status == InspectionStatusEnum::NOT_PASS) ? 'bg-' . __('inspection_cars.class_' . $inspection_job_step->inspection_status) : 'bg-' . __('inspection_cars.class_' . $inspection_job_status->inspection_status);
                            } else {
                                $status['status_inspection_class'] = '';
                            }
                            $status['remark_reason'] = $inspection_job_step->remark_reason ? $inspection_job_step->remark_reason : '';
                            $status['remark_reason_text'] = $inspection_job_step->remark_reason ? __('inspection_cars.remark_reason_' . $inspection_job_step->remark_reason) : '';
                            $status['transfer_type'] = $inspection_job_step->transfer_type ? $inspection_job_step->transfer_type : '';
                            $status['transfer_type_text'] = $inspection_job_step->transfer_type ? __('operations.transfer_type_' . $inspection_job_step->transfer_type) : '';
                            if (count($status) > 0) {
                                $status_arr[] = $status;
                            }
                        }
                    }
                    $car->status_arr = $status_arr;
                }
                return $car;
            });

        $rental_bill_id = ($rental_bill) ? $rental_bill->id : null;
        $new_rental_driver_list = RentalTrait::getRentalDriverList($rental->id);
        return view('admin.short-term-rental-alter-asset.form', [
            'view' => true,
            'rental_id' => $rental_id,
            'd' => $rental,
            'rental_bill_id' => $rental_bill_id,
            'driver_list' => $new_rental_driver_list,
            'cars' => $cars,
            'rental' => $rental,
            'allow_product_additional' => $allow_product_additional,
            'allow_driver' => $allow_driver,
            'product_additional_list' => $product_additional_list,
            'allow_product_transport' => $allow_product_transport,
            'product_transport_list' => $product_transport_list,
            'product_transport_return_list' => $product_transport_return_list,
        ]);
    }

    public function getRentalCarsWithStatus($rental_bill_id, $rental_id = null)
    {
        $rental = Rental::find($rental_id);
        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('rental_lines.rental_bill_id', $rental_bill_id)
            ->where('rental_lines.item_type', Product::class)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
                'rental_lines.id as rental_line_id',
                'rental_lines.status as status',
            )->orderBy('rental_line_id')
            ->get()->map(function ($car) use ($rental) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;
                if ($car->status == STATUS_DEFAULT) {
                    $car->is_replace = 1;
                }
                $inspection_job = InspectionJob::where('car_id', $car->id)->where('item_id', $rental->id)->orderBy('transfer_type', 'desc')->get();
                if (is_countable($inspection_job)) {
                    $status_arr = [];
                    foreach ($inspection_job as $item2) {
                        $status = [];
                        $inspection_job_step = InspectionJobStep::where('inspection_job_id', $item2->id)->first();
                        if ($inspection_job_step) {
                            $inspection_job_status = InspectionJob::find($inspection_job_step->inspection_job_id);
                            $status['status_inspection'] = $inspection_job_step->inspection_status ? $inspection_job_step->inspection_status : '';
                            if ($inspection_job_step->inspection_status) {
                                $status['status_inspection_text'] = ($inspection_job_step->inspection_status == InspectionStatusEnum::NOT_PASS) ? __('inspection_cars.status_' . $inspection_job_step->inspection_status) : __('inspection_cars.status_' . $inspection_job_status->inspection_status);
                            } else {
                                $status['status_inspection_text'] = '';
                            }
                            if ($inspection_job_step->inspection_status) {
                                $status['status_inspection_class'] = ($inspection_job_step->inspection_status == InspectionStatusEnum::NOT_PASS) ? 'bg-' . __('inspection_cars.class_' . $inspection_job_step->inspection_status) : 'bg-' . __('inspection_cars.class_' . $inspection_job_status->inspection_status);
                            } else {
                                $status['status_inspection_class'] = '';
                            }
                            $status['remark_reason'] = $inspection_job_step->remark_reason ? $inspection_job_step->remark_reason : '';
                            $status['remark_reason_text'] = $inspection_job_step->remark_reason ? __('inspection_cars.remark_reason_' . $inspection_job_step->remark_reason) : '';
                            $status['transfer_type'] = $inspection_job_step->transfer_type ? $inspection_job_step->transfer_type : '';
                            $status['transfer_type_text'] = $inspection_job_step->transfer_type ? __('operations.transfer_type_' . $inspection_job_step->transfer_type) : '';
                            if (count($status) > 0) {
                                $status_arr[] = $status;
                            }
                        }
                    }
                    $car->status_arr = $status_arr;
                }
                return $car;
            });
        return $cars;
    }
}
