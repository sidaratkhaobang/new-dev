<?php

namespace App\Http\Controllers\admin;

use App\Enums\Actions;
use App\Enums\OrderChannelEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShortTermRentalChannelController extends Controller
{
    use RentalTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $validator = Validator::make($request->all(), [
            'order_channel' => ['required'],
            'type_package' => ['required'],
            'payment_channel' => ['required']
        ], [], [
            'order_channel' => __('short_term_rentals.order_channel'),
            'type_package' => __('short_term_rentals.type_package'),
            'payment_channel' => __('short_term_rentals.payment_channel'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $rental = Rental::findOrFail($request->rental_id);
        $rental->order_channel = $request->order_channel;
        $rental->type_package = $request->type_package;
        $rental->payment_channel = $request->payment_channel;
        $rental->save();
        $redirect_route = route('admin.short-term-rental.info.edit', ['rental_id' => $rental->id]);
        return $this->responseValidateSuccess($redirect_route);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Rental $short_term_rental_channel)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');
        $order_channel_list = $this->getOrderChannelList();
        $package_type_list = $this->getPackageTypeList();
        $payment_type_list = $this->getPaymentTypeList();
        return view('admin.short-term-rental-channel.form', [
            'd' => $short_term_rental_channel,
            'page_title' => $page_title,
            'rental_id' => $short_term_rental_channel?->id,
            'order_channel_list' => $order_channel_list,
            'package_type_list' => $package_type_list,
            'payment_type_list' => $payment_type_list,
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
