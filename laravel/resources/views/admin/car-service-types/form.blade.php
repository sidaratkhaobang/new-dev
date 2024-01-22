@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<x-blocks.block>
    <form id="save-form">
        <div class="row push">
            <div class="col-sm-4">
                <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('cars.license_plate')" :optionals="['required' => true]" />
            </div>
        </div>

        <div class="row push">
            <div class="col-sm-4">
                <x-forms.select-option id="service_types[]" :value="$car_service_type_array" :list="$service_type_list" :label="__('car_service_types.service_type')"
                    :optionals="['multiple' => true, 'required' => true]" />
            </div>
            <div class="col-sm-4">
                @if (isset($view))
                    <x-forms.view-image :id="'car_images'" :label="__('service_types.images')" :list="$car_images_files" />
                @else
                    <x-forms.upload-image :id="'car_images'" :label="__('car_service_types.images')" />
                @endif
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.submit-group :optionals="[
            'url' => 'admin.car-service-types.index',
            'view' => empty($view) ? null : $view,
            'manage_permission' => Actions::Manage . '_' . Resources::CarServiceType,
        ]" />
    </form>
</x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-service-types.store'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'car_images',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $car_images_files,
    'show_url' => true
])

@push('scripts')
    <script>
        $('#engine_no').prop('disabled', true);
        $('#chassis_no').prop('disabled', true);
        $('#license_plate').prop('disabled', true);
        $status = '{{ isset($view) }}';
        if ($status) {
            $('[name="service_types[]"]').prop('disabled', true);
        }
    </script>
@endpush
