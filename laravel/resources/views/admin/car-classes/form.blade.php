@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.car-classes.section.car-specs')
                @include('admin.car-classes.section.car-accessories')
                @include('admin.car-classes.section.car-class-colors')
                @include('admin.car-classes.section.car-class-upload')
                @include('admin.car-classes.section.car-class-website')

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.car-classes.index']" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-classes.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_type_id',
    'url' => route('admin.util.select2.car-type'),
    'parent_id' => 'car_brand_id',
])
@include('admin.components.select2-ajax', [
    'id' => 'car_category_id',
    'url' => route('admin.util.select2.car-category'),
    'parent_id' => 'car_type_id',
])

@include('admin.car-classes.scripts.car-accessory-script')
@include('admin.car-classes.scripts.car-class-color-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
'id' => 'images',
'max_files' => 10,
'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
])

@include('admin.components.upload-image', [
'id' => 'car_images',
'max_files' => 10,
'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
'mock_files' => $car_images_files
])

@include('admin.components.select2-ajax', [
'id' => 'color_field',
'modal' => '#modal-car-class-color',
'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
'id' => 'accessory_field',
'modal' => '#modal-class-car-accessory',
'url' => route('admin.util.select2.accessories-type-accessory'),
])

{{-- @include('admin.components.select2-ajax', [
'id' => 'accessory_version_field',
'modal' => '#modal-class-car-accessory',
'url' => route('admin.util.select2.accessory-versions'),
])
 --}}