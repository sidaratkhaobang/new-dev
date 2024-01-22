<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderLineTypeEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Factories\QuotationFactory;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShortTermRentalBillController extends Controller
{
    use RentalTrait;
    const PRIMARY_BILL_CAN_NOT_UPDATE = 'PRIMARY_BILL_CAN_NOT_UPDATE';
    const PAID_BILL = 'PAID_BILL';
    const LINE_NOT_FOUND = 'LINE_NOT_FOUND';
    const LINE_ID_MISSING = 'LINE_ID_MISSING';

    public function index(Request $request)
    {
        $s = $request->s;
        $list = RentalBill::select(
            'rental_bills.id as id',
            'rental_bills.worksheet_no as worksheet_no',
            'rental_bills.bill_type as bill_type',
            'rental_bills.rental_id as rental_id',
            'rental_bills.subtotal as subtotal',
            'rental_bills.discount as discount',
            'rental_bills.coupon_discount as coupon_discount',
            'rental_bills.vat as vat',
            'rental_bills.total as total',
            'rental_bills.payment_method as payment_method',
            'rental_bills.payment_remark as payment_remark',
            'rental_bills.status as status'
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('rental_bills.worksheet_no', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = RentalBill::where('id', $request->id)
            ->select(
                'rental_bills.id as id',
                'rental_bills.worksheet_no as worksheet_no',
                'rental_bills.bill_type as bill_type',
                'rental_bills.rental_id as rental_id',
                'rental_bills.subtotal as subtotal',
                'rental_bills.discount as discount',
                'rental_bills.coupon_discount as coupon_discount',
                'rental_bills.vat as vat',
                'rental_bills.total as total',
                'rental_bills.payment_method as payment_method',
                'rental_bills.payment_remark as payment_remark',
                'rental_bills.status as status'
            )->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $lines = RentalLine::where('rental_bill_id', $data->id)
            ->select(
                'id',
                'name',
                'description',
                'item_type',
                'item_id',
                'is_free',
                'is_from_promotion',
                'is_from_coupon',
                'car_id',
                'amount',
                'subtotal',
                'discount',
                'vat',
                'total'
            )
            ->get()->toArray();
        $data->lines = $lines;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {
        $lines = $request->lines;
        $rental_id = $request->rental_id;
        $payment_method = $request->payment_method;
        $payment_remark = $request->payment_remark;

        $validator = Validator::make($request->all(), [
            'rental_id' => ['required', 'string'],
            'lines' => ['sometimes', 'array', 'min:1'],

        ], [], [
            'rental_id' => __('short_term_rentals.short_term_rental_id'),
            'lines' => __('short_term_rentals.lines'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $rental = Rental::find($rental_id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $rental_bill = new RentalBill();
        $rental_bill->rental_id = $rental->id;
        $rental_bill->payment_method = $payment_method;
        $rental_bill->payment_remark = $payment_remark;
        $rental_bill->status = RentalStatusEnum::PENDING;
        $rental_bill->bill_type = RentalBillTypeEnum::OTHER;
        $rental_bill->subtotal = 0;
        $rental_bill->total = 0;
        $rental_bill->vat = 0;
        $rental_bill->save();

        $bill_subtotal = 0;
        $bill_total = 0;
        if (is_array($lines) && sizeof($lines) > 0) {
            foreach ($lines as $key => $item) {
                $rental_line = new RentalLine();
                $rental_line->rental_id = $rental->id;
                $rental_line->rental_bill_id = $rental_bill->id;
                $rental_line->item_type = OrderLineTypeEnum::EXTRA;
                $rental_line->item_id = (string) Str::orderedUuid();
                $rental_line->name = $item['name'] ?? '';
                $rental_line->description = $item['description'] ?? '';
                $amount = intval($item['amount'] ?? 0);
                $subtotal = floatval($item['subtotal'] ?? 0);
                $rental_line->amount = $amount;
                $rental_line->subtotal = $subtotal;
                $rental_line->total = $subtotal * $amount;
                $rental_line->save();
                $bill_subtotal += $subtotal;
                $bill_total += $subtotal * $amount;
                $rental_line->pickup_date = $rental->pickup_date;
                $rental_line->return_date = $rental->return_date;
            }
        }
        $rental_bill->subtotal = $bill_subtotal;
        $rental_bill->vat = ($bill_total * 7) / 107;
        $rental_bill->total = $bill_total;
        $rental_bill->save();

        // $quotation = $this->createRentalQuotation($rental, $rental_bill);
        // $this->saveRentalQuotationLines($rental_bill->id, $quotation->id);
        // TODO remove rental_bill
        /* $qtf = new QuotationFactory($rental_bill);
        $qtf->create(); */
        return $this->responseWithCode(true, DATA_SUCCESS, $rental_bill->id, 200);
    }

    public function update(Request $request)
    {
        $lines = $request->lines;
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
        ], [], [
            'id' => __('short_term_rentals.rental_bill_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental_bill = RentalBill::find($request->id);
        if (!$rental_bill) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        if (strcmp($rental_bill->bill_type, RentalBillTypeEnum::PRIMARY) === 0) {
            return $this->responseWithCode(false, self::PRIMARY_BILL_CAN_NOT_UPDATE, null, 422);
        }
        if (in_array($rental_bill->STATUS, [RentalStatusEnum::PAID])) {
            return $this->responseWithCode(false, self::PAID_BILL, null, 422);
        }

        $rental_bill->fill($request->all());
        $rental_bill->save();

        if (is_array($lines) && sizeof($lines) > 0) {
            foreach ($lines as $key => $item) {
                if (!isset($item['id'])) {
                    return $this->responseWithCode(false, self::LINE_ID_MISSING, null, 422);
                }
                $rental_line = RentalLine::find($item['id']);
                if (!$rental_line) {
                    return $this->responseWithCode(false, self::LINE_NOT_FOUND, null, 422);
                }
                $rental_line->update($item);
            }
            $total = RentalLine::where('rental_bill_id', $rental_bill->id)->sum('total');
            $rental_bill->subtotal = $total;
            $rental_bill->vat = ($total * 7) / 107;
            $rental_bill->total = $total;
            $rental_bill->save();
        }
        $rental = Rental::find($rental_bill->rental_id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $this->updateQuotation($rental_bill, $rental);
        return $this->responseWithCode(true, DATA_SUCCESS, $rental_bill->id, 200);
    }


    function updateQuotation($rental_bill, $rental)
    {
        $user = Auth::user();
        $quotation = Quotation::firstOrNew(['rental_bill_id' => $rental_bill->id]);
        $quotation_count = Quotation::all()->count() + 1;
        if (!$quotation->exists) {
            $prefix = 'QT';
            $quotation->qt_no = generateRecordNumber($prefix, $quotation_count, false);
        }
        $quotation->qt_type = QuotationStatusEnum::DRAFT;
        $quotation->reference_type = Rental::class;
        $quotation->reference_id = $rental->id;
        $quotation->customer_id = $rental->customer_id;
        $quotation->customer_name = $rental->customer_name;
        $quotation->customer_address = $rental->customer_address;
        $quotation->customer_tel = $rental->customer_tel;
        $quotation->customer_email = $rental->customer_email;
        $quotation->customer_zipcode = $rental->customer_zipcode;
        $quotation->customer_province_id = $rental->customer_province_id;
        $quotation->subtotal = $rental_bill->subtotal;
        $quotation->vat = $rental_bill->vat;
        $quotation->total = $rental_bill->total;
        $quotation->rental_bill_id = $rental_bill->id;
        $quotation->save();

        $quotation->ref_1 = ($user && $user->branch) ? $user->branch->code : null;
        $quotation->ref_2 = $quotation->qt_no;
        $quotation->save();

        $rental->quotation_id = $quotation->id;
        $rental->save();
    }
}
