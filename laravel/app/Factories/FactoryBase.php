<?php

namespace App\Factories;

use Exception;
use stdClass;

class FactoryBase
{
    function formatCustomerObject($customer_object)
    {
        if (!is_array($customer_object)) {
            $customer_object = $customer_object->getAttributes();
        }
        $customer = new stdClass;
        $customer->customer_id = $customer_object['customer_id'] ?? null;
        $customer->customer_name = $customer_object['customer_name'] ?? null;
        $customer->customer_address = $customer_object['customer_address'] ?? null;
        $customer->customer_tel = $customer_object['customer_tel'] ?? null;
        $customer->customer_email = $customer_object['customer_email'] ?? null;
        $customer->customer_zipcode = $customer_object['customer_zipcode'] ?? null;
        $customer->customer_province_id = $customer_object['customer_province_id'] ?? null;
        $customer->customer_district_id = $customer_object['customer_district_id'] ?? null;
        $customer->customer_subdistrict_id = $customer_object['customer_subdistrict_id'] ?? null;
        $customer->customer_tax_no = $customer_object['customer_tax_no'] ?? null;
        return $customer;
    }

    function formatCustomerObjectFromBilling($customer_object)
    {
        if (!is_array($customer_object)) {
            $customer_object = $customer_object->getAttributes();
        }
        $customer = new stdClass;
        $customer->customer_id = $customer_object['customer_id'] ?? null;
        $customer->customer_name = $customer_object['customer_billing_name'] ?? null;
        $customer->customer_address = $customer_object['customer_billing_address'] ?? null;
        $customer->customer_tel = $customer_object['customer_billing_tel'] ?? null;
        $customer->customer_email = $customer_object['customer_billing_email'] ?? null;
        $customer->customer_zipcode = $customer_object['customer_billing_zipcode'] ?? null;
        $customer->customer_province_id = $customer_object['customer_billing_province_id'] ?? null;
        $customer->customer_district_id = $customer_object['customer_billing_district_id'] ?? null;
        $customer->customer_subdistrict_id = $customer_object['customer_billing_subdistrict_id'] ?? null;
        $customer->customer_tax_no = $customer_object['customer_billing_tax_no'] ?? null;
        return $customer;
    }

    function formatSummaryObject($summary_object)
    {
        if (!is_array($summary_object)) {
            $summary_object = $summary_object->getAttributes();
        }
        $summary = new stdClass;
        $summary->is_withholding_tax = isset($summary_object['is_withholding_tax']) ? boolval($summary_object['is_withholding_tax']) : false;
        $summary->withholding_tax_value = $summary_object['withholding_tax_value'] ?? null;
        $summary->subtotal = $summary_object['subtotal'] ?? null;
        $summary->discount = $summary_object['discount'] ?? null;
        $summary->coupon_discount = $summary_object['coupon_discount'] ?? null;
        $summary->vat = $summary_object['vat'] ?? null;
        $summary->withholding_tax = $summary_object['withholding_tax'] ?? null;
        $summary->total = $summary_object['total'] ?? null;
        return $summary;
    }
}
