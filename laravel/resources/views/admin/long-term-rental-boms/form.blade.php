@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="bom_no" :value="$d->worksheet_no" :label="__('long_term_rental_boms.bom_no')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('long_term_rental_boms.name')" :optionals="['required' => true], 'maxlength' => 100" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="type" :value="$d->type" :list="$type_lists" :label="__('long_term_rental_boms.type')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="remark" :value="$d->remark" :label="__('long_term_rental_boms.remark')" />
                    </div>
                </div>
                @if (isset($edit))
                    <x-forms.hidden id="edit" :value="$edit" />
                    <x-forms.hidden id="type_hidden" :value="$d->type" />
                @endif
                <x-forms.hidden id="id" :value="$d->id" />
                <h4 id="text-topic"></h4>
                <hr id="hr">
                @include('admin.long-term-rental-boms.sections.pr-car-accessory')
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.long-term-rental-boms.index', 
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::LongTermRentalBom,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.long-term-rental-boms.scripts.pr-car-script')
@include('admin.long-term-rental-boms.scripts.pr-accessory-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental-boms.store'),
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
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@push('scripts')
    <script>
        $('#bom_no').prop('disabled', true);
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#type').prop('disabled', true);
            $('#remark').prop('disabled', true);
        }
        $status = '{{ isset($edit) }}';
        if ($status) {
            $('#type').prop('disabled', true);
        }

        $(document).ready(function() {
            var type_work = '{{ $d->type }}';
            var enum_car = '{{ \App\Enums\LongTermRentalTypeEnum::CAR }}';
            var enum_accessory = '{{ \App\Enums\LongTermRentalTypeEnum::ACCESSORY }}';
            if (type_work != '') {
                if (type_work == enum_car) {
                    $('#pr-car').show();
                    $('#text-topic').show();
                    $('#pr-accessory').hide();
                    $('#text-topic').text('ข้อมูลรถ');
                    $('#acc-form').hide();
                    $('#hr').show();
                } else if (type_work == enum_accessory) {
                    $('#pr-car').hide();
                    $('#pr-accessory').show();
                    $('#text-topic').show();
                    $('#text-topic').text('ข้อมูลอุปกรณ์');
                    $('#acc-form').show();
                    $('#hr').show();
                } else {
                    $('#pr-car').hide();
                    $('#pr-accessory').hide();
                    $('#text-topic').hide();
                    $('#acc-form').hide();
                    $('#hr').hide();
                }
            } else {
                $('#pr-car').hide();
                $('#pr-accessory').hide();
                $('#text-topic').hide();
                $('#acc-form').hide();
                $('#hr').hide();
            }
            $('#type').change(function() {
                var type = $('#type :selected').val();
                var enum_car = '{{ \App\Enums\LongTermRentalTypeEnum::CAR }}';
                var enum_accessory = '{{ \App\Enums\LongTermRentalTypeEnum::ACCESSORY }}';
                if (type == enum_car) {
                    $('#pr-car').show();
                    $('#text-topic').show();
                    $('#pr-accessory').hide();
                    $('#text-topic').text('ข้อมูลรถ');
                    $('#acc-form').hide();
                    $('#hr').show();
                } else if (type == enum_accessory) {
                    $('#pr-car').hide();
                    $('#pr-accessory').show();
                    $('#text-topic').show();
                    $('#text-topic').text('ข้อมูลอุปกรณ์');
                    $('#acc-form').show();
                    $('#hr').show();
                } else {
                    $('#pr-car').hide();
                    $('#pr-accessory').hide();
                    $('#text-topic').text('');
                    $('#acc-form').hide();
                    $('#hr').hide();
                }

            });
        });
    </script>
@endpush
