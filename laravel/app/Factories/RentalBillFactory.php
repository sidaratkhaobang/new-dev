<?php

namespace App\Factories;

use Exception;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Models\RentalBill;
use App\Models\RentalBillLine;
use App\Classes\QuickPay;

class RentalBillFactory implements FactoryInterface
{
    public $rental;
    public $worksheet_no;
    public $bill_type;

    public function __construct($rental, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->rental = $rental;
        $this->bill_type = isset($optionals['bill_type']) ? $optionals['bill_type'] : RentalBillTypeEnum::PRIMARY;
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(RentalBill::class, false);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        $rental_bill = new RentalBill();
        $rental_bill->rental_id = $this->rental->id;
        $rental_bill->bill_type = $this->bill_type;
        $rental_bill->status = RentalStatusEnum::PENDING;
        $rental_bill->subtotal = $this->rental->subtotal;
        $rental_bill->discount = $this->rental->discount;
        $rental_bill->coupon_discount = $this->rental->coupon_discount;
        $rental_bill->vat = $this->rental->vat;
        $rental_bill->withholding_tax = $this->rental->withholding_tax;
        $rental_bill->total = $this->rental->total;
        $rental_bill->save();

        $rental_lines = $rental_bill->rentalLines;
        foreach ($rental_lines as $rental_line) {
            $rental_bill_line = new RentalBillLine();
            // save id
            $rental_bill_line->rental_bill_id = $rental_bill->id;
            $rental_bill_line->rental_line_id = $rental_line->id;

            // clone data
            $rental_bill_line->item_type = $rental_line->item_type;
            $rental_bill_line->item_id = $rental_line->item_id;
            $rental_bill_line->is_free = $rental_line->is_free;
            $rental_bill_line->is_from_product = $rental_line->is_from_product;
            $rental_bill_line->is_from_promotion = $rental_line->is_from_promotion;
            $rental_bill_line->is_from_coupon = $rental_line->is_from_coupon;
            $rental_bill_line->car_id = $rental_line->car_id;
            $rental_bill_line->name = $rental_line->name;
            $rental_bill_line->description = $rental_line->description;
            $rental_bill_line->amount = $rental_line->amount;
            $rental_bill_line->unit_price = $rental_line->unit_price;
            $rental_bill_line->subtotal = $rental_line->subtotal;
            $rental_bill_line->discount = $rental_line->discount;
            $rental_bill_line->vat = $rental_line->vat;
            $rental_bill_line->total = $rental_line->total;
            $rental_bill_line->save();
        }

        return $rental_bill;
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }
    }
}
