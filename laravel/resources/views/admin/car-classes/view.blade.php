@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.car-classes.section.view.car-specs')
                @include('admin.car-classes.section.view.car-accessories')
                @include('admin.car-classes.section.view.car-class-colors')
                @include('admin.car-classes.section.view.car-class-upload')
                @include('admin.car-classes.section.view.car-class-website')

                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ route('admin.car-classes.index') }}" >{{ __('lang.back') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
'id' => 'images',
'max_files' => 10,
'view_only' => true,
'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
])

@include('admin.components.upload-image', [
'id' => 'car_images',
'max_files' => 10,
'view_only' => true,
'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
'mock_files' => $car_images_files
])

@push('scripts')
<script>
    $('#name').prop('disabled', true);
    $('#car_brand_id').prop('disabled', true);
    $('#car_type_id').prop('disabled', true);
    $('#car_category_id').prop('disabled', true);
    $('#full_name').prop('disabled', true);
    $('#manufacturing_year').prop('disabled', true);
    $('#description').prop('disabled', true);
    $('#engine_size').prop('disabled', true);
    $('#gear_id').prop('disabled', true);
    $('#drive_system_id').prop('disabled', true);
    $('#central_lock_id').prop('disabled', true);
    $('#car_seat_id').prop('disabled', true);
    $('#air_bag_id').prop('disabled', true);
    $('#side_mirror_id').prop('disabled', true);
    $('#anti_thift_system_id').prop('disabled', true);
    $('#abs_id').prop('disabled', true);
    $('#front_brake_id').prop('disabled', true);
    $('#rear_brake_id').prop('disabled', true);
    $('#car_tire_id').prop('disabled', true);
    $('#car_battery_id').prop('disabled', true);
    $('#oil_type').prop('disabled', true);
    $('#oil_tank_capacity').prop('disabled', true);
    $('#car_wiper_id').prop('disabled', true);
    $('#remark').prop('disabled', true);
    $('#website').prop('disabled', true);

</script>
@endpush
