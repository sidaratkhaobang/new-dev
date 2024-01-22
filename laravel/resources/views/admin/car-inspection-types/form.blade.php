@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('styles')
<style>
    .th-min-width {
        min-width: 400px;
    }
    .th-min-width-sm {
        min-width: 300px;
    }
</style>
@endpush
@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspection_types.inspection_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspection-types.sections.inspection-detail')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspection_types.inspection_seq_out'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspection-types.sections.inspection-seq-out')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_inspection_types.inspection_seq_in'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.car-inspection-types.sections.inspection-seq-in')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.submit-group :optionals="['url' => 'admin.car-inspection-types.index', 'view' => empty($view) ? null : $view]" />
        </div>
    </div>
</form>
@include('admin.car-inspection-types.modals.confirm')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-inspection-types.store'),
])
@include('admin.components.date-input-script')
@include('admin.car-inspection-types.scripts.car-inspection-script')
@include('admin.car-inspection-types.scripts.car-inspection-script2')

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $("input[type=radio]").attr('disabled', true);

        }
        $('#inspect_type_name').prop('disabled', true);
        $('#car_type_name').prop('disabled', true);
        $('#rental_type').prop('disabled', true);

        $(".btn-save-review").on("click", function() {
            let storeUri = "{{ route('admin.car-inspection-types.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            saveForm(storeUri, formData);
            $("#modal-confirm").modal("hide");
        });
    </script>
@endpush
