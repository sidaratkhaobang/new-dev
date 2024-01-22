@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.purchase-requisitions.sections.header')
        <div class="block-content">
            <form id="save-form">

                @include('admin.purchase-requisitions.sections.purchase')
                @include('admin.purchase-requisitions.views.pr-car-accessory')
                @include('admin.purchase-requisitions.views.pr-upload')
                @include('admin.purchase-requisitions.sections.dealers')
                @include('admin.purchase-requisitions.sections.car-order')

                <x-forms.hidden id="id" :value="$d->id" />
                @include('admin.purchase-requisitions.sections.submit')
            </form>
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.form-save', [
    'store_uri' => route('admin.purchase-requisition.save-form-dealer'),
])

@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.purchase-requisitions.scripts.pr-car-script')
@include('admin.purchase-requisitions.scripts.pr-accessory-script')
@include('admin.components.date-input-script')
@include('admin.purchase-requisitions.scripts.dealers-script')
@include('admin.purchase-requisitions.scripts.car-order-script')
@include('admin.purchase-requisitions.scripts.readonly-field-script')


@include('admin.components.select2-ajax', [
    'id' => 'car_class_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.select2-ajax', [
    'id' => 'parent_id',
    'url' => route('admin.util.select2.pr-parent'),
])

@include('admin.components.select2-ajax', [
    'id' => 'creditor_id_field',
    'modal' => '#modal-purchase-order-dealer',
    'url' => route('admin.util.select2.dealers'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'dealer_files',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@include('admin.components.upload-image', [
    'id' => 'quotation_files',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $quotation_files,
    'show_url' => true
])

@push('scripts')
    <script>
        $('#purchaser').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#request_date').prop("readonly", true);
        $('#pr_no').prop("readonly", true);
        $('#review_by').prop('readonly', true);
        $('#reviewed_at').prop('readonly', true);
        $('#review_department').prop('readonly', true);
        $('#reject_reason').prop('readonly', true);
        //rental
        $('#require_date').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);

        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-show-cancel-modal").on("click", function() {
            document.getElementById("cancel_status").value = $(this).attr('data-status');
            document.getElementById("cancel_id").value = document.getElementById("id").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
            $('#modal-cancel').modal('show');
        });

        $("#rental_type").on("change.select2", function(e) {
            $('#reference_id').val(null).trigger('change');
            $('#customer_type').val('');
            $('#customer_name').val('');
            $('#rental_refer').val('');
            $('#contract_refer').val('');
            $('#job_type').val('');
            $('#rental_duration').val('');
            var rental_type = $("#rental_type").val();
            if (rental_type === '{{ \App\Enums\RentalTypeEnum::SHORT }}') {
                document.getElementById("rental_select").style.display = "block"
                document.getElementById("short_rental_1").style.display = "block"
                document.getElementById("short_rental_2").style.display = "block"
                document.getElementById("long_rental_1").style.display = "none"
                document.getElementById("long_rental_2").style.display = "none"
            } else if (rental_type === '{{ \App\Enums\RentalTypeEnum::LONG }}') {
                document.getElementById("rental_select").style.display = "block"
                document.getElementById("short_rental_1").style.display = "none"
                document.getElementById("short_rental_2").style.display = "none"
                document.getElementById("long_rental_1").style.display = "block"
                document.getElementById("long_rental_2").style.display = "block"
            } else {
                document.getElementById("rental_select").style.display = "none"
                document.getElementById("short_rental_1").style.display = "none"
                document.getElementById("short_rental_2").style.display = "none"
                document.getElementById("long_rental_1").style.display = "none"
                document.getElementById("long_rental_2").style.display = "none"
            }
        });

        $("#reference_id").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.purchase-requisition.rental-type-by-id') }}";
                },
                type: 'GET',
                data: function(params) {
                    rental_type = $("#rental_type").val();
                    return {
                        rental_type: rental_type,
                        s: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            }
        });

        $("#reference_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.purchase-requisition.rental-type-data') }}", {
                params: {
                    rental_type: $("#rental_type").val(),
                    rental_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#customer_type").val(e.customer_type);
                            $("#customer_name").val(e.customer_name);
                            if (response.data.rental_type ===
                                '{{ \App\Enums\RentalTypeEnum::LONG }}') {
                                $("#job_type").val(e.job_type);
                                $("#rental_duration").val(e.rental_duration);
                            }
                        });
                    }
                }
            });
        });

        $(".btn-save-form-dealer").on("click", function() {
            let storeUri = "{{ route('admin.purchase-requisition.save-form-dealer') }}";
            var formData = appendFormData();
            saveForm(storeUri, formData);
        });

        $(".btn-save-draft-form-dealer").on("click", function() {
            let storeUri = "{{ route('admin.purchase-requisition.save-form-dealer') }}";
            var formData = appendFormData();
            formData.append('draft_status', true);
            saveForm(storeUri, formData);
        });

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }

            if (window.addPurchaseOrderDealerVue) {
                let data = window.addPurchaseOrderDealerVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.dealer_files && item.dealer_files.length > 0) {
                            item.dealer_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('dealer_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addPurchaseOrderDealerVue.getPendingDeleteMediaIds();
                console.log(delete_ids, 'deleteddd');

                if (delete_ids && delete_ids.length > 0) {
                    console.log(delete_ids, 'delete');
                    delete_ids.forEach((item) => {
                        console.log(item, 'eiei');

                        if (item.pending_delete_dealer_files && item.pending_delete_dealer_files.length > 0) {
                            console.log(item.pending_delete_dealer_files, 'eiei');
                            item.pending_delete_dealer_files.forEach(function(id) {
                                // console.log(id);
                                formData.append('pending_delete_dealer_files[]', id);
                            });
                        }
                    });
                }


                //delete dealer row
                let delete_dealer_ids = window.addPurchaseOrderDealerVue.pending_delete_dealer_ids;
                if (delete_dealer_ids && (delete_dealer_ids.length > 0)) {
                    delete_dealer_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_dealer_ids[]', delete_driver_id);
                    });
                }
            }
            return formData;
        }
    </script>
@endpush
