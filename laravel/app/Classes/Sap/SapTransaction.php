<?php

namespace App\Classes\Sap;

use App\Models\SAPInterface;
use App\Models\SAPInterfaceLine;
use App\Classes\Sap\SapModel;
use App\Enums\SAPInterfaceLineTypeEnum;
use Illuminate\Support\Facades\Log;

class SapTransaction
{
    private $dr_lines;
    private $cr_lines;
    private $lines;
    private $err_message;

    public function __construct()
    {
        $this->dr_lines = collect([]);
        $this->cr_lines = collect([]);
        $this->lines = collect([]);
        $this->err_message = null;
    }

    function addDrLine(SapModel $param, string $line_type = null)
    {
        $param->posting_key = '40';
        $param->line_type = $line_type;
        $d = $this->generateLine($param);
        $this->dr_lines->push($d);
        $this->lines->push($d);
    }

    function addCrLine(SapModel $param, string $line_type = null)
    {
        $param->posting_key = '50';
        $param->line_type = $line_type;
        $d = $this->generateLine($param);
        $this->cr_lines->push($d);
        $this->lines->push($d);
    }

    private function generateLine(SapModel $param)
    {
        $d = new SAPInterfaceLine();
        $d->line_type = $param->line_type;
        //$d->flag = $param->flag;
        $d->posting_date = $param->posting_date;
        $d->document_date = $param->document_date;
        $d->document_type = $param->document_type;
        $d->company_code = $param->company_code;
        $d->branch_number = $param->branch_number;
        $d->currency = $param->currency;
        $d->reference_document = $param->reference_document;
        $d->header_text = $param->header_text;
        $d->posting_key = $param->posting_key;
        $d->account_no = $param->account_no;
        $d->amount_in_document = $param->amount_in_document;
        $d->amount_in_local_currency = $param->amount_in_document;
        $d->cost_center = $param->cost_center;
        $d->base_amount = $param->base_amount;
        $d->tax_code = $param->tax_code;
        $d->assignment = $param->assignment;
        $d->text = $param->text;
        return $d;
    }

    function generateSAPTransactions(string $account_type, string $transfer_type, string $transfer_sub_type, string $document_type, string $status = null)
    {
        $passed = $this->validateSAPTransactions();
        if (!$passed) {
            __log('SAP Error', [
                'err_message' => $this->err_message,
                'account_type' => $account_type,
                'transfer_type' => $transfer_type,
                'transfer_sub_type' => $transfer_sub_type,
                'document_type' => $document_type,
                'status' => $status,
            ], 'error');
            return false;
        }
        $sap = new SAPInterface();
        $sap->account_type = $account_type;
        $sap->transfer_type = $transfer_type;
        $sap->transfer_sub_type = $transfer_sub_type;
        $sap->document_type = $document_type;
        $sap->status = $status;
        $sap->save();

        $sap_interface_id = $sap->id;
        $size = sizeof($this->lines);
        foreach ($this->lines as $index => $line) {
            $flag = false;
            if ($index == ($size - 1)) {
                $flag = true;
            }
            $line->flag = $flag;
            $line->sap_interface_id = $sap_interface_id;
            $line->save();
        }
    }

    private function validateSAPTransactions()
    {
        $this->err_message = null;
        $passed = true;
        foreach ($this->lines as $line) {
            if (empty($line->account_no)) {
                $passed = false;
                $this->err_message = 'Empty account_no';
            }
            if (empty($line->amount_in_document)) {
                $passed = false;
                $this->err_message = 'Empty amount_in_document';
            }
            if (empty($line->posting_date)) {
                $passed = false;
                $this->err_message = 'Empty posting_date';
            }
            if (empty($line->document_type)) {
                $passed = false;
                $this->err_message = 'Empty document_type';
            }
            if (empty($line->branch_number)) {
                $passed = false;
                $this->err_message = 'Empty branch_number';
            }

            // check by type
            if (strcmp($line->line_type, SAPInterfaceLineTypeEnum::OUTPUT_TAX) == 0) {
                if (empty($line->tax_code)) {
                    $passed = false;
                    $this->err_message = 'Empty tax_code for ' . SAPInterfaceLineTypeEnum::OUTPUT_TAX;
                }
                if (floatval($line->base_amount) <= 0) {
                    $passed = false;
                    $this->err_message = 'Empty base_amount for ' . SAPInterfaceLineTypeEnum::OUTPUT_TAX;
                }
            }
            if (strcmp($line->line_type, SAPInterfaceLineTypeEnum::INCOME) == 0) {
                if (empty($line->cost_center)) {
                    $passed = false;
                    $this->err_message = 'Empty cost_center for ' . SAPInterfaceLineTypeEnum::INCOME;
                }
            }
        }

        $summary_dr = 0;
        $summary_cr = 0;
        foreach ($this->dr_lines as $line) {
            $summary_dr += floatval($line->amount_in_document);
        }
        foreach ($this->cr_lines as $line) {
            $summary_cr += floatval($line->amount_in_document);
        }
        // if ($summary_dr != $summary_cr) {
        //     $passed = false;
        //     $this->err_message = 'Unbalanced account';
        // }
        return $passed;
    }
}
