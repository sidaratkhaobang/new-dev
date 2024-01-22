<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\RentalBill;
use Illuminate\Http\Request;

class ShortTermRentalBillSummaryController extends Controller
{
    public function edit(Request $request)
    {
        //TODO
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;

        // $rental = Rental::find($rental_id);
        // $customer_type = Customer::find($rental->customer_id);
        // $customer_type = $customer_type->customer_type;

        $d = Rental::findOrFail($rental_id);
        $list = RentalBill::where('rental_id', $rental_id)->get();

        // $payment_method_list = $this->getPaymentMethodList();

        return view('admin.short-term-rental-bill-summary.index', [
            'd' => $d,
            'rental_id' => $rental_id,
            // 'rental_bill_id' => $rental_bill_id,
            'list' => $list,
            // 'customer_type' => $customer_type,
            // 'payment_method_list' => $payment_method_list,
            // 'summary' => $summary
        ]);
    }
}
