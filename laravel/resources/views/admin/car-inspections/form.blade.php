@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspections.inspection_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspections.sections.inspection-detail')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspections.question_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspections.sections.question-detail')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspections.question_additional_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspections.sections.question-additional-detail')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.car-inspections.index', 'view' => empty($view) ? null : $view]" />
        </div>
    </div>
</form>
@include('admin.car-inspections.modals.confirm')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-inspections.store'),
])
@include('admin.components.date-input-script')
@include('admin.car-inspections.scripts.car-inspection-script')

@include('admin.components.select2-ajax', [
    'id' => 'location_group_field',
    'url' => route('admin.util.select2.location-groups'),
])

@include('admin.components.select2-ajax', [
    'id' => 'location_field',
    'url' => route('admin.util.select2.locations'),
    'parent_id' => 'location_group_field',
])

@push('scripts')
    <script>

$status = '{{ isset($view) }}';
    
    if($status){
        $('.form-control').prop('disabled', true);
        $("input[type=checkbox]").attr('disabled', true);
        
    }
    $(".btn-save-review").on("click", function() {
        let storeUri = "{{ route('admin.car-inspections.store') }}";
        var formData = new FormData(document.querySelector('#save-form'));
        var status = $(this).attr('data-status');
            saveForm(storeUri,formData);
            $("#modal-confirm").modal("hide");
    });
</script>
@endpush
