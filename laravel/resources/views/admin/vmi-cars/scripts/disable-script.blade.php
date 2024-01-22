@push('scripts')
    <script>
         function disabledPremiumSection(is_disabled) {
            $('#premium').prop('disabled', is_disabled);
            $('#discount').prop('disabled', is_disabled);
            $('#stamp_duty').prop('disabled', is_disabled);
            $('#tax').prop('disabled', is_disabled);
            $('#premium_total').prop('disabled', is_disabled);
            $('#premium_total').prop('disabled', is_disabled);
            $('#withholding_tax').prop('disabled', is_disabled);
            $('#statement_no').prop('disabled', is_disabled);
            $('#tax_invoice_no').prop('disabled', is_disabled);
            $('#statement_date').prop('disabled', is_disabled);
            $('#account_submission_date').prop('disabled', is_disabled);
            $('#operated_date').prop('disabled', is_disabled);
            $('#status_pay_premium').prop('disabled', is_disabled);
        }

        function disabledVMIBar(is_disabled) {
            $('#receive_date').prop('disabled', is_disabled);
            $('#check_date').prop('disabled', is_disabled);
            $('#policy_reference_child_vmi').prop('disabled', is_disabled);
            $('#policy_reference_vmi').prop('disabled', is_disabled);
            $('#endorse_vmi').prop('disabled', is_disabled);
        }

        function disabledPA(is_disabled) {
            $('#pa').prop('disabled', is_disabled);
            $('#pa_and_bb').prop('disabled', is_disabled);
            $('#pa_per_endorsement').prop('disabled', is_disabled);
            $('#pa_total_premium').prop('disabled', is_disabled);
            $('#id_deductible').prop('disabled', is_disabled);
            $('#discount_deductible').prop('disabled', is_disabled);
            $('#fit_discount').prop('disabled', is_disabled);
            $('#fleet_discount').prop('disabled', is_disabled);
            $('#ncb').prop('disabled', is_disabled);
            $('#good_vmi').prop('disabled', is_disabled);
            $('#bad_vmi').prop('disabled', is_disabled);
        }

        function disabledDiscount(is_disabled) {
            $('#other_discount_percent').prop('disabled', is_disabled);
            $('#other_discount').prop('disabled', is_disabled);
            $('#gps_discount').prop('disabled', is_disabled);
            $('#total_discount').prop('disabled', is_disabled);
            $('#net_discount').prop('disabled', is_disabled);
            $('#cct').prop('disabled', is_disabled);
            $('#gross').prop('disabled', is_disabled);
        }
        function disabledRecovery(is_disabled) {
            $('#tpbi_person').prop('disabled', is_disabled);
            $('#tpbi_aggregate').prop('disabled', is_disabled);
            $('#tppd_aggregate').prop('disabled', is_disabled);
            $('#deductible').prop('disabled', is_disabled);
            $('#own_damage').prop('disabled', is_disabled);
            $('#fire_and_theft').prop('disabled', is_disabled);
            $('#deductible_car').prop('disabled', is_disabled);
            $('#pa_driver').prop('disabled', is_disabled);
            $('#pa_passenger').prop('disabled', is_disabled);
            $('#medical_exp').prop('disabled', is_disabled);
            $('#bail_bond').prop('disabled', is_disabled);
        }
    </script>
@endpush