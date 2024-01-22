@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">ข้อมูลใบเช่ายาว: {{ $d->worksheet_no }}</h3>
        </div>
        <div class="block-content">
            <form id="save-form">
                {{-- <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="creditor_id" :value="$creditor" :label="__('long_term_rentals.dealers')" />
                    </div>
                </div> --}}
                {{-- @include('admin.purchase-requisitions.sections.dealers') --}}
                @include('admin.long-term-rental-specs.sections.rental-detail')
                @if (strcmp($d->rental_price_status, LongTermRentalPriceStatusEnum::REJECT) == 0)
                    @include('admin.long-term-rental-quotations.sections.reject')
                @endif
                @include('admin.long-term-rental-compare-price.sections.selected-dealer')
                {{-- @include('admin.long-term-rental-quotations.sections.dealer-price') --}}
                @include('admin.long-term-rental-quotations.sections.rental-price')
                @include('admin.long-term-rental-quotations.sections.wreck-price')

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.text-area-new-line id="quotation_remark" :value="$d->quotation_remark" :label="__('long_term_rentals.remark')" />
                    </div>
                </div>
                <x-forms.hidden id="id" name="id" :value="$d->id" />
                <x-forms.hidden id="quotation_id" name="quotation_id" :value="$d->quotation_id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.long-term-rental.quotations.index') }}">{{ __('lang.back') }}</a>
                        @if (!isset($view))
                            @can(Actions::Manage . '_' . Resources::LongTermRentalQuotation)
                                @if ($d->rental_price_status != LongTermRentalPriceStatusEnum::CONFIRM)
                                    <button type="button"
                                        class="btn btn-info btn-save-form">{{ __('lang.save_draft') }}</button>
                                @endif
                                <button type="button"
                                    class="btn btn-primary btn-save-qt">{{ __('long_term_rentals.save_qt') }}</button>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- @include('admin.purchase-requisitions.scripts.dealers-script') --}}
@include('admin.long-term-rental-compare-price.scripts.dealers-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.quotations.store'),
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
        $('#worksheet_no').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#customer_type').prop('disabled', true);
        $('#customer_id').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#offer_date').prop('disabled', true);
        $('#customer').prop('disabled', true);
        $('#reason').prop('disabled', true);
        $('.input-show-room').prop('disabled', true);

        $('#creditor_id').prop("readonly", true);
        $status = '{{ isset($view) }}';
        var lt_rental_list = @json($lt_rental_list);
        if ($status) {
            $('#quotation_remark').prop("disabled", true);
            $("input[type=radio]").attr('disabled', true);
            if (lt_rental_list.length > 0) {
                lt_rental_list.forEach(function(compare_price) {
                    $('#price\\[' + compare_price.lt_rental_line_id + '\\]').prop('disabled', true);
                });
            }
        }

        // $(".btn-save-qt").on("click", function() {
        //     let storeUri = "{{ route('admin.long-term-rental.quotations.store') }}";
        //     var formData = new FormData(document.querySelector('#save-form'));
        //     formData.append('quotation_status', true);
        //     saveForm(storeUri, formData);
        // });

        $(".btn-save-qt").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.quotations.store') }}";
            var formData = appendFormData();
            formData.append('quotation_status', true);
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


        var radio_val = $("input[type=radio][name=purchase_option_check]:checked").val();
        if (radio_val == {{ STATUS_DEFAULT }}) {
            $('#wreck_table').hide();
            $('#wreck_topic').hide();
            $('#wreck_hr').hide();
        } else {
            $('#wreck_table').show();
            $('#wreck_topic').show();
            $('#wreck_hr').show();
        }

        $("input[type=radio][name=purchase_option_check]").change(function() {
            var radio_val = $("input[type=radio][name=purchase_option_check]:checked").val();
            if (radio_val == {{ STATUS_DEFAULT }}) {
                $('#wreck_table').hide();
                $('#wreck_topic').hide();
                $('#wreck_hr').hide();
            } else {
                $('#wreck_table').show();
                $('#wreck_topic').show();
                $('#wreck_hr').show();
            }
            // console.log(radio_val);
        });
    </script>
@endpush
