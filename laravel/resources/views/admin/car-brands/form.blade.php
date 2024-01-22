@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <form id="save-form" >
            <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="code" :value="$d->code" :label="__('car_brands.code')" :optionals="['required' => true, 'maxlength' => 3]" />
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_brands.name')" :optionals="['required' => true], 'maxlength' => 100" />
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    @if (isset($view))
                        <x-forms.view-image :id="'car_brand_images'" :label="__('car_brands.images')" :list="$car_brand_images" />
                    @else
                        <x-forms.upload-image :id="'car_brand_images'" :label="__('car_brands.images')"/>
                    @endif
                </div>
            </div>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.car-brands.index','view' => empty($view) ? null : $view ]"/>
        </form>
    </div>
</div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-brands.store')
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'car_brand_images',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $car_brand_images,
    'show_url' => true
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#code').prop('disabled', true);
    $('#name').prop('disabled', true);
    }

</script>
@endpush

