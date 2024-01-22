@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        <button type="button" onclick="openCheckHistory()" class="btn btn-primary"><i
                class="fa fa-arrow-rotate-left opacity-50 me-1"></i>&nbsp;
            ประวัติเช็กระยะ</button>
    </nav>
@endsection

@section('content')
    <form id="save-form">
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="d-md-flex justify-content-md-between align-items-md-center">
                    <div class="">
                        <h4><i class="fa fa-file-lines me-1"></i>
                            {{ __('check_distance_notices.car_table') }}</h4>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('cars.code')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('cars.license_plate')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="brand" :value="$car_brand_name" :label="__('check_distance_notices.brand')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="clsas" :value="$car_class_name" :label="__('check_distance_notices.clsas')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="color" :value="$car_color_name" :label="__('car_classes.color')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="registration_date" :value="$d->registered_date" :label="__('cars.registration_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="car_age" :value="$car_age" :label="__('cars.car_age')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="start_system_date" :value="$d->start_date" :label="__('cars.start_system_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="car_storage_age" :value="$car_age_start" :label="__('cars.car_storage_age')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="mileage" :value="$d->current_mileage" :label="__('check_distance_notices.mileage')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="d-md-flex justify-content-md-between align-items-md-center">
                    <div class="">
                        <h4><i class="fa fa-file-lines me-1"></i>
                            {{ __('check_distance_notices.customer_table') }}</h4>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="customer_name" :value="$rental_customer_name" :label="__('check_distance_notices.customer_name')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="customer_tel" :value="$rental_customer_tel" :label="__('check_distance_notices.tel')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="rental_type" :value="$rental_job_type" :label="__('check_distance_notices.rental_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="rental_duration" :value="$rental_duration" :label="__('check_distance_notices.rental_duration')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="rental_no" :value="$rental_no" :label="__('check_distance_notices.rental_no')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="d-md-flex justify-content-md-between align-items-md-center">
                    <div class="">
                        <h4><i class="fa fa-file-lines me-1"></i>
                            {{ __('check_distance_notices.check_table') }}</h4>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="check_latest" :value="$check_latest" :label="__('check_distance_notices.check_latest')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="check_latest_date" :value="$check_latest_date" :label="__('check_distance_notices.check_latest_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="check_next" :value="$check_next" :label="__('check_distance_notices.check_next')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="check_next_date" :value="$check_next_date" :label="__('check_distance_notices.check_next_date')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="contact_latest" :value="$contact_latest" :label="__('check_distance_notices.contact_latest')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="contact_tel" :value="$contact_tel" :label="__('check_distance_notices.tel')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="repair_latest" :value="$contact_latest" :label="__('check_distance_notices.repair_latest')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="repair_tel" :value="$contact_tel" :label="__('check_distance_notices.repair_tel')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route('admin.check-distance-notices.index') }}">{{ __('lang.back') }}</a>
                    </div>
                </div>
            </div>
            @include('admin.check-distance-notices.modals.check-history-modal')
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.check-distance-notices.store'),
])

@push('scripts')
    <script>
        function openCheckHistory() {
            $('#modal-repair-history').modal('show');
        }

        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }
    </script>
@endpush
