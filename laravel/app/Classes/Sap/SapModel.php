<?php

namespace App\Classes\Sap;

class SapModel
{
    public $line_type;
    public $flag;
    public $posting_date;
    public $document_date;
    public $document_type;
    public $company_code;
    public $branch_number;
    public $currency;
    public $reference_document;
    public $header_text;
    public $posting_key;
    public $account_no;
    public $amount_in_document;
    public $amount_in_local_currency;

    public $cost_center;
    public $base_amount;
    public $tax_code;
    public $assignment;
    public $text;

    public function __construct($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text)
    {
        $this->line_type = null;
        $this->flag = false;
        $this->company_code = '1005';
        $this->currency = 'THB';

        $this->posting_date = $posting_date;
        $this->document_date = $document_date;
        $this->document_type = $document_type;
        $this->branch_number = $branch_number;
        $this->reference_document = $reference_document;
        $this->header_text = $header_text;
    }
}
