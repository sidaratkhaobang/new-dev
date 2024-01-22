@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('long_term_rentals.lt_rental_status_class_' . $d->status),
            __('long_term_rentals.lt_rental_status_' . $d->status),
            null,
        ) !!}
    @endif
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection

@push('custom_styles')
    <link rel='stylesheet' href='https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'>
    <style>
        .bootstrap-tagsinput {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            color: #343a40;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d8ea;
            border-radius: 0.25rem;
        }

        .bootstrap-tagsinput .tag {
            margin-right: 3px;
            color: white;
            background: #157CF2;
            padding: 3px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
        }

        .block .block-content .btn {
            min-width: unset;
        }

        .btn-group {
            background-color: #ffffff;
            color: #000000;
        }

        p.container {
            text-align: center;
            margin-bottom: 0px;
            padding: 24px;
        }

        .btn-img {
            /* height: 200px; */
            background: #FFFFFF;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }


        .btn-img:hover {
            border: solid #a4c1e2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        .btn-active {
            border: solid #157CF2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        .block-type {
            padding: 16px;
        }

        .radio-type {
            width: 24px;
            height: 24px;
            margin: 24px;
        }

        .btn-watch-package {
            border-radius: 6px;
            border: 1px solid var(--genaral-gray, #CCC);
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        @include('admin.long-term-rentals.sections.btn-group')

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.rental_table_long_term'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                @include('admin.long-term-rentals.sections.rental')
            </div>
        </div>
        @include('admin.long-term-rentals.sections.auction')

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.customer_table'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                @include('admin.long-term-rentals.sections.customer')
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.contact'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                @include('admin.long-term-rentals.sections.contact')
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.spec_header'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.radio-inline id="is_spec" :value="$d->is_spec" :list="$yes_no_list" :label="__('long_term_rentals.spec_header')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('long_term_rentals.upload_table'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                @include('admin.long-term-rentals.sections.upload')
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="count_files" :value="$count_files" />
                <x-forms.hidden id="rental_type" :value="$rental_type" />
                <x-forms.hidden id="status" :value="$d->status" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        {{-- <a class="btn btn-danger" href="{{ route('admin.long-term-rentals.index') }}">{{ __('lang.cancel') }}</a> --}}
                        <a class="btn btn-secondary"
                            href="{{ route('admin.long-term-rentals.index') }}">{{ __('lang.back') }}</a>
                        @if (!isset($view))
                            @if (
                                !in_array($d->status, [
                                    LongTermRentalStatusEnum::SPECIFICATION,
                                    LongTermRentalStatusEnum::COMPARISON_PRICE,
                                    LongTermRentalStatusEnum::RENTAL_PRICE,
                                    LongTermRentalStatusEnum::QUOTATION,
                                ]))
                                <button type="button"
                                    class="btn btn-info btn-save-form">{{ __('lang.save_draft') }}</button>
                            @endif
                            <button type="button"
                                class="btn btn-primary btn-save-request_spec">{{ __('lang.save') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rentals.store'),
])
@include('admin.long-term-rentals.scripts.rental-month-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'tor_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($tor_files) ? $tor_files : [],
    'show_url' => true,
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_id',
    'parent_id' => 'customer_type',
    'url' => route('admin.util.select2-customer.customer-codes'),
])
@include('admin.components.upload-image', [
    'id' => 'rental_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($rental_files) ? $rental_files : [],
])

@include('admin.components.upload-image', [
    'id' => 'payment_form',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($payment_forms) ? $payment_forms : [],
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'approved_rental_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($approved_rental_files) ? $approved_rental_files : [],
])

@push('scripts')
    <script>
        $('#worksheet_no').prop("readonly", true);
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#job_type').prop('disabled', true);
            $('#rental_duration').prop('disabled', true);
            $('input[name="need_pay_auction"]').prop('disabled', true);
            $('input[name="won_auction"]').prop('disabled', true);
            $('#auction_submit_date').prop('disabled', true);
            $('#auction_winning_date').prop('disabled', true);
            $('#require_date').prop('disabled', true);
            $('#bidder_price').prop('disabled', true);
            $('#bidder_name').prop('disabled', true);
            $('#actual_delivery_date_auction').prop('disabled', true);
            $('#contract_start_date_auction').prop('disabled', true);
            $('#contract_end_date_auction').prop('disabled', true);
            $('#actual_delivery_date_no_auction').prop('disabled', true);
            $('#contract_start_date_no_auction').prop('disabled', true);
            $('#contract_end_date_no_auction').prop('disabled', true);
            $('#reject_reason_id').prop('disabled', true);
            $('#reject_reason_description').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#customer_type').prop('disabled', true);
            $('#customer_id').prop('disabled', true);
            $('#customer_name').prop('disabled', true);
            $('#customer_email').prop('disabled', true);
            $('#customer_tel').prop('disabled', true);
            $('#customer_tax').prop('disabled', true);
            $('#customer_province_id').prop('disabled', true);
            $('#customer_zipcode').prop('disabled', true);
            $('#customer_address').prop('disabled', true);
            $('.tagsinput input').attr('disabled', 'disabled');
            $('#month').prop('disabled', true);
            $("input[type='radio']").attr('disabled', true);
            $('#approval_type').prop('disabled', true);
            $('#approve_status').prop('disabled', true);
            $('#offer_date').prop('disabled', true);
            // $('#customer_link').removeAttr('href');
            $('#customer_link').addClass("disabled");
            $('#customer_link').css("background-color", "#343a40");
            $('#customer_link').css("border-color", "#343a40");
            $('#contact_name').prop('disabled', true);
            $('#contact_email').prop('disabled', true);
            $('#contact_remark').prop('disabled', true);
            $('#contact_tel').prop('disabled', true);
            $('#delivery_date_remark_no_auction').prop('disabled', true);
            $('#delivery_date_remark_auction').prop('disabled', true);
        }

        if (!$status) {
            function getRentalType(lt_rental_type_id) {
                if ($('.btn-active').length) {
                    $('.btn-active').not($(this)).removeClass('btn-active').addClass('btn-img');
                }
                $('.btn-' + lt_rental_type_id).removeClass('btn-img').addClass('btn-active');
                $('.btn-radio-' + lt_rental_type_id).closest('.btn-type').find('.radio-type').prop("checked", true);

                var data_id = lt_rental_type_id;
                axios.get("{{ route('admin.long-term-rentals.check-rental-type') }}", {
                    params: {
                        lt_rental_type_id: data_id,
                    }
                }).then(response => {
                    if (response.data.success) {
                        if (response.data.rental_type === '{{ AuctionStatusEnum::AUCTION }}') {
                            console.log('AUCTION');
                            $('#rental_type').val(response.data.rental_type);
                            document.getElementById("auction_show").style.display = "block"
                            document.getElementById("auction").style.display = "block"
                            document.getElementById("no_auction").style.display = "none"
                            document.getElementById("auction_data_show").style.display = "block"
                            document.getElementById("need_actual_delivery_date_auction").style.display = "block"
                        } else if (response.data.rental_type === '{{ AuctionStatusEnum::NO_AUCTION }}') {
                            console.log('NO_AUCTION');
                            $('#rental_type').val(response.data.rental_type);
                            document.getElementById("auction_show").style.display = "block"
                            document.getElementById("no_auction").style.display = "block"
                            document.getElementById("auction").style.display = "none"
                            document.getElementById("auction_data_show").style.display = "none"
                            document.getElementById("need_actual_delivery_date_no_auction").style.display = "block"
                        }
                    }
                });
            }
            $('input[name="need_actual_delivery_date_no_auction"]').on("click", function() {
                if ($('input[name="need_actual_delivery_date_no_auction"]:checked').val() ===
                    '{{ BOOL_FALSE }}') {
                    document.getElementById("need_actual_delivery_remark_no_auction").style.display = "block"
                    document.getElementById("need_actual_delivery_date_no_auction").style.display = "none"
                } else {
                    document.getElementById("need_actual_delivery_remark_no_auction").style.display = "none"
                    document.getElementById("need_actual_delivery_date_no_auction").style.display = "block"
                }
            });
            $('input[name="need_actual_delivery_date_auction"]').on("click", function() {
                if ($('input[name="need_actual_delivery_date_auction"]:checked').val() === '{{ BOOL_FALSE }}') {
                    document.getElementById("need_actual_delivery_remark_auction").style.display = "block"
                    document.getElementById("need_actual_delivery_date_auction").style.display = "none"
                } else {
                    document.getElementById("need_actual_delivery_remark_auction").style.display = "none"
                    document.getElementById("need_actual_delivery_date_auction").style.display = "block"
                }
            });

            $('input[name="need_pay_auction"]').on("click", function() {
                if ($('input[name="need_pay_auction"]:checked').val() === '{{ BOOL_TRUE }}') {
                    document.getElementById("need_auction_file").style.display = "block"
                } else {
                    document.getElementById("need_auction_file").style.display = "none"
                }
            });

            $(document).on('change', "input[name='won_auction']", function() {
                let Type = $(this).val()
                console.log(Type)
                if (Type == "{{ AuctionResultEnum::LOSE }}") {
                    RemarkRejectAunction("Show")
                } else {
                    RemarkRejectAunction()
                }
            });

            function RemarkRejectAunction(ShowStatus = null) {
                var parentElement = $('#reject_reason_id').parent();
                console.log(parentElement)
                if (ShowStatus) {
                    document.getElementById("won_show").style.display = "none"
                    document.getElementById("lose_show").style.display = "block"
                    $('#reject_reason_id').parent().removeClass('d-none')
                    $('#reject_reason_description').parents().removeClass('d-none')
                } else {
                    document.getElementById("won_show").style.display = "block"
                    document.getElementById("lose_show").style.display = "none"
                    $('#reject_reason_id').parent().addClass('d-none')
                    $('#reject_reason_description').parent().addClass('d-none')
                }
            }
        }

        // Top BYN Menu

        $lt_status = '{{ $d->status }}';
        if ($lt_status != "{{ LongTermRentalStatusEnum::NEW }}") {
            $('#lt_rental_type_id_hidden').prop('disabled', true);
            $('#approval_type_hidden').prop('disabled', true);
        }

        eventSelect = $('#customer_id');
        eventSelect.on('change', function(e) {
            clearCustomerDetail();
        });

        eventSelect.on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-customer.customer-detail') }}", {
                params: {
                    customer_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    addCustomerDetail(response.data.data);
                }
            });
        });

        function addCustomerDetail(data) {
            $('#customer_name').val(data.name);
            $('#customer_tax').val(data.tax_no);
            $('#customer_email').val(data.email);
            $('#customer_tel').val(data.tel);
            $('#customer_province_id').val(data.province_id).trigger('change');
            $('#customer_zipcode').val('');
            $('#customer_address').val(data.address);
        }

        function clearCustomerDetail() {
            $('#customer_name').val('');
            $('#customer_email').val('');
            $('#customer_tel').val('');
            $('#customer_province_id').val(null).trigger('change');
            $('#customer_zipcode').val('');
            $('#customer_address').val('');
        }

        $(".btn-save-request_spec").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rentals.store') }}";
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
            $create = '{{ isset($create) }}';
            formData.append('create', $create);
            // formData.append('set_specs', true);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
