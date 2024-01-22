@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .js-table-checkable tbody tr {
            cursor: default !important;
        }

        .input-number-car-amount {
            -webkit-appearance: none;
            margin: 0;
            -moz-appearance: textfield;
            /* pointer-events: none; */
        }

        .block-content-full {
            background-color: #FFF8E6;
        }

        .block-bordered-custom {
            border: 1px solid #EFB008 !important;
        }

        /* .form-progress-bar {
                                    color: #888888;
                                    padding: 30px;
                                } */

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
            /* display: flex;
                            justify-content: center;
                            align-items: center; */
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            /* position: absolute; */
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* width: 70%; */
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }
    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    @if ($d->id)
        <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    @endif
    <div class="block {{ __('block.styles') }}">
        @include('admin.purchase-orders.sections.header')
        <div class="block-content">
            <form id="save-form">
                @include('admin.purchase-orders.sections.purchaser')
                {{-- @include('admin.purchase-orders.sections.car-detail') --}}
                @include('admin.purchase-orders.sections.pr-upload')
                @include('admin.purchase-orders.sections.pr-car-accessory')
                @include('admin.purchase-orders.sections.dealers')

                @include('admin.purchase-orders.sections.car-order')

                <x-forms.hidden id="id" name="id" :value="$d->id" />
                <x-forms.hidden id="pr_id" name="pr_id" :value="$d->pr_id" />
                {{-- <x-forms.hidden id="require_date" name="require_date" :value="$require_date" /> --}}

                @include('admin.purchase-orders.sections.submit')

            </form>
        </div>
    </div>
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.purchase-orders.scripts.dealers-script')
@include('admin.purchase-orders.scripts.update-status')
@include('admin.purchase-orders.scripts.update-cancel-status')
@include('admin.purchase-orders.scripts.pr-car-script')
@include('admin.purchase-orders.scripts.pr-accessory-script')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.purchase-orders.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'creditor_id_field',
    'modal' => '#modal-purchase-order-dealer',
    'url' => route('admin.util.select2.dealers'),
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
    'show_url' => true,
])

