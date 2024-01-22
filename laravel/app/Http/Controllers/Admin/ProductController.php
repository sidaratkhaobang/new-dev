<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CalculateTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarType;
use App\Models\GLAccount;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductAdditionalRelation;
use App\Models\ProductCarClass;
use App\Models\ProductCarType;
use App\Models\ProductGLAccount;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Product);
        $sku = $request->sku;
        $name = $request->name;
        $branch_id = $request->branch_id;
        $service_type_id = $request->service_type_id;
        $list = Product::leftjoin('branches', 'branches.id', '=', 'products.branch_id')
            ->leftjoin('service_types', 'service_types.id', '=', 'products.service_type_id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.standard_price',
                'branches.name as branch_name',
                'service_types.name as service_type_name',
                'products.status',
            )
            ->when(!empty($sku), function ($query) use ($sku) {
                return $query->where('products.id', $sku);
            })
            ->when(!empty($name), function ($query) use ($name) {
                return $query->where('products.id', $name);
            })
            ->when(!empty($service_type_id), function ($query) use ($service_type_id) {
                return $query->where('products.service_type_id', $service_type_id);
            })
            ->when(!empty($branch_id), function ($query) use ($branch_id) {
                return $query->where('products.branch_id', $branch_id);
            })
            ->sortable('sku')
            ->search($request->s)
            ->paginate(PER_PAGE);

        $name_list = Product::select('id', 'name')->get();
        $sku_list = Product::select('id', 'sku as name')->get();
        $service_type_list = ServiceType::select('id', 'name')->get();
        $branch_list = Branch::select('id', 'name')->get();
        return view('admin.products.index', [
            'list' => $list,
            's' => $request->s,
            'sku' => $sku,
            'name' => $name,
            'service_type_id' => $service_type_id,
            'branch_id' => $branch_id,
            'name_list' => $name_list,
            'sku_list' => $sku_list,
            'service_type_list' => $service_type_list,
            'branch_list' => $branch_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $d = new Product();
        $yes_no_list = getYesNoList();
        $product_additionals = ProductAdditional::all();
        $service_types = ServiceType::all();
        $branches = Branch::all();
        $days = getDayCollection();
        $calculate_type_list = $this->getCalculateTypeList();
        $status_list = $this->getStatusList();
        $gl_account_list = GLAccount::select('name', 'id')->get();
        $car_type_list = CarType::select('name', 'id')->get();
        $gl_account = [];
        $car_type = [];

        $page_title = __('lang.create') . __('products.page_title');
        return view('admin.products.form', [
            'd' => $d,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'product_additionals' => $product_additionals,
            'service_types' => $service_types,
            'branches' => $branches,
            'days' => $days,
            'calculate_type_list' => $calculate_type_list,
            'status_list' => $status_list,
            'booking_day_arr' => [],
            'gl_account_list' => $gl_account_list,
            'gl_account' => $gl_account,
            'car_type' => $car_type,
            'car_type_list' => $car_type_list,
        ]);
    }

    public function show(Product $product)
    {
        $this->authorize(Actions::View . '_' . Resources::Product);
        $yes_no_list = getYesNoList();
        $product_additionals = ProductAdditional::all();
        $service_types = ServiceType::all();
        $branches = Branch::all();
        $days = getDayCollection();
        $calculate_type_list = $this->getCalculateTypeList();
        $status_list = $this->getStatusList();
        $gl_account_list = GLAccount::select('name', 'id')->get();
        $gl_account = $this->getGLAccountArray($product->id);
        $car_type_list = CarType::select('name', 'id')->get();
        $booking_day_arr = [];
        $car_type = $this->getCarTypeArray($product->id);
        foreach ($days as $day) {
            $booking_day = 'booking_day_' . $day['value'];
            if ($product->$booking_day == STATUS_ACTIVE) {
                array_push($booking_day_arr, $day['value']);
            }
        }
        $product_additional_list = ProductAdditionalRelation::leftjoin('product_additionals', 'product_additionals.id', '=', 'products_addtionals_relation.product_addtional_id')
            ->select(
                'products_addtionals_relation.id',
                'product_additionals.id as product_additional_id',
                'product_additionals.name as product_additional_text',
                'product_additionals.price',
                'products_addtionals_relation.amount',
                'products_addtionals_relation.is_free',
            )
            ->where('products_addtionals_relation.product_id', $product->id)
            ->get();

        $page_title = __('lang.view') . __('products.page_title');
        return view('admin.products.form', [
            'd' => $product,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'product_additionals' => $product_additionals,
            'service_types' => $service_types,
            'branches' => $branches,
            'days' => $days,
            'calculate_type_list' => $calculate_type_list,
            'status_list' => $status_list,
            'booking_day_arr' => $booking_day_arr,
            'product_additional_list' => $product_additional_list,
            'view' => true,
            'gl_account_list' => $gl_account_list,
            'gl_account' => $gl_account,
            'car_type' => $car_type,
            'car_type_list' => $car_type_list,
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $yes_no_list = getYesNoList();
        $product_additionals = ProductAdditional::all();
        $service_types = ServiceType::all();
        $branches = Branch::all();
        $days = getDayCollection();
        $calculate_type_list = $this->getCalculateTypeList();
        $status_list = $this->getStatusList();
        $gl_account_list = GLAccount::select('name', 'id')->get();
        $gl_account = $this->getGLAccountArray($product->id);
        $car_type_list = CarType::select('name', 'id')->get();
        $booking_day_arr = [];
        $car_type = $this->getCarTypeArray($product->id);
        foreach ($days as $day) {
            $booking_day = 'booking_day_' . $day['value'];
            if ($product->$booking_day == STATUS_ACTIVE) {
                array_push($booking_day_arr, $day['value']);
            }
        }
        $product_additional_list = ProductAdditionalRelation::leftjoin('product_additionals', 'product_additionals.id', '=', 'products_addtionals_relation.product_addtional_id')
            ->select(
                'products_addtionals_relation.id',
                'product_additionals.id as product_additional_id',
                'product_additionals.name as product_additional_text',
                'product_additionals.price',
                'products_addtionals_relation.amount',
                'products_addtionals_relation.is_free',
            )
            ->where('products_addtionals_relation.product_id', $product->id)
            ->get();

        $page_title = __('lang.edit') . __('products.page_title');
        return view('admin.products.form', [
            'd' => $product,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'product_additionals' => $product_additionals,
            'service_types' => $service_types,
            'branches' => $branches,
            'days' => $days,
            'calculate_type_list' => $calculate_type_list,
            'status_list' => $status_list,
            'booking_day_arr' => $booking_day_arr,
            'product_additional_list' => $product_additional_list,
            'gl_account_list' => $gl_account_list,
            'gl_account' => $gl_account,
            'car_type' => $car_type,
            'car_type_list' => $car_type_list,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $request->merge(['reserve_booking_duration' => intval(preg_replace("/[^0-9]/", '', $request->all()['reserve_booking_duration']))]);
        $request->merge(['fix_days' => intval(preg_replace("/[^0-9]/", '', $request->fix_days))]);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                // Rule::unique('products', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'calculate_type' => ['required'],
            'fix_days' => ['required_if:calculate_type,=,' . CalculateTypeEnum::FIXED, 'integer', 'min:0', 'max:100'],
            'standard_price' => ['required'],
            'status' => ['required'],
            'is_used_application' => ['required'],
            'reserve_booking_duration' => ['nullable', 'integer', 'min:0', 'max:100'],
        ], [], [
            'name' => __('products.name'),
            'sku' => __('products.sku'),
            'calculate_type' => __('products.calculate_type'),
            'standard_price' => __('products.standard_price'),
            'status' => __('lang.status'),
            'is_used_application' => __('products.is_used_application'),
            'fix_days' => __('products.fix_days'),
            'reserve_booking_duration' => __('products.reserve_booking_duration'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $product = Product::firstOrNew(['id' => $request->id]);
        $product->name = $request->name;
        if (!$product->sku) {
            $product->sku = 'TBD';
        }
        $product->calculate_type = $request->calculate_type;
        $product->service_type_id = $request->service_type_id;
        $standard_price = str_replace(',', '', $request->standard_price);
        $product->standard_price = $standard_price;
        $product->branch_id = $request->branch_id;

        $product->booking_day_sun = 0;
        $product->booking_day_mon = 0;
        $product->booking_day_tue = 0;
        $product->booking_day_wed = 0;
        $product->booking_day_thu = 0;
        $product->booking_day_fri = 0;
        $product->booking_day_sat = 0;
        if ($request->reserve_date) {
            foreach ($request->reserve_date as $key => $value) {
                $booking_day = 'booking_day_' . $value;
                $product->$booking_day = STATUS_ACTIVE;
            }
        }
        $product->start_booking_time = $request->start_booking_time;
        $product->end_booking_time = $request->end_booking_time;
        $product->reserve_booking_duration = $request->reserve_booking_duration;
        $product->start_date = $request->start_date;
        $product->end_date = $request->end_date;
        $product->status = $request->status;
        $product->is_used_application = $request->is_used_application;
        $product->fix_days = intval($request->fix_days);
        $product->fix_return_time = $request->fix_return_time;
        $product->save();

        if ($product->id) {
            $this->saveProductAdditionals($request, $product->id);
            $products_gl_accounts = $this->saveCustomerGroupRelation($request, $product->id);
            $products_car_type = $this->saveCarTypeGroupRelation($request, $product->id);
        }

        $redirect_route = route('admin.products.index');
        if ($request->set_price) {
            $redirect_route = route('admin.product-prices.index', ['product_id' => $product->id]);
        }
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getGLAccountArray($products)
    {
        return ProductGLAccount::leftJoin('gl_accounts', 'gl_accounts.id', '=', 'products_gl_accounts.gl_account_id')
            ->select('gl_accounts.id as id', 'gl_accounts.name as name')
            ->where('products_gl_accounts.product_id', $products)
            ->pluck('products.id')
            ->toArray();
    }

    public function getCarTypeArray($products)
    {
        return ProductCarType::leftJoin('car_types', 'car_types.id', '=', 'products_car_types.car_type_id')
            ->select('car_types.id as id', 'car_types.name as name')
            ->where('products_car_types.product_id', $products)
            ->pluck('products.id')
            ->toArray();
    }

    private function saveCustomerGroupRelation($request, $product_id)
    {
        ProductGLAccount::where('product_id', $product_id)->delete();
        if (!empty($request->gl_account)) {
            foreach ($request->gl_account as $gl_account) {
                $products_gl_accounts = new ProductGLAccount();
                $products_gl_accounts->product_id = $product_id;
                $products_gl_accounts->gl_account_id = $gl_account;
                $products_gl_accounts->save();
            }
        }
        return true;
    }

    private function saveCarTypeGroupRelation($request, $product_id)
    {
        ProductCarType::where('product_id', $product_id)->delete();
        if (!empty($request->car_type)) {
            foreach ($request->car_type as $car_type) {
                $products_car_type = new ProductCarType();
                $products_car_type->product_id = $product_id;
                $products_car_type->car_type_id = $car_type;
                $products_car_type->save();
            }
        }
        return true;
    }

    private function getCalculateTypeList()
    {
        return collect([
            [
                'id' => CalculateTypeEnum::HOURLY,
                'value' => CalculateTypeEnum::HOURLY,
                'name' => __('products.calculate_type_' . CalculateTypeEnum::HOURLY),
            ],
            [
                'id' => CalculateTypeEnum::DAILY,
                'value' => CalculateTypeEnum::DAILY,
                'name' => __('products.calculate_type_' . CalculateTypeEnum::DAILY),
            ],
            [
                'id' => CalculateTypeEnum::FIXED,
                'value' => CalculateTypeEnum::FIXED,
                'name' => __('products.calculate_type_' . CalculateTypeEnum::FIXED),
            ],
            [
                'id' => CalculateTypeEnum::MONTHLY,
                'value' => CalculateTypeEnum::MONTHLY,
                'name' => __('products.calculate_type_' . CalculateTypeEnum::MONTHLY),
            ],
        ]);
    }

    private function getStatusList()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => __('lang.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => __('lang.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function saveProductAdditionals($request, $product_id)
    {
        ProductAdditionalRelation::where('product_id', $product_id)->delete();
        if (!empty($request->product_additionals)) {
            foreach ($request->product_additionals as $item) {
                $product_additional = new ProductAdditionalRelation();
                $product_additional->product_id = $product_id;
                $product_additional->product_addtional_id = $item['product_additional_id'];
                $product_additional->amount = trim($item['amount']) ?? 1;
                $product_additional->is_free = $item['is_free'] ?? false;
                $product_additional->save();
            }
        }
        return true;
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $product = Product::find($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
