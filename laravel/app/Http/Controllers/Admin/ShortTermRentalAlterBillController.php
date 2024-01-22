<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\Sap\SapProcess;
use App\Enums\Actions;
use App\Enums\OrderLineTypeEnum;
use App\Enums\Resources;
use App\Factories\QuotationFactory;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\DrivingJob;
use App\Models\DrivingJobLog;
use App\Models\CarParkTransfer;
use App\Models\InspectionJob;
use App\Models\InspectionFlow;
use App\Traits\InspectionTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Enums\QuotationStatusEnum;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Product;
use App\Models\InspectionStep;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerBillingAddress;
use App\Models\Province;
use App\Factories\InspectionJobFactory;

class ShortTermRentalAlterBillController extends Controller
{
    use RentalTrait;
    public function index(Request $request)
    {
        //TODO
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental_line_ids = RentalLine::where('rental_id', $rental_id)->pluck('id');
        $d = Rental::findOrFail($rental_id);
        $list = RentalBill::where('rental_id', $rental_id)->get();
        foreach ($list as $key => $rental_bill) {
            $checked_promotion_code_id = RentalTrait::getSelectedPromotion($rental_bill->id)->first();
            $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill->id);
            $om = new OrderManagement($rental_bill);
            $om->setPromotion($checked_promotion_code_id, $checked_vouchers);
            $om->setWithHoldingTaxVal($rental_bill->withholding_tax_value);
            $om->calculate();
            $summary = $om->getSummary();
            $rental_bill->total = $summary['total'];
        }
        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line_ids)
            ->where('rental_lines.item_type', Product::class)
            // ->where('rental_lines.rental_bill_id', $rental_bill->id)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->get()->map(function ($car) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;
                return $car;
            });

        return view('admin.short-term-rental-alter-bill.index', [
            'd' => $d,
            'rental_id' => $rental_id,
            'list' => $list,
            'customer_id' => $d->customer_id,
            'cars' => $cars,
        ]);
    }

    public function show(Request $request)
    {
        //TODO
        // dd($rental_bill_id);
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental_line_ids = RentalLine::where('rental_id', $rental_id)->pluck('id');
        $rental = Rental::find($rental_id);
        // $rental_bill = RentalBill::find($rental_bill_id);
        // $customer_type = Customer::find($rental->customer_id);
        // $customer_type = $customer_type->customer_type;
        $d = Rental::findOrFail($rental_id);
        $list = RentalBill::where('rental_id', $rental_id)->get();
        $customer_id = $rental->customer_id;
        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line_ids)
            ->where('rental_lines.item_type', Product::class)
            // ->where('rental_lines.rental_bill_id', $rental_bill->id)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->get()->map(function ($car) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;
                return $car;
            });

        // return view('admin.short-term-rental.index', [
        return view('admin.short-term-rental-alter-bill.index', [
            'view' => true,
            'd' => $d,
            'rental_id' => $rental_id,
            'list' => $list,
            'customer_id' => $customer_id,
            'cars' => $cars,
        ]);
    }

    public function edit($rental_bill_id, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental_bill = RentalBill::find($rental_bill_id);
        $rental_id = $rental_bill->rental_id;
        $rental_line_ids = RentalLine::where('rental_id', $rental_bill->rental_id)->pluck('id');
        $rental = Rental::find($rental_bill->rental_id);
        $customer_type = Customer::find($rental->customer_id);
        $customer_type = $customer_type->customer_type;

        $rental_line_list = RentalLine::leftjoin('cars', 'cars.id', '=', 'rental_lines.car_id')->where('rental_lines.rental_bill_id', $rental_bill_id)->select('rental_lines.*', 'cars.license_plate')->get();
        $rental_line_list->map(function ($item) {
            $name = in_array($item->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $item->name : $item->summary_display_name;
            $item->summary_name_i = $item->name;
            $description = in_array($item->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $item->description : $item->summary_description;
            $item->summary_description_i = $item->description;
            return $item;
        });
        $payment_gateway_name = null;
        $payment_status_name = null;
        if ($rental_bill->payment_gateway) {
            $payment_gateway_name = __('short_term_rentals.payment_gateway_' . $rental_bill->payment_gateway);
        }
        if ($rental_bill->status) {
            $payment_status_name = __('short_term_rentals.status_' . $rental_bill->status);
        }

        $ref_sheet_image = $rental_bill->getMedia('ref_sheet_image');
        $ref_sheet_image = get_medias_detail($ref_sheet_image);

        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line_ids)
            ->where('rental_lines.item_type', Product::class)
            // ->where('rental_lines.rental_bill_id', $rental_bill->id)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->get()->map(function ($car) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;
                return $car;
            });
        // dd($cars);
        // calculate summary
        $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill->id);
        $om = new OrderManagement($rental_bill);
        $om->setPromotion($rental_bill->promotion_code, $checked_vouchers);
        $om->setWithHoldingTaxVal($rental_bill->withholding_tax_value);
        $om->calculate();
        $summary = $om->getSummary();

        $tax_invoice_list = [];
        $check_customer_address = BOOL_TRUE;
        $rental_bill_customer_billing = RentalBill::where('id', $rental_bill_id)->select('id', 'check_customer_address', 'customer_billing_address_id')->first();
        if ($rental_bill_customer_billing) {
            $check_customer_address = $rental_bill_customer_billing->check_customer_address;
            if (strcmp($check_customer_address, BOOL_FALSE) == 0) {
                $tax_invoice_list = CustomerBillingAddress::where('id', $rental_bill_customer_billing->customer_billing_address_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        }
        $withholding_tax_list = RentalTrait::getWithHodingTaxList();

        $data = [
            'd' => $rental_bill,
            'rental_id' => $rental_id,
            'rental_bill_id' => $rental_bill_id,
            'rental_bill' => $rental_bill,
            'rental_line_list' => $rental_line_list,
            // 'payment_method_list' => $payment_method_list,
            'payment_gateway_name' => $payment_gateway_name,
            'payment_status_name' => $payment_status_name,
            'summary' => $summary,
            'ref_sheet_image' => $ref_sheet_image,
            'customer_type' => $customer_type,
            'cars' => $cars,
            'check_customer_address' => $check_customer_address,
            'tax_invoice_list' => $tax_invoice_list,
            'withholding_tax_list' => $withholding_tax_list
        ];
        if (!in_array($rental_bill->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING])) {
            $data['view'] = true;
            $data['redirect'] = route('admin.short-term-rental.alter.edit-bill', [
                'rental_id' => $rental_id,
            ]);
        }
        return view('admin.short-term-rental-alter-bill.form', $data);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        $rental_bills = RentalBill::where('rental_id', $rental_id)->where('bill_type', RentalBillTypeEnum::OTHER)->get();
        if ($rental_bills) {
            // TODO remove rental_bill
            /* foreach ($rental_bills as $key => $rental_bill) {
                $qtf = new QuotationFactory($rental_bill);
                $qtf->create();
            } */
        }

        $rental_lines = RentalLine::where('rental_id', $rental_id)->whereNotNull('former_car_id')->where('status', STATUS_ACTIVE)->get();
        $self_drive_types = [SelfDriveTypeEnum::SEND, SelfDriveTypeEnum::PICKUP];
        if (strcmp($rental->status, RentalStatusEnum::CHANGE) == 0) {
            if ($rental_lines) {
                $service_type_rental = $rental->serviceType->service_type;

                foreach ($rental_lines as $index => $rental_line) {
                    $ijf = new InspectionJobFactory($service_type_rental, null, $rental->id, $rental_line->car_id, [
                        'inspection_must_date_out' => $rental_line->pickup_date,
                        'inspection_must_date_in' => $rental_line->return_date,
                    ]);
                    $ijf->create();

                    if (strcmp($service_type_rental, ServiceTypeEnum::SELF_DRIVE) == 0) {
                        foreach ($self_drive_types as $self_drive_type) {
                            $driving_job = DrivingJob::where('job_id', $rental_id)->where('job_type', Rental::class)->where('car_id', $rental_line->former_car_id)->where('self_drive_type', $self_drive_type)->first();
                            if ($driving_job) {
                                $driving_job_log = new DrivingJobLog();
                                $driving_job_log->driving_job_id = $driving_job->id;
                                $driving_job_log->rental_id =  $rental_id;
                                $driving_job_log->car_id = $driving_job->car_id;
                                $driving_job_log->save();

                                $driving_job->car_id = $rental_line->car_id;
                                $driving_job->save();

                                $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
                                if ($car_park_transfer) {
                                    $car_park_transfer->car_id = $driving_job->car_id;
                                    $car_park_transfer->save();
                                }
                            }
                        }
                    } else {
                        $driving_job = DrivingJob::where('job_id', $rental_id)->where('job_type', Rental::class)->where('car_id', $rental_line->former_car_id)->first();
                        if ($driving_job) {
                            $driving_job_log = new DrivingJobLog();
                            $driving_job_log->driving_job_id = $driving_job->id;
                            $driving_job_log->rental_id =  $rental_id;
                            $driving_job_log->car_id = $driving_job->car_id;
                            $driving_job_log->save();

                            $driving_job->car_id = $rental_line->car_id;
                            $driving_job->save();

                            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
                            if ($car_park_transfer) {
                                $car_park_transfer->car_id = $driving_job->car_id;
                                $car_park_transfer->save();
                            }
                        }
                    }
                }
            }
            $rental->status = RentalStatusEnum::PREPARE;
            $rental->save();
        }
        // $rental->status = RentalStatusEnum::PAID;


        $redirect_route = route('admin.short-term-rentals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeRentalBill(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);

        $rental_bill = new RentalBill();
        $rental_bill->bill_type = RentalBillTypeEnum::OTHER;
        $rental_bill->subtotal = $request->bill_total;
        $rental_bill->total = $request->bill_total;
        $rental_bill->vat = $request->bill_vat;
        $rental_bill->rental_id = $rental_id;
        $rental_bill->check_customer_address = $request->is_customer_address;
        $rental_bill->customer_billing_address_id = $request->customer_billing_address_id;
        $rental_bill->status = RentalStatusEnum::PENDING;
        $rental_bill->save();

        $rental_lines = $request->rental_line;
        if ($rental_lines) {
            foreach ($rental_lines as $item_line) {
                $rental_line = new RentalLine();
                $rental_line->rental_id = $rental_id;
                $rental_line->rental_bill_id = $rental_bill->id;
                $rental_line->item_type = OrderLineTypeEnum::EXTRA;
                $rental_line->item_id = (string) Str::orderedUuid();
                $rental_line->name = $item_line['summary_name_i'];
                $rental_line->subtotal = $item_line['subtotal'];
                $rental_line->total = $item_line['total'];
                $rental_line->description = $item_line['summary_description_i'];
                $rental_line->amount = $item_line['amount'];
                $rental_line->car_id = $item_line['car_id'];
                $rental_line->pickup_date = $rental->pickup_date;
                $rental_line->return_date = $rental->return_date;
                $rental_line->save();
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function showBill($rental_bill_id, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental_bill = RentalBill::find($rental_bill_id);
        $rental_id = $rental_bill->rental_id;
        $rental = Rental::find($rental_bill->rental_id);
        $rental_line_ids = RentalLine::where('rental_id', $rental_bill->rental_id)->pluck('id');
        $customer_type = Customer::find($rental->customer_id);
        $customer_type = $customer_type->customer_type;
        $rental_line_list = RentalLine::leftjoin('cars', 'cars.id', '=', 'rental_lines.car_id')->where('rental_lines.rental_bill_id', $rental_bill_id)->select('rental_lines.*', 'cars.license_plate')->get();
        $rental_line_list->map(function ($item) {
            $name = in_array($item->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $item->name : $item->summary_display_name;
            $item->summary_name_i = $name;
            $description = in_array($item->item_type, [OrderLineTypeEnum::EXTRA, OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST]) ? $item->description : $item->summary_description;
            $item->summary_description_i = $description;
            return $item;
        });
        $payment_gateway_list = RentalTrait::getPaymentGatewayList();
        // dd($rental_bill);
        $ref_sheet_image = $rental_bill->getMedia('ref_sheet_image');
        $ref_sheet_image = get_medias_detail($ref_sheet_image);

        // calculate summary
        $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill->id);
        $om = new OrderManagement($rental_bill);
        $om->setPromotion($rental_bill->promotion_code, $checked_vouchers);
        $om->setWithHoldingTaxVal($rental_bill->withholding_tax_value);
        $om->calculate();
        $summary = $om->getSummary();

        $payment_gateway_name = null;
        $payment_status_name = null;
        if ($rental_bill->payment_gateway) {
            $payment_gateway_name = __('short_term_rentals.payment_gateway_' . $rental_bill->payment_gateway);
        }
        if ($rental_bill->status) {
            $payment_status_name = __('short_term_rentals.status_' . $rental_bill->status);
        }
        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line_ids)
            ->where('rental_lines.rental_bill_id', $rental_bill->id)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'cars.id as id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->get()->map(function ($car) {
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car->name = $car->license_plate;
                return $car;
            });

        $tax_invoice_list = [];
        $check_customer_address = BOOL_TRUE;
        $rental_bill_customer_billing = RentalBill::where('id', $rental_bill_id)->select('id', 'check_customer_address', 'customer_billing_address_id')->first();
        if ($rental_bill_customer_billing) {
            $check_customer_address = $rental_bill_customer_billing->check_customer_address;
            if (strcmp($check_customer_address, BOOL_FALSE) == 0) {
                $tax_invoice_list = CustomerBillingAddress::where('id', $rental_bill_customer_billing->customer_billing_address_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        }
        $withholding_tax_list = RentalTrait::getWithHodingTaxList();
        return view('admin.short-term-rental-alter-bill.form', [
            'view' => true,
            'd' => $rental_bill,
            'rental_id' => $rental_id,
            'rental_bill' => $rental_bill,
            'rental_bill_id' => $rental_bill_id,
            'rental_line_list' => $rental_line_list,
            'payment_method_list' => $payment_gateway_list,
            'summary' => $summary,
            'payment_gateway_name' => $payment_gateway_name,
            'payment_status_name' => $payment_status_name,
            'ref_sheet_image' => $ref_sheet_image,
            'customer_type' => $customer_type,
            'cars' => $cars,
            'check_customer_address' => $check_customer_address,
            'tax_invoice_list' => $tax_invoice_list,
            'withholding_tax_list' => $withholding_tax_list
        ]);
    }
}