@push('scripts')
    <script>
        jQuery(function() {
            Dashmix.helpers(['dm-table-tools-checkable']);
        });
        var selected_dealers = addPurchaseOrderDealerVue.$data.purchase_order_dealer_list;
        var selected_creditor_id = '{{ $d->creditor_id }}';
        var purchase_order_lines = @json($purchase_order_lines);

        // set disable field
        $('#purchase_order_no').prop('disabled', true);
        $('#po_request_date').prop('disabled', true);
        $('#requester_name').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#purchase_requisition_no').prop('disabled', true);
        $('#request_date').prop('disabled', true);
        $('#purchase_requisition_date').prop('disabled', true);
        $('#delivery_date').prop('disabled', true);
        $('#rental_type').prop('disabled', true);
        $('#purchase_requisition_remark').prop('disabled', true);
        $('#reviewer_name').prop('disabled', true);
        $('#reviewer_department').prop('disabled', true);
        $('#review_at').prop('disabled', true);
        $('#reason').prop('disabled', true);
        // $('#time_of_delivery').prop('disabled', true);
        // $('#payment_condition').prop('disabled', true);

        //modal
        $('#car_class_field').prop('disabled', true);
        $('#car_color_field').prop('disabled', true);
        $('#amount_car_field').prop('disabled', true);
        $('#remark_car_field').prop('disabled', true);


        if (selected_creditor_id) {
            $('#ordered_creditor_id').val(selected_creditor_id).trigger('change');
            $('#check-all').prop('disabled', false);
            $('.form-check-input-each').prop('disabled', false);
        } else {
            $('#check-all').prop('disabled', true);
            $('.form-check-input-each').prop('disabled', true);
        }

        $('#ordered_creditor_id').on('select2:select', function(e) {
            $('#check-all').prop('disabled', false);
            $('.form-check-input-each').prop('disabled', false);
        });

        $('#ordered_creditor_id').on('change', function() {
            $('#check-all').prop('disabled', true);
            $('.form-check-input-each').prop('disabled', true);
            $('#check-all').prop('checked', false);
            $('.form-check-input-each').prop('checked', false);
            $('.summary-vat-total').text('-');
            $('.summary-price-total').text('-');
            $('.summary-discount-total').text('-');
            $('#summary-car-amount').text('-');

            purchase_requisition_car_list.forEach(function(car) {
                $('#' + car.id + '_amount').prop('disabled', true);
                $('#' + car.id + '_amount').val('');
                $('#vat_total_' + car.id).html('-');
                $('#price_total_' + car.id).html('-');
                $('#discount_total_' + car.id).html('-');
            });
        });

        // disabled price input
        purchase_requisition_car_list.forEach(function(car) {
            $('#' + car.id + '_amount').prop('disabled', true);
            $('#row_' + car.id).on('click', function() {
                var disabled = $(this).is(':checked') ? false : true;
                if (disabled) {
                    $('#' + car.id + '_amount').val('');
                    $('#vat_total_' + car.id).html('-');
                    $('#price_total_' + car.id).html('-');
                    $('#discount_total_' + car.id).html('-');
                }
                $('#' + car.id + '_amount').prop('disabled', disabled);
            });
        });

        if (purchase_order_lines.length > 0) {
            purchase_order_lines.forEach(function(car_price) {
                $('#row_' + car_price.item_id).prop('checked', true);
                $('#' + car_price.item_id + '_amount').prop('disabled', false);
                $('#' + car_price.item_id + '_amount').val(car_price.amount);
                $('#vat_total_' + car_price.item_id).html(numberWithCommas(car_price.vat));
                $('#discount_total_' + car_price.item_id).html(numberWithCommas(car_price.discount));
                $('#price_total_' + car_price.item_id).html(numberWithCommas(car_price.total));
                $('#selected_cars\\[' + car_price.item_id + '\\]\\[vat\\]').val(car_price.vat);
                $('#selected_cars\\[' + car_price.item_id + '\\]\\[discount\\]').val(car_price.discount);
                $('#selected_cars\\[' + car_price.item_id + '\\]\\[price\\]').val(car_price.total);
            });
            sumVatPrice();
            sumCarAmount();
            checkAllcheck()
        }

        // toogle input check all
        $('.form-check-input-each').change(function() {
            sumVatPrice();
            sumCarAmount();
            checkAllcheck()
        });
        $('#check-all').change(function() {
            if (this.checked) {
                $('.form-check-input-each').prop('checked', true);
                purchase_requisition_car_list.forEach(function(car) {
                    $('#' + car.id + '_amount').prop('disabled', false);
                });
            } else {
                $('.form-check-input-each').prop('checked', false);
                purchase_requisition_car_list.forEach(function(car) {
                    $('#' + car.id + '_amount').prop('disabled', true);
                    $('#' + car.id + '_amount').val('');
                    $('#vat_total_' + car.id).html('-');
                    $('#price_total_' + car.id).html('-');
                    $('#discount_total_' + car.id).html('-');
                });
            }
            sumVatPrice();
            sumCarAmount();
        });

        // iput each car amount change 
        $('.input-number-car-amount').on('input', function() {
            var required_car = $(this).val();
            var pr_car_id = $(this).attr('data-id');
            var selected_creditor_id = $('#ordered_creditor_id').val();
            var max_car = purchase_requisition_car_list.find(o => o.id == pr_car_id).amount;
            if (required_car % 1 !== 0) {
                required_car = parseInt(isNaN(parseInt(required_car)) ? 0 : required_car);
                $('#' + pr_car_id + '_amount').val(required_car);
            }
            if (required_car > max_car) {
                $('#' + pr_car_id + '_amount').val(max_car);
                required_car = max_car;
            }

            var selected_dealer_data = selected_dealers.find(o => o.creditor_id == selected_creditor_id);
            var price_list = selected_dealer_data.dealer_price_list.find(o => o.car_id == pr_car_id);
            var vat_total = parseFloat(price_list.vat * required_car).toFixed(2);
            var price_total = parseFloat((price_list.car_price - price_list.discount) * required_car).toFixed(2);
            // var price_total = parseFloat(price_list.car_price * required_car).toFixed(2);
            var discount_total = parseFloat(price_list.discount * required_car).toFixed(2);

            sumCarAmount();
            $('#vat_total_' + pr_car_id).html(numberWithCommas(vat_total));
            $('#selected_cars\\[' + pr_car_id + '\\]\\[vat\\]').val(vat_total);
            $('#price_total_' + pr_car_id).html(numberWithCommas(price_total));
            $('#selected_cars\\[' + pr_car_id + '\\]\\[price\\]').val(price_total);
            $('#discount_total_' + pr_car_id).html(numberWithCommas(discount_total));
            $('#selected_cars\\[' + pr_car_id + '\\]\\[discount\\]').val(discount_total);
        });

        // summary price
        $('.input-number-car-amount').on('input change', function() {
            console.log('input change');
            sumVatPrice();
        });

        function sumVatPrice() {
            list = ['vat', 'price', 'discount'];
            list.forEach(function(item) {
                var item_sum = 0;
                $('.' + item + '-total').each(function() {
                    value = parseFloat($(this).text().replace(/,/g, ''));
                    item_sum += (isNaN(value) ? 0 : value);
                });
                sum = numberWithCommas(parseFloat(item_sum).toFixed(2));
                $('.summary-' + item + '-total').text(sum);
                $('#summary_' + item + '_total').val(parseFloat(item_sum).toFixed(2));
            });
        }

        function sumCarAmount() {
            var car_amount_sum = 0;
            $('.input-number-car-amount').each(function() {
                car_amount = parseInt(+$(this).val());
                car_amount_sum += parseInt(isNaN(car_amount) ? 0 : car_amount);
            });
            $('#summary-car-amount').text(parseInt(car_amount_sum));
        }

        function checkAllcheck() {
            checked = ($('.form-check-input-each:checked').length == $('.form-check-input-each').length) ? true : false;
            $('#check-all').prop('checked', checked);
        }

        $(".btn-update-status").on("click", function() {
            let storeUri = "{{ route('admin.purchase-orders.store') }}";
            var formData = appendFormData();
            formData.append('status_updated', true);
            saveForm(storeUri, formData);
        });

        $(".btn-purchase-order-update-status").on("click", function() {
            let storeUri = "{{ route('admin.purchase-orders.update-status') }}";
            var formData = appendFormData();
            formData.append('status_updated', true);
            saveForm(storeUri, formData);
        });

        $(".btn-purchase-order-update-status").on("click", function() {
            var data = {
                purchase_order_status: $(this).attr('id'),
                purchase_order_id: document.getElementById("id").value,
            };
            updatePurchaseOrderStatus(data);
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
