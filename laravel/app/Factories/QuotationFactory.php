<?php

namespace App\Factories;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Enums\QuotationTypeEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Factories\FactoryBase;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\ConditionQuotation;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\ConditionQuotationChecklist;
use stdClass;

class QuotationFactory extends FactoryBase implements FactoryInterface
{
    public $reference_type;
    public $reference_id;
    public $worksheet_no;
    public $qt_type;
    public $customer;
    public $summary;
    public $clone_qt_lines;
    public $service_type;

    public function __construct($reference_type, $reference_id, $customer_object, $summary_object, $clone_qt_lines, $service_type, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->reference_type = $reference_type;
        $this->reference_id = $reference_id;
        $this->customer = $this->formatCustomerObject($customer_object);
        $this->summary = $this->formatSummaryObject($summary_object);
        $this->clone_qt_lines = $clone_qt_lines;
        $this->service_type = $service_type;
        $this->qt_type = isset($optionals['qt_type']) ? $optionals['qt_type'] : RentalBillTypeEnum::PRIMARY;
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(Quotation::class, false);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        $quotation = new Quotation();
        $quotation->qt_no = $this->worksheet_no;
        $quotation->qt_type = $this->qt_type;
        $quotation->reference_type = $this->reference_type;
        $quotation->reference_id = $this->reference_id;

        // customer
        $quotation->customer_id = $this->customer->customer_id;
        $quotation->customer_name = $this->customer->customer_name;
        $quotation->customer_address = $this->customer->customer_address;
        $quotation->customer_tel = $this->customer->customer_tel;
        $quotation->customer_email = $this->customer->customer_email;
        $quotation->customer_zipcode = $this->customer->customer_zipcode;
        $quotation->customer_province_id = $this->customer->customer_province_id;
        $quotation->customer_district_id = $this->customer->customer_district_id;
        $quotation->customer_subdistrict_id = $this->customer->customer_subdistrict_id;

        // summary
        $quotation->is_withholding_tax = $this->summary->is_withholding_tax;
        $quotation->withholding_tax_value = $this->summary->withholding_tax_value;
        $quotation->subtotal = $this->summary->subtotal;
        $quotation->discount = $this->summary->discount;
        $quotation->coupon_discount = $this->summary->coupon_discount;
        $quotation->vat = $this->summary->vat;
        $quotation->withholding_tax = $this->summary->withholding_tax;
        $quotation->total = $this->summary->total;

        // status
        $quotation->status = QuotationStatusEnum::CONFIRM;
        $quotation->save();

        $quotation->ref_1 = get_branch_code();
        $quotation->ref_2 = $quotation->qt_no;
        $quotation->save();

        $this->cloneQuotationLines($quotation->id);

        if ($this->service_type) {
            $this->cloneConditionQuotation($quotation, $this->service_type);
        }
        return $quotation;
    }

    function update($quotation)
    {
        // customer
        $quotation->customer_id = $this->customer->customer_id;
        $quotation->customer_name = $this->customer->customer_name;
        $quotation->customer_address = $this->customer->customer_address;
        $quotation->customer_tel = $this->customer->customer_tel;
        $quotation->customer_email = $this->customer->customer_email;
        $quotation->customer_zipcode = $this->customer->customer_zipcode;
        $quotation->customer_province_id = $this->customer->customer_province_id;
        $quotation->customer_district_id = $this->customer->customer_district_id;
        $quotation->customer_subdistrict_id = $this->customer->customer_subdistrict_id;

        // summary
        $quotation->is_withholding_tax = $this->summary->is_withholding_tax;
        $quotation->withholding_tax_value = $this->summary->withholding_tax_value;
        $quotation->subtotal = $this->summary->subtotal;
        $quotation->discount = $this->summary->discount;
        $quotation->coupon_discount = $this->summary->coupon_discount;
        $quotation->vat = $this->summary->vat;
        $quotation->withholding_tax = $this->summary->withholding_tax;
        $quotation->total = $this->summary->total;

        // count
        $edit_count = intval($quotation->edit_count);
        $edit_count++;
        $quotation->edit_count = $edit_count;

        // status
        $quotation->status = QuotationStatusEnum::DRAFT;
        $quotation->save();

        $quotation->ref_1 = get_branch_code();
        $quotation->ref_2 = $quotation->qt_no;
        $quotation->save();

        QuotationLine::where('quotation_id', $quotation->id)->forceDelete();
        QuotationForm::where('quotation_id', $quotation->id)->forceDelete();

        $this->cloneQuotationLines($quotation->id);

        if ($this->service_type) {
            $this->cloneConditionQuotation($quotation, $this->service_type);
        }
        return $quotation;
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }

        if (empty($this->service_type)) {
            throw new Exception('empty serviceType');
        }
    }

    function cloneQuotationLines($quotation_id)
    {
        foreach ($this->clone_qt_lines as $line) {
            $quotation_line = new QuotationLine();
            $quotation_line->quotation_id = $quotation_id;
            $quotation_line->reference_id = $line->id;
            $quotation_line->reference_type = get_class($line);
            $quotation_line->amount = $line->amount;
            $quotation_line->unit_price = $line->unit_price;
            $quotation_line->subtotal = $line->subtotal;
            $quotation_line->discount = $line->discount;
            $quotation_line->vat = $line->vat;
            $quotation_line->total = $line->total;
            $quotation_line->save();
        }
    }

    function cloneConditionQuotation($quotation, $type)
    {
        $condition_quotations = ConditionQuotation::where('condition_type', $type)
            ->where('status', STATUS_ACTIVE)
            ->orderBy('seq', 'asc')
            ->get();
        foreach ($condition_quotations as $item_condition_quotation) {
            $quotation_form = new QuotationForm();
            $quotation_form->quotation_id = $quotation->id;
            $quotation_form->name = $item_condition_quotation->name;
            $quotation_form->seq = $item_condition_quotation->seq;
            $quotation_form->status = $item_condition_quotation->status;
            $quotation_form->save();

            $condition_quotation_checklist = ConditionQuotationChecklist::where('condition_quotations_id', $item_condition_quotation->id)
                ->where('status', STATUS_ACTIVE)
                ->orderBy('seq', 'asc')
                ->get();
            foreach ($condition_quotation_checklist as $item_condition_quotation_checklist) {
                $quotation_form_checklist = new QuotationFormChecklist();
                $quotation_form_checklist->quotation_form_id = $quotation_form->id;
                $quotation_form_checklist->name = $item_condition_quotation_checklist->name;
                $quotation_form_checklist->seq = $item_condition_quotation_checklist->seq;
                $quotation_form_checklist->status = $item_condition_quotation_checklist->status;
                $quotation_form_checklist->save();
            }
        }
    }
}
