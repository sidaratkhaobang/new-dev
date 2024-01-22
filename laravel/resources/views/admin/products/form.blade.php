@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <form id="save-form">
        <x-blocks.block :title="__('products.product_detail')" :optionals="['is_toggle' => false]">
            @include('admin.products.sections.product-detail')
            <x-forms.hidden id="id" :value="$d->id"/>
        </x-blocks.block>
        <x-blocks.block :title="__('product_additionals.page_title')" :optionals="['is_toggle' => false]">
            @if (isset($view))
                @include('admin.products.sections.views.product-additional')
            @else
                @include('admin.products.sections.product-additional')
            @endif
        </x-blocks.block>

        <x-blocks.block>
            <x-forms.submit-group :optionals="[
                'url' => 'admin.products.index',
                'view' => empty($view) ? null : $view,
                'manage_permission' => Actions::Manage . '_' . Resources::Product,
            ]">
            </x-forms.submit-group>
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.products.store'),
])

@include('admin.components.date-input-script')
@include('admin.products.scripts.product-additional-script')

@include('admin.components.select2-ajax', [
    'id' => 'product_additional_id_field',
    'modal' => '#modal-product-additional',
    'url' => route('admin.util.select2.product-additionals'),
])

@include('admin.components.select2-ajax', [
    'id' => 'service_type_id',
    'url' => route('admin.util.select2.service-types'),
])



@push('scripts')
    <script>
        $('#price_field').prop('disabled', true);
        var product_additional_list = @json($product_additionals);
        console.log(product_additional_list);
        $('#product_additional_id_field').on('select2:select', function (e) {
            var data = e.params.data;
            let product_additional = product_additional_list.find(o => o.id === data.id);
            price = product_additional.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $('#price_field').val(price);
        });

        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#sku').prop('disabled', true);
            $('#standard_price').prop('disabled', true);
            $('#service_type_id').prop('disabled', true);
            $('#branch_id').prop('disabled', true);
            $('input[name="calculate_type"]').prop('disabled', true);
            $('input[name="reserve_date[]"]').prop('disabled', true);
            $('input[name="status"]').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#start_booking_time').prop('disabled', true);
            $('#end_booking_time').prop('disabled', true);
            $('#reserve_booking_duration').prop('disabled', true);
            $('input[name="is_used_application"]').prop('disabled', true);
            $('[name="gl_account[]"]').prop('disabled', true);
            $('[name="car_type[]"]').prop('disabled', true);
            $('#fix_days').prop('disabled', true);
            $('#fix_return_time').prop('disabled', true);
        }

        $(".btn-save-form-set-price").on("click", function () {
            let storeUri = "{{ route('admin.products.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('set_price', true);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
