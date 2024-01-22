@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">ข้อมูลใบเช่ายาว: {{ $d->worksheet_no }}</h3>
        </div>
        <div class="block-content">
            <form id="save-form">
                @include('admin.long-term-rental-compare-price.sections.rental-detail')
                @include('admin.long-term-rental-compare-price.sections.car-accessory')
                {{-- @include('admin.purchase-requisitions.sections.dealers') --}}
                @include('admin.long-term-rental-compare-price.sections.selected-dealer')
                @include('admin.long-term-rental-compare-price.sections.submit')
                <x-forms.hidden id="id" name="id" :value="$d->id" />
            </form>
        </div>
    </div>
@endsection
@include('admin.long-term-rental-compare-price.scripts.car-script')
@include('admin.long-term-rental-compare-price.scripts.accessory-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.long-term-rental-compare-price.scripts.dealers-script')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.compare-price.store'),
])

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
    'id' => 'creditor_id_field',
    'url' => route('admin.util.select2.dealers'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'dealer_files',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@push('scripts')
    <script>
        $('#rental_requisition_sheet').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer').prop('disabled', true);
        $('#offer_date').prop('disabled', true);
        $('.input-show-room').prop('disabled', true);

        //modal
        // $('#car_class_field').prop('disabled', true);
        // $('#car_color_field').prop('disabled', true);
        // $('#amount_car_field').prop('disabled', true);
        // $('#accessory_field').prop('disabled', true);
        // $('#amount_accessory_field').prop('disabled', true);


        var selected_creditor_id = '{{ $d->creditor_id }}';
        if (selected_creditor_id) {
            $('#ordered_creditor_id').val(selected_creditor_id).trigger('change');
        }

        $(".btn-confirm-status").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.compare-price.store') }}";
            var formData = appendFormData();
            formData.append('status_pending_review', true);
            saveForm(storeUri, formData);
        });


        $(".btn-draft-status").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.compare-price.store') }}";
            var formData = appendFormData();
            // formData.append('status_pending_review', true);
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
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_dealer_files && item.pending_delete_dealer_files.length >
                            0) {
                            item.pending_delete_dealer_files.forEach(function(id) {
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
