@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('short_term_rentals.class_' . $d->status),
            __('short_term_rentals.status_' . $d->status),
            null,
        ) !!}
    @endif
@endsection

@push('styles')
    <style>
        .table-striped-custom .pair-row {
            background-color: #f6f8fc;
            /* Replace with your desired color */
        }

        .btn-img {
            height: 150px;
            width: 300px;
            background: #FFFFFF;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        .style-img {
            border-top-left-radius: 0.5em;
            border-top-right-radius: 0.5em;
            width: 100%;
            height: 150px;
            object-fit: contain;
        }

        .btn-img:hover {
            border: solid #a4c1e2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
            cursor: pointer;
        }

        .active {
            border: solid #157CF2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :step="1" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-info :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-asset :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-driver :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-promotion :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>

    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.summary')" :step="7" :optionals="['block_icon_class' => __('short_term_rentals.step_icon.summary')]"/>
        <div class="block-content pt-0">
            <form id="save-form">
                <x-blocks.block :title="__('short_term_rentals.product_service')" :optionals="['is_toggle' => false]">
                    <x-slot name="options">
                        <button type="button" class="btn btn-primary" onclick="openExtraModal()"><i
                                    class="icon-add-circle"></i>{{ __('lang.add') }}</button>
                    </x-slot>
                    <div id="rental-lines" v-cloak data-detail-uri="" data-title="">
                        @include('admin.short-term-rental-summary.sections.rental-line')
                        @include('admin.short-term-rental-summary.sections.summary')
                    </div>
                </x-blocks.block>

                @if ($order_channel == OrderChannelEnum::WEBSITE)
                    <x-blocks.block :title="__('short_term_rentals.payment')" :optionals="['is_toggle' => false]">
                        @include('admin.short-term-rental-summary.sections.payment-website')
                    </x-blocks.block>
                @endif

                @if ($quotation && $order_channel == OrderChannelEnum::SMARTCAR)
                    <x-blocks.block :title="__('short_term_rentals.payment')" :optionals="['is_toggle' => false]">
                        @include('admin.short-term-rental-summary.sections.payment-info')
                    </x-blocks.block>
                @endif

                <x-blocks.block :title="__('short_term_rentals.other_info')" :optionals="['is_toggle' => false]">
                    <div class="row">
                        <div class="col-sm-12">
                            <x-forms.text-area-new-line id="rental_remark" :value="$d->rental_remark"
                                                        :label="__('short_term_rentals.rental_remark')"/>
                        </div>
                    </div>
                </x-blocks.block>

                @include('admin.short-term-rental-summary.sections.bill')
                @include('admin.short-term-rental-summary.sections.bill_contact')
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                <x-forms.hidden id="status" :value="$d->status"/>
                <x-forms.hidden id="order_channel" :value="$d->order_channel"/>
                <x-forms.hidden id="rental_bill_id" :value="$rental_bill_id"/>

                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="6" :optionals="[
                    'btn_name' => __('short_term_rentals.save_form'),
                    'input_class_submit' => 'btn-rental-update-status',
                    'data_status' => RentalStatusEnum::PENDING,
                    'status' => $btn_status,
                    'isdraft' => true,
                ]"/>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.summary.store'),
])
@include('admin.short-term-rental-summary.scripts.rental-line-script')
@include('admin.short-term-rentals.scripts.update-cancel-status')

@include('admin.short-term-rental-summary.scripts.gen-2c2p-payment-script')

@include('admin.components.upload-image-scripts')

@if (in_array($order_channel, [OrderChannelEnum::SMARTCAR, OrderChannelEnum::WEBSITE]))
    @include('admin.components.upload-image', [
        'id' => 'ref_sheet_image',
        'max_files' => 100,
        'accepted_files' => '.jpg,.jpeg,.png',
        'mock_files' => $ref_files,
    ])
@endif

@include('admin.components.select2-ajax', [
    'id' => 'biller_province_id',
    'url' => route('admin.util.select2.provinces'),
])
@include('admin.components.select2-ajax', [
    'id' => 'biller_district_id',
    'parent_id' => 'biller_province_id',
    'url' => route('admin.util.select2.districts'),
])
@include('admin.components.select2-ajax', [
    'id' => 'biller_subdistrict_id',
    'parent_id' => 'biller_district_id',
    'url' => route('admin.util.select2.subdistricts'),
])

