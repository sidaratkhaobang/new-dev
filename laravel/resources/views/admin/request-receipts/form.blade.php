@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d?->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('request_receipts.class_' . $d->status), __('request_receipts.text_' . $d->status), null) !!}
    @endif
@endsection
@push('styles')
    <style>
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        <x-blocks.block :title="__('request_receipts.receipt_detail')">
            @include('admin.request-receipts.sections.receipt-detail')
        </x-blocks.block>
        <x-blocks.block :title="__('request_receipts.customer_detail')">
            @include('admin.request-receipts.sections.customer-detail')
        </x-blocks.block>
        <x-blocks.block :title="__('request_receipts.list_detail')">
            @if (!isset($view))
                <x-slot name="btn_option">
                    @can(Actions::Manage . '_' . Resources::RequestReceipt)
                        <button type="button" class="btn btn-primary" onclick="addList()"
                            id="openModal">{{ __('lang.add') }}</button>
                    @endcan
                </x-slot>
            @endif
            @include('admin.request-receipts.sections.list-detail')
        </x-blocks.block>

        <x-forms.hidden id="id" :value="$d?->id" />
    </form>
    <x-blocks.block>
        <x-forms.submit-group :optionals="[
            'view' => empty($view) ? null : $view,
            'isdraft' => true,
            'btn_name' => __('request_receipts.save'),
            'btn_draft_name' => __('request_receipts.save_draft'),
            'icon_class_name' => 'icon-send',
            'data_status' => RequestReceiptStatusEnum::WAITING_RECEIPT,
        ]"></x-forms.submit-group>
    </x-blocks.block>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.request-receipts.store'),
])
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'customer_id',
    'parent_id' => 'customer_type',
    'url' => route('admin.util.select2-customer.customer-codes'),
])
@include('admin.components.select2-ajax', [
    'id' => 'customer_province_id',
    'url' => route('admin.util.select2-garage.province'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_district_id',
    'url' => route('admin.util.select2-garage.amphure'),
    'parent_id' => 'customer_province_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_subdistrict_id',
    'url' => route('admin.util.select2-garage.district'),
    'parent_id' => 'customer_district_id',
])
@include('admin.request-receipts.scripts.request-receipt-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $optional_files,
    'show_url' => true,
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
        }
        $('#customer_zipcode').prop('disabled', true);
        $('#total').prop('disabled', true);

        $('#customer_id').on('change', function(e) {
            clearCustomerDetail();
        });

        $('#customer_id').on('select2:select', function(e) {
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

        async function populateDropdown(url, targetId, dataId, selectedValue) {
            try {
                const response = await fetch(`${url}?parent_id=${dataId}`);
                const data = await response.json();
                const options = data.map(item => `<option value="${item.id}">${item.text}</option>`).join('');
                $(`#${targetId}`).html(options).val(selectedValue).trigger('change');
            } catch (error) {
                // 
            }
        }

        function addCustomerDetail(data) {
            populateDropdown("{{ route('admin.util.select2-request-receipt.province') }}", "customer_province_id", null,
                data.province_id);
            populateDropdown("{{ route('admin.util.select2-request-receipt.amphure') }}", "customer_district_id", data
                .province_id, data.district_id);
            populateDropdown("{{ route('admin.util.select2-request-receipt.district') }}", "customer_subdistrict_id", data
                .district_id, data.subdistrict_id);

            $('#customer_name').val(data.name);
            $('#customer_tax').val(data.tax_no);
            $('#customer_email').val(data.email);
            $('#customer_tel').val(data.tel);
            $('#customer_zipcode').val(data.zip_code);
            $('#customer_address').val(data.address);
        }


        $('#customer_subdistrict_id').on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-request-receipt.zipcode') }}", {
                params: {
                    customer_subdistrict_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    $('#customer_zipcode').val(response.data.data.zip_code);
                }
            });
        });
        function clearCustomerDetail() {
            $('#customer_name').val('');
            $('#customer_email').val('');
            $('#customer_tel').val('');
            $('#customer_province_id').val(null).trigger('change');
            $('#customer_subdistrict_id').val(null).trigger('change');
            $('#customer_district_id').val(null).trigger('change');
            $('#customer_zipcode').val('');
            $('#customer_address').val('');
            $('#customer_tax_no').val('');
        }

        $(document).ready(function() {
            $('input[name="is_select_db_customer[]"]').on("click", function() {
                clearCustomerDetail();
                var isChecked = $(this).is(':checked');
                $('#customer_id').val('').trigger('change');
                $('#customer_id_section').css('display', isChecked ? 'block' : 'none');
                $('#customer_name_section').css('display', isChecked ? 'none' : 'block');
            });
        });
    </script>
@endpush
