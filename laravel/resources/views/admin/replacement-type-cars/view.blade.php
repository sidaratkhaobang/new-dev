@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.replacement-type-cars.sections.car-info')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.replacement-type-cars.sections.history')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row push">
                <div class="text-end">
                    <a class="btn btn-secondary"
                        href="{{ route('admin.replacement-type-cars.index') }}">{{ __('lang.back') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#code').prop('disabled', true);
        $('#license_plate').prop('disabled', true);
        $('#engine_no').prop('disabled', true);
        $('#chassis_no').prop('disabled', true);
        $('#car_class_id').prop('disabled', true);
        $('#car_color_id').prop('disabled', true);
        $('#car_brand_id').prop('disabled', true);
        $('#registration_date').prop('disabled', true);
        $('#car_age').prop('disabled', true);
        $('#usage_start_date').prop('disabled', true);
        $('#car_age_in_storage').prop('disabled', true);
    </script>
@endpush