@push('scripts')
    <script>
        var enum_omise = '{{ \App\Enums\PaymentGatewayEnum::OMISE }}';
        var enum_mc_payment = '{{ \App\Enums\PaymentGatewayEnum::MC_PAYMENT }}';
        var type_payment = '{{ $d->payment_gateway }}';
        $('#bill_postal_code').prop('disabled', true)
        $('#biller_subdistrict_id').on('select2:select', function (e) {
            var data = e.params.data;
            setZipCode(data.zip_code);
        }).on('select2:unselect', function (e) {
            setZipCode('');
        }).on('select2:clear', function (e) {
            setZipCode('');
        });

        function setZipCode(zip_code) {
            $('#bill_postal_code').val(zip_code);
        }

        /* if (type_payment == enum_omise || type_payment == enum_mc_payment) {
            $('#ref_file').show();
            $('#date_payment').show();
            $('#payment_status_draft').show();
        } else {
            $('#ref_file').hide();
            $('#date_payment').hide();
            $('#payment_status_draft').hide();
        } */
        /* $('select').on('change', function(e) {
            var type = $('#payment_gateway :selected').val();
            if (type == enum_omise || type == enum_mc_payment) {
                $('#ref_file').show();
                $('#date_payment').show();
                $('#payment_status_draft').show();
                flatpickr("#payment_date", {
                    defaultDate: "today",
                    maxDate: "today",
                });
            } else {
                // console.log('sf');
                $('#ref_file').hide();
                $('#date_payment').hide();
                $('#payment_status_draft').hide();

            }
        }); */

        // var enum_smart_car = '{{ \App\Enums\OrderChannelEnum::SMARTCAR }}';
        var enum_other = '{{ \App\Enums\OrderChannelEnum::OTHER }}';
        var order_channel = '{{ $order_channel }}';
        var enum_pending = '{{ \App\Enums\RentalStatusEnum::PENDING }}';
        var status = '{{ $d->status }}';
        /* if (order_channel == enum_other) {
            $('#payment_gateway_id').show();
            $('#payment_remark_id').show();
            $('#payment_status_id').show();
        } else {
            $('#payment_gateway_id').hide();
            $('#payment_remark_id').hide();
            $('#payment_status_id').hide();
            $('#ref_file').hide();
            $('#date_payment').hide();
        }
        if (status == enum_pending) {
            $('input[name="order_channel_disabled"]').prop('disabled', true);
        } else {
            $('input[name="order_channel_disabled"]').prop('disabled', false);
        } */
        /* $('input[name="order_channel"]').on("click", function() {
            var order_channel = $('input[name="order_channel"]:checked').val();
            if (order_channel === enum_other) {
                $('#payment_gateway_id').show();
                $('#payment_remark_id').show();
                $('#payment_status_id').show();
            } else {
                $('#payment_gateway_id').hide();
                $('#payment_remark_id').hide();
                $('#payment_status_id').hide();
                $('#ref_file').hide();
                $('#date_payment').hide();
                $('[name="payment_gateway"]').val('').trigger('change');
            }
        }); */

        $('.copy_link').click(function (e) {
            e.preventDefault();
            var copyText = $('#link').val();
            document.addEventListener('copy', function (e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            copyAlert('คัดลอกสำเร็จ');

        });

        document.getElementById('active_tax').onclick = function () {
            if (!this.checked) {
                $('[name="withholding_tax[]"]').prop('checked', false);
            }
            toggleSub(this, 'active_sub');
        };

        function toggleSub(box, id) {
            var el = document.getElementById(id);
            if (box.checked) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }

        function updateWithholdingTax() {
            var params = {
                is_withholding_tax: $('#active_tax').is(':checked'),
                withholding_tax_value: $('input[type=radio][name=withholding_tax_value]:checked').val(),
                rental_id: $('#rental_id').val()
            };
            axios.post("{{ route('admin.short-term-rental.summary.update-withholding-tax') }}", params).then(response => {
                if (response.data.success) {
                    addRentalVue.setSummary(response.data.summary);
                }
            });
        }

        $('#active_tax').change(function () {
            updateWithholdingTax();
        });

        $('input[type=radio][name=withholding_tax_value]').change(function () {
            updateWithholdingTax();
        });

        $('.btn-rental-update-status').on('click', function () {
            var status = $(this).attr('data-status');
            let storeUri = '{{ route('admin.short-term-rental.summary.store') }}';
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

                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }

            formData.append('change_status', status);
            saveForm(storeUri, formData);
        });

        $('.btn-save-form-append').on('click', function () {
            // var status = $(this).attr('data-status');
            let storeUri = '{{ route('admin.short-term-rental.summary.store') }}';
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

                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
