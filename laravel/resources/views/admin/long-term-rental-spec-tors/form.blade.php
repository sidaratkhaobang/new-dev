@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('styles')
    <style>
        .tr-last-item .td-table {
            border-bottom-width: 1.5px !important;
            border-color: #a8aec2;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
        </div>
        <div class="block-content">
            <form id="save-form">
                @include('admin.long-term-rental-spec-tors.sections.tor-description')
                @include('admin.long-term-rental-spec-tors.sections.car-accessory')
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="lt_rental_id" :value="$lt_rental_id" />
                <div class="row push mt-3">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ $redirect_route }}">{{ __('lang.back') }}</a>
                        @if (!isset($view_only))
                            @if (isset($accessory_controller))
                                <button type="button"
                                    class="btn btn-primary btn-save-form-accessory">{{ __('lang.save') }}</button>
                            @else
                                <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                            @endif
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental.specs.tor.store'),
])
@include('admin.long-term-rental-spec-tors.scripts.car-script')
@include('admin.long-term-rental-spec-tors.scripts.accessory-script')

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

@push('scripts')
    <script>
        $('#amount_accessory_field').prop('readonly', true);
        $("#amount_per_car_accessory_field").keyup(function() {
            var amount_car = document.getElementById("amount_car_field").value;
            var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field").value;

            $('#amount_accessory_field').val(amount_per_car_accessory * amount_car);
        });

        $("#amount_car_field").keyup(function() {
            var amount_car = document.getElementById("amount_car_field").value;
            var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field").value;

            $('#amount_accessory_field').val(amount_per_car_accessory * amount_car);
        });
        var view_only = '{{ isset($view_only) ? true : false }}';
        if (view_only) {
            $('#remark_tor').prop('disabled', true);
        }
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-save-form-accessory").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rental.specs.tor.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('accessory_controller', true);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
