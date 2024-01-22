<?php

namespace App\Factories;

use Exception;
use App\Factories\FactoryBase;
use App\Models\Receipt;
use App\Models\ReceiptLine;

class ReceiptFactory extends FactoryBase implements FactoryInterface
{
    public $worksheet_no;
    public $branch_id;
    public $receipt_type;
    public $customer;
    public $summary;

    public function __construct($receipt_type, $customer_object, $summary_object, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->branch_id = isset($optionals['branch_id']) ? $optionals['branch_id'] : get_branch_id();
        $this->receipt_type = $receipt_type;
        $this->customer = $this->formatCustomerObject($customer_object);
        $this->summary = $this->formatSummaryObject($summary_object);
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(Receipt::class, false);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        $receipt = new Receipt();
        $receipt->branch_id = $this->branch_id;
        $receipt->worksheet_no = $this->worksheet_no;
        $receipt->reference_id = null;
        $receipt->reference_type = null;
        $receipt->receipt_type = $this->receipt_type;

        // customer
        $receipt->customer_id = $this->customer->customer_id;
        $receipt->customer_name = $this->customer->customer_name;
        $receipt->customer_address = $this->customer->customer_address;
        $receipt->customer_tel = $this->customer->customer_tel;
        $receipt->customer_email = $this->customer->customer_email;
        //$receipt->customer_code = $this->customer->customer_code;
        $receipt->customer_tax_no = $this->customer->customer_tax_no;

        // summary
        $receipt->is_withholding_tax = $this->summary->is_withholding_tax;
        $receipt->withholding_tax_value = $this->summary->withholding_tax_value;
        $receipt->subtotal = $this->summary->subtotal;
        $receipt->vat = $this->summary->vat;
        $receipt->withholding_tax = $this->summary->withholding_tax;
        $receipt->total = $this->summary->total;
        $receipt->save();
        return $receipt;
    }

    function createWithLine($reference_id, $reference_type, $name)
    {
        $receipt = $this->create();
        $this->createReceiptLine($receipt->id, $reference_id, $reference_type, $name, $receipt->subtotal, $receipt->vat, $receipt->total);
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }
    }

    function createReceiptLine($receipt_id, $reference_id, $reference_type, $name, $subtotal, $vat, $total)
    {
        $receipt_line = new ReceiptLine();
        $receipt_line->receipt_id = $receipt_id;
        $receipt_line->reference_type = $reference_type;
        $receipt_line->reference_id = $reference_id;
        $receipt_line->name = $name;
        $receipt_line->subtotal = $subtotal;
        $receipt_line->vat = $vat;
        $receipt_line->total = $total;
        $receipt_line->save();
    }
}
