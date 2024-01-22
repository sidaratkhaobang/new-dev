@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('car_rental_categories.engine_no')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('car_rental_categories.chassis_no')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('car_rental_categories.license_plate')" :optionals="['required' => true]" />
                    </div>
                </div>


                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="rental_category[]" :value="$car_rental_category" :list="$rental_category_list" :label="__('car_rental_categories.rental_category')"
                        :optionals="['multiple' => true,'required' => true]" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
   
                <x-forms.submit-group  :optionals="['url' => 'admin.car-rental-categories.index','view' => empty($view) ? null : $view]" 
                />
                {{-- @endif --}}
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-rental-categories.store'),
])

@push('scripts')
<script>
    
    $('#engine_no').prop('disabled', true);
    $('#chassis_no').prop('disabled', true);
    $('#license_plate').prop('disabled', true);
    $status = '{{ isset($view) }}';
    if($status){
    $('[name="rental_category[]"]').prop('disabled', true);
    }

</script>
@endpush