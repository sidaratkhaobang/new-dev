<?php

namespace App\Http\Controllers;

use App\Models\Creditor;
use App\Models\DealerCheckCar;
use App\Models\LongTermRental;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LongTermRentalSpecVendorController extends Controller
{
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $no_car_dealers = $request->no_car_dealer;
        $no_car_reasons = $request->no_car_reason;
        if (isset($no_car_dealers)) {
            foreach ($no_car_dealers as $tor_line_id => $val) {
                if (empty($no_car_reasons[$tor_line_id])) {
                    return $this->responseWithCode(false, __('lang.field_required') . __('long_term_rentals.no_car_reason'), null, 422);
                }
            }
        }

        if (isset($request->data2)) {
            foreach ($request->data2 as $tor_line_id => $data) {
                foreach ($data as $data2) {
                    if (empty($data2['amount'])) {
                        return $this->responseWithCode(false, __('lang.field_required') . __('long_term_rentals.car_amount'), null, 422);
                    }
                    if (empty($data2['delivery_month_year'])) {
                        return $this->responseWithCode(false, __('lang.field_required') . __('long_term_rentals.delivery_month_year'), null, 422);
                    }
                }
            }
        }
        if (!isset($no_car_dealers) && !isset($request->data2)) {
            return $this->responseWithCode(true, 'กรุณากรอกข้อมูลอย่างน้อยหนึ่งแถว', null, 422);
        }

        if ($no_car_dealers) {
            foreach ($no_car_dealers as $tor_line_id => $val) {
                DealerCheckCar::where('dealer_id', $request->dealer_id)
                    ->where('lt_rental_id', $request->rental_id)
                    ->where('tor_line_id', $tor_line_id)
                    ->delete();
                $dealer_check_car = new DealerCheckCar();
                $dealer_check_car->lt_rental_id = $request->rental_id;
                $dealer_check_car->dealer_id = $request->dealer_id;
                $dealer_check_car->tor_line_id = $tor_line_id;
                $dealer_check_car->amount = 0;
                $dealer_check_car->delivery_month_year = null;
                $dealer_check_car->remark = $no_car_reasons[$tor_line_id] ?? null;
                $dealer_check_car->response_date = Carbon::now();
                $dealer_check_car->is_ready_to_deliver = STATUS_DEFAULT;
                $dealer_check_car->save();
            }
        }
        if (isset($request->data2)) {
            foreach ($request->data2 as $tor_line_id => $data) {
                DealerCheckCar::where('dealer_id', $request->dealer_id)
                    ->where('lt_rental_id', $request->rental_id)
                    ->where('tor_line_id', $tor_line_id)
                    ->delete();
                foreach ($data as $data2) {
                    $date = date('Y-d-m', strtotime('01/' . $data2['delivery_month_year']));
                    $dealer_check_car = new DealerCheckCar();
                    $dealer_check_car->lt_rental_id = $request->rental_id;
                    $dealer_check_car->dealer_id = $request->dealer_id;
                    $dealer_check_car->tor_line_id = $tor_line_id;
                    $dealer_check_car->amount = $data2['amount'];
                    $dealer_check_car->delivery_month_year = $date;
                    $dealer_check_car->remark = $data2['remark'];
                    $dealer_check_car->response_date = Carbon::now();
                    $dealer_check_car->save();
                }
            }
        }
        return $this->responseValidateSuccess('');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(LongTermRental $rental, Creditor $dealer)
    {
        $tor_line_list = $this->getRentalTorLinesFromRentalId($rental->id);
        $rental_id = $rental->id;
        $dealer_id = $dealer->id;
        $tor_line_list->map(function ($item) use ($rental_id, $dealer_id) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->no_cars_checked = false;
            $item->no_cars_reason = '';
            $dealer_check_car_results = DealerCheckCar::where('lt_rental_id', $rental_id)
                ->where('tor_line_id', $item->id)->where('dealer_id', $dealer_id)
                ->get();
            $dealer_check_cars = $dealer_check_car_results->where('is_ready_to_deliver', STATUS_ACTIVE);
            $dealer_check_car_not_ready = $dealer_check_car_results->where('is_ready_to_deliver', STATUS_DEFAULT)->first();
            $dealer_check_cars->map(function ($item) use ($rental_id) {
                $item->delivery_month_year = $item->delivery_month_year ? date('m/Y', strtotime($item->delivery_month_year)) : '';
            });
            if (sizeof($dealer_check_cars) == 0) {
                $item->no_cars_checked = true;
                $item->no_car_reason = $dealer_check_car_not_ready ? $dealer_check_car_not_ready->remark : '';
            }
            $item->dealer_check_cars = $dealer_check_cars;
            $item->require_date_text = get_thai_date_format($item->actual_delivery_date, 'j F Y');
            $item->customer_require = date("m/Y", strtotime($item->actual_delivery_date));
        });

        $page_title = __('long_term_rentals.dealer_check');
        return view('admin.long-term-rental-specs.car-check-vendor-form', [
            'rental' => $tor_line_list,
            'page_title' => $page_title,
            'dealer' => $dealer,
            'rental_id' => $rental_id,
        ]);
    }

    public function getRentalTorLinesFromRentalId($long_term_rental_id)
    {
        return LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tor_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
                'lt_rentals.actual_delivery_date'
            )
            ->where('lt_rentals.id', $long_term_rental_id)
            ->orderBy('tor_id')
            ->get();
    }

    public function getAccessoriesByTorLineId($lt_rental_tor_line_id)
    {
        return LongTermRentalTorLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'lt_rental_tor_line_accessories.accessory_id')
            ->where('lt_rental_tor_line_accessories.lt_rental_tor_line_id', $lt_rental_tor_line_id)
            ->select(
                'lt_rental_tor_line_accessories.*',
                'lt_rental_tor_line_accessories.amount as amount_accessory',
                'lt_rental_tor_line_accessories.amount_per_car as amount_per_car_accessory',
                'accessories.name as accessory_text'
            )
            ->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}