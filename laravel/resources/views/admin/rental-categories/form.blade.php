@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('rental_categories.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$listStatus" :label="__('rental_categories.status')" :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                    </div>
                </div>


                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="service_type[]" :value="$service_type" :list="$service_type_list" :label="__('rental_categories.service_category')"
                        :optionals="['multiple' => true,'required' => true]" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
   
                <x-forms.submit-group  :optionals="['url' => 'admin.rental-categories.index','view' => empty($view) ? null : $view]" 
                />
                {{-- @endif --}}
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.rental-categories.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#name').prop('disabled', true);
    $('#location_group_id').prop('disabled', true);
    $('#province').prop('disabled', true);
    $('#lat').prop('disabled', true);
    $('#lng').prop('disabled', true);
    $("input[type=radio]").attr('disabled', true);
    $("input[type=checkbox]").attr('disabled', true);
    $('[name="service_type[]"]').prop('disabled', true);
    }

</script>

@endpush