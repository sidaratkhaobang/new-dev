<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\OrderChannelEnum;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ShortTermRentalServiceTypeController extends Controller
{
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $d = new Rental();
        $d->createdBy = Auth::user();
        $d->created_at = date('Y-m-d H:i:s');
        $service_types = ServiceType::all();
        $page_title = __('short_term_rentals.add_new');
        return view('admin.short-term-rental-service-types.form', [
            'page_title' => $page_title,
            'service_types' => $service_types,
            'd' => $d,
        ]);
    }

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $d = Rental::find($request->rental_id);
        $service_types = ServiceType::all();
        $page_title = __('short_term_rentals.add_new');
        return view('admin.short-term-rental-service-types.form', [
            'page_title' => $page_title,
            'service_types' => $service_types,
            'd' => $d,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $validator = Validator::make($request->all(), [
            'service_type_id' => [
                'required'
            ]
        ], [], [
            'service_type_id' => __('short_term_rentals.service_type'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental = Rental::firstOrNew(['id' => $request->id]);
        if (!($rental->exists)) {
            $rental->worksheet_no = generate_worksheet_no(Rental::class);
        }
        $rental->service_type_id = $request->service_type_id;
        if ($rental->service_type_id != $request->service_type_id) {
            $rental->product_id = null;
            $rental->pickup_date = null;
            $rental->return_date = null;
        }
        $rental->branch_id = get_branch_id();
        $rental->rental_type = RentalTypeEnum::SHORT;
        $rental->order_channel = OrderChannelEnum::SMARTCAR;
        $rental->rental_state = RentalStateEnum::INFO;
        $rental->status = RentalStatusEnum::DRAFT;
        $rental->save();

        $redirect_route = route('admin.short-term-rental-channel.edit', ['short_term_rental_channel' => $rental->id]);
        return $this->responseValidateSuccess($redirect_route);
    }
}
