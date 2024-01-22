<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\QuickPay;
use App\Classes\RentalCarManagement;
use App\Enums\Actions;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Amphure;
use App\Models\District;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Enums\TransferTypeEnum;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Product;
use App\Models\Province;
use App\Models\RentalBill;
use App\Models\ServiceType;
use App\Traits\RentalTrait;
use App\Traits\CustomerTrait;
use App\Models\CustomerBillingAddress;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Models\DrivingJob;
use App\Models\Quotation;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShortTermRentalController extends Controller
{
    use RentalTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $branch_id = $request->branch_id;
        $worksheet_id = $request->worksheet_id;
        $customer_id = $request->customer_id;
        $service_type_id = $request->service_type_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $status_id = $request->status;
        $lists = Rental::sortable(['worksheet_no' => 'desc'])
            ->with(['quotations'])
            ->leftJoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->select('rentals.*', 'branches.name as branch_name', 'service_types.name as service_type_name')
            ->search($request->s, $request)->paginate(PER_PAGE);

        // dd($lists);
        $branch_lists = Branch::all();
        $worksheet_lists = Rental::select('id', 'worksheet_no as name')->get();
        $customer_lists = Rental::select('id', 'customer_name as name')->get();
        $service_type_lists = ServiceType::all();
        $status_list = RentalTrait::getStatusShortTermRentalList();
        $model = Rental::class;
        return view('admin.short-term-rentals.index', [
            'lists' => $lists,
            'branch_id' => $branch_id,
            'worksheet_id' => $worksheet_id,
            'customer_id' => $customer_id,
            'service_type_id' => $service_type_id,
            'service_type_lists' => $service_type_lists,
            'worksheet_lists' => $worksheet_lists,
            's' => $request->s,
            'branch_lists' => $branch_lists,
            'customer_lists' => $customer_lists,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'model' => $model,
            'status_list' => $status_list,
            'status_id' => $status_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
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
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Rental $short_term_rental)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $rental_id = $short_term_rental->id;
        $d = Rental::find($rental_id);
        if (empty($d)) {
            return redirect()->route('admin.short-term-rentals.index');
        }
        $order_channel_list = $this->getOrderChannelList();

        // branch
        $branch_name = null;
        if (empty($d->branch_id)) {
            $d->branch_id = Auth::user()?->branch_id;
            $branch_name = find_name_by_id($d->branch_id, Branch::class);
        }
        $branch_list = RentalTrait::getBranchOfServiceType($d->service_type_id);

        // product
        $product_name = find_name_by_id($d->product_id, Product::class);
        $product = Product::find($d->product_id);

        // customer
        $customer_name = null;
        if ($d->customer_id) {
            $customer = Customer::find($d->customer_id);
            $customer_name = ($customer) ? $customer->customer_code . ' - ' . $customer->name : null;
            $d->customer_type = ($customer) ? $customer->customer_type : null;
        }
        $customer_type_list = CustomerTrait::getCustomerType();
        $province_list = Province::select('id', 'name_th as name')->get();
        $branch_office_list = RentalTrait::getBranchOfficeList();

        // location
        $origin_name = find_name_by_id($d->origin_id, Location::class);
        $destination_name = find_name_by_id($d->destination_id, Location::class);
        if ($d->origin_name) {
            $d->origin_id = ADDITIONAL;
            $origin_name = $d->origin_name;
        }
        if ($d->destination_name) {
            $d->destination_id = ADDITIONAL;
            $destination_name = $d->destination_name;
        }

        // check_customer_address
        $use_customer_billing_address = ($d->customer_billing_address_id ? true : false);
        $is_required_tax_invoice = boolval($d->is_required_tax_invoice);
        $check_customer_address = (boolval($d->check_customer_address));
        $customer_billing_province_name = find_name_by_id($d->customer_billing_province_id, Province::class, 'name_th');
        $customer_billing_district_name = find_name_by_id($d->customer_billing_district_id, Amphure::class, 'name_th');
        $customer_billing_subdistrict_name = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'name_th');
        $customer_billing_zipcode = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'zip_code');
        $customer_district_name = find_name_by_id($d->customer_district_id, Amphure::class, 'name_th');
        $customer_subdistrict_name = find_name_by_id($d->customer_subdistrict_id, District::class, 'name_th');
        $customer_zipcode = find_name_by_id($d->customer_subdistrict_id, District::class, 'zip_code');
        $page_title = __('lang.view') . __('short_term_rentals.sheet');
        return view('admin.short-term-rental-alter.form', [
            'view' => true,
            'd' => $d,
            'branch_name' => $branch_name,
            'product_name' => $product_name,
            'origin_name' => $origin_name,
            'destination_name' => $destination_name,
            'customer_name' => $customer_name,
            'product' => $product,
            'rental_id' => $rental_id,
            'service_type_id' => $d->service_type_id,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'branch_office_list' => $branch_office_list,
            'branch_list' => $branch_list,
            'use_customer_billing_address' => $use_customer_billing_address,
            'is_required_tax_invoice' => $is_required_tax_invoice,
            'page_title' => $page_title,
            'customer_zipcode' => $customer_zipcode,
            'check_customer_address' => $check_customer_address,
            'customer_billing_province_name' => $customer_billing_province_name,
            'customer_billing_district_name' => $customer_billing_district_name,
            'customer_billing_subdistrict_name' => $customer_billing_subdistrict_name,
            'customer_billing_zipcode' => $customer_billing_zipcode,
            'customer_district_name' => $customer_district_name,
            'customer_subdistrict_name' => $customer_subdistrict_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental = Rental::find($id);
        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }
        if (in_array($rental->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING])) {
            if (strcmp($rental->rental_state, RentalStateEnum::INFO) == 0) {
                return redirect()->route('admin.short-term-rental.info.edit', ['rental_id' => $rental->id]);
            } else if (strcmp($rental->rental_state, RentalStateEnum::ASSET) == 0) {
                $rental_bill = RentalBill::where('rental_id', $rental->id)->first();
                if ($rental_bill) {
                    return redirect()->route('admin.short-term-rental.asset.edit', ['rental_id' => $rental->id, 'rental_bill_id' => $rental_bill->id]);
                } else {
                    return redirect()->route('admin.short-term-rentals.index');
                }
            } else if (strcmp($rental->rental_state, RentalStateEnum::DRIVER) == 0) {
                return redirect()->route('admin.short-term-rental.driver.edit', ['rental_id' => $rental->id]);
            } else if (strcmp($rental->rental_state, RentalStateEnum::PROMOTION) == 0) {
                $rental_bill = RentalBill::where('rental_id', $rental->id)->first();
                // return redirect()->route('admin.short-term-rental.bill.edit', ['rental_id' => $rental->id]);
                return redirect()->route('admin.short-term-rental.promotion.edit', ['rental_id' => $rental->id]);
            } else if (strcmp($rental->rental_state, RentalStateEnum::SUMMARY) == 0) {
                // return redirect()->route('admin.short-term-rental.bill.edit', ['rental_id' => $rental->id]);
                $rental_bill = RentalBill::where('rental_id', $rental->id)->first();
                return redirect()->route('admin.short-term-rental.summary.edit', ['rental_id' => $rental->id]);
            }
        } else {
            return redirect()->route('admin.short-term-rental.alter.edit', ['rental_id' => $rental->id]);
        }

        // undefined
        return redirect()->route('admin.short-term-rentals.index');
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

    public function updateStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
            ]);
        }
        if ($request->status) {
            $rental->status = $request->status;
            $rental->save();
        }
        return response()->json([
            'success' => 'ok',
            'message' => __('lang.delete_success'),
            'redirect' => $request->redirect_route
        ]);
    }

    public function getAvailableCars(Request $request)
    {
        $data = RentalTrait::getCarRentalTimeLine($request);
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!empty($value->timelines)) {
                    foreach ($value->timelines as $key_timelines => $value_timelines) {
                        $pickup_date = Carbon::createFromFormat('Y-m-d H:i:s', $value_timelines->pickup_date)->format('H:i');
                        $return_date = Carbon::createFromFormat('Y-m-d H:i:s', $value_timelines->return_date)->format('H:i');
                        $value->timelines[$key_timelines]['pickup_hours'] = $pickup_date;
                        $value->timelines[$key_timelines]['return_hours'] = $return_date;
                    }
                }
            }
        }

        /* $rm = new RentalCarManagement($request->service_type_id);
        $data = $rm->getRentalCars($request->service_type_id, $request->product_id); */
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function getAssetCars(Request $request)
    {
        $service_type_id = $request->service_type_id;
        $product_id = $request->product_id;
        $car_brand_id = $request->brand_id;
        $optionals = [];
        if ($car_brand_id) {
            $optionals['car_brand_id'] = $car_brand_id;
        }
        $rm = new RentalCarManagement($service_type_id);
        $car_list = $rm->getRentalCars($service_type_id, $product_id, $optionals);
        $car_list = RentalTrait::formatCarList($car_list);
        return [
            'success' => true,
            'data' => $car_list
        ];
    }

    public function getAvailableCarSpares(Request $request)
    {
        $data = RentalTrait::getCarRentalSpareTimeLine($request);
        // $data = RentalTrait::getAvailableCars($request);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function getPromotionDetail(Request $request)
    {
        $data = RentalTrait::getPromotionDiscount($request);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function gen2c2pPaymentLink(Request $request)
    {
        $rental = Rental::find($request->id);
        if (!$rental) {
            return response()->json([
                'success' => false,
                'data' => DATA_NOT_FOUND
            ]);
        }

        $quotation = $rental->quotationPrimary;

        if (empty($quotation) || strcmp($rental->status, RentalStatusEnum::PENDING) != 0) {
            return [
                'success' => false,
                'data' => DATA_NOT_FOUND
            ];
        }

        $url = RentalTrait::generateQuickpayUrl($rental, $quotation);
        return [
            'success' => true,
            'data' => $url
        ];
    }

    public function showCalendar(Request $request)
    {
        $status_list = RentalTrait::getRentalStatusList();
        $rm = new RentalCarManagement(null);
        $car_list = $rm->getRentalCars(null, null);
        $car_list = RentalTrait::formatCarList($car_list);
        return view('admin.short-term-rentals.calendar', [
            'status_list' => $status_list,
            'car_list' => $car_list,
        ]);
    }

    public function getCalendar(Request $request)
    {
        $rental_line = RentalLine::find($request->id);
        $rental = Rental::leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->leftjoin('rental_lines', 'rental_lines.rental_id', '=', 'rentals.id')
            ->leftjoin('cars', 'cars.id', '=', 'rental_lines.car_id')
            ->leftjoin('locations as origin', 'origin.id', '=', 'rentals.origin_id')
            ->leftjoin('locations as destination', 'destination.id', '=', 'rentals.destination_id')
            ->leftjoin('customers', 'customers.id', '=', 'rentals.customer_id')
            ->where('rental_lines.id', $rental_line->id)
            // ->where('rental_lines.status', STATUS_ACTIVE)
            ->select(
                'rentals.worksheet_no',
                'products.name as product_name',
                'products.sku as product_sku',
                'cars.license_plate',
                DB::raw('DATE_FORMAT(rentals.pickup_date, "%d/%m/%Y %H:%i") as pickup_date'),
                DB::raw('DATE_FORMAT(rentals.return_date, "%d/%m/%Y %H:%i") as return_date'),
                'origin.name as origin_name',
                'destination.name as destination_name',
                'customers.name as customer_name',
                'customers.tel as customer_tel'
            )->first();
        return response()->json($rental);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental = Rental::find($id);
        $rental->delete();

        $driving_job = DrivingJob::where('job_id', $id)->get();
        foreach ($driving_job as $dj) {
            $dj->delete();
        }


        return $this->responseComplete();
    }

    public function getCarRentalsByMonthYear(Request $request)
    {
        $rentals = RentalTrait::getCarRentalByMonthYear($request->id, $request->month, $request->year);
        $html = view('admin.short-term-rentals.components.timeline', [
            'data' => $rentals
        ])->render();
        return [
            'success' => true,
            'id' => $request->id,
            'data' => $rentals,
            'html' => $html
        ];
    }
}
