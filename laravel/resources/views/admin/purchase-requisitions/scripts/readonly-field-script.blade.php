@push('scripts')
    <script>
        // Readonly Field
        $('#purchaser').prop('readonly', true);
        $('#department').prop('readonly', true);
        $('#pr_no').prop('readonly', true);
        $('#request_date').prop("readonly", true);
        $('#require_date').prop("readonly", true);
        $('#parent_id').prop('disabled', true);
        $('#rental_type').prop('disabled', true);
        $('#remark').prop('readonly', true);
        $('#review_by').prop('readonly', true);
        $('#reviewed_at').prop('readonly', true);
        $('#review_department').prop('readonly', true);
        $('#reject_reason').prop('readonly', true);
        $('#cancel_reason').prop('readonly', true);
        //rental
        $('#reference_id').prop('disabled', true);
        $('#rental_refer').prop('disabled', true);
        $('#contract_refer').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#approve_ref_no').prop('disabled', true);
        $('#replacement_ref_no').prop('disabled', true);
    </script>
@endpush
