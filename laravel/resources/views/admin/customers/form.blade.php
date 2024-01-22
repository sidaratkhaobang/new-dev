@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    <x-blocks.block :title="__('customers.info_customer_table')" >
        @include('admin.customers.sections.info')
    </x-blocks.block>

    <x-blocks.block :title="__('customers.billing_address_table')" >
        @if (isset($view))
            @include('admin.customers.sections.views.billing-address')
        @else
            @include('admin.customers.sections.billing-address')
        @endif
    </x-blocks.block>

    <x-blocks.block :title="__('customers.driver_table')" >
        @if (isset($view))
            @include('admin.customers.sections.views.driver')
        @else
            @include('admin.customers.sections.driver')
        @endif
    </x-blocks.block>

    <x-forms.hidden id="id" :value="$d->id" />

    <x-blocks.block>
        <x-forms.submit-group :optionals="['url' => 'admin.customers.index', 'view' => empty($view) ? null : $view]"/>
    </x-blocks.block>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.customers.store'),
])
@include('admin.customers.scripts.billing-address-script')
@include('admin.customers.scripts.driver-script')

@include('admin.components.select2-ajax', [
    'id' => 'province_id',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'district_id',
    'url' => route('admin.util.select2.districts'),
    'parent_id' => 'province_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'subdistrict_id',
    'url' => route('admin.util.select2.subdistricts'),
    'parent_id' => 'district_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'province_field',
    'modal' => '#modal-billing-address',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'district_field',
    'parent_id' => 'province_field',
    'modal' => '#modal-billing-address',
    'url' => route('admin.util.select2.districts'),
])

@include('admin.components.select2-ajax', [
    'id' => 'subdistrict_field',
    'parent_id' => 'district_field',
    'modal' => '#modal-billing-address',
    'url' => route('admin.util.select2.subdistricts'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'driving_license_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])
@include('admin.components.upload-image', [
    'id' => 'citizen_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#customer_code').prop('disabled', true);
            $('#debtor_code').prop('disabled', true);
            $('#customer_type').prop('disabled', true);
            $('#customer_grade').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#tax_no').prop('disabled', true);
            $('#email').prop('disabled', true);
            $('#fullname_th').prop('disabled', true);
            $('#fullname_en').prop('disabled', true);
            $('#prefixname_th').prop('disabled', true);
            $('#prefixname_en').prop('disabled', true);
            $('#address').prop('disabled', true);
            $('#province_id').prop('disabled', true);
            $('#district_id').prop('disabled', true);
            $('#subdistrict_id').prop('disabled', true);
            $('#fax').prop('disabled', true);
            $('#tel').prop('disabled', true);
            $('#phone').prop('disabled', true);
            $('#sale_id').prop('disabled', true);
            $('[name="customer_group[]"]').prop('disabled', true);
        }

        $(".btn-save-form-data").on("click", function() {
            let storeUri = "{{ route('admin.customers.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.addCustomerDriverVue) {
                let data = window.addCustomerDriverVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.driver_license_files && item.driver_license_files.length > 0) {
                            item.driver_license_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('driver_license_file[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }
                        if (item.driver_citizen_files && item.driver_citizen_files.length > 0) {
                            item.driver_citizen_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('driver_citizen_files[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addCustomerDriverVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_license_files && item.pending_delete_license_files.length >
                            0) {
                            item.pending_delete_license_files.forEach(function(id) {
                                formData.append('pending_delete_license_files[]', id);
                            });
                        }
                        if (item.pending_delete_citizen_files && item.pending_delete_citizen_files.length >
                            0) {
                            item.pending_delete_citizen_files.forEach(function(id) {
                                formData.append('pending_delete_citizen_files[]', id);
                            });
                        }
                    });
                }
                //delete driver row
                let delete_driver_ids = window.addCustomerDriverVue.pending_delete_driver_ids;
                if (delete_driver_ids && (delete_driver_ids.length > 0)) {
                    delete_driver_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_driver_ids[]', delete_driver_id);
                    });
                }
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
