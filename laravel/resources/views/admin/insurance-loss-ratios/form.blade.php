@extends('admin.layouts.layout')
@section('page_title',$page_title.' '.$d->worksheet_no)
@section('history')
    @include('admin.components.btns.history')
@endsection
@section('content')
    <form id="save-form">
        @include('admin.components.creator')
        <x-forms.hidden id="accident_id" :value="$d->id"/>
        @include('admin.insurance-deducts.sections.form-car-customer-detail')
        @include('admin.insurance-deducts.sections.form-accident-detail')
        @include('admin.insurance-deducts.sections.form-insurance-detail')
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <x-forms.submit-group
                        :optionals="['url' => 'admin.insurance-loss-ratios.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceDeduct]"/>
                </div>
            </div>
        </div>
    </form>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.insurance-deducts.store'),
])
@push('scripts')
    <script>
        let mode = "{{$mode}}"

        if (mode == "{{MODE_VIEW}}") {
            $('#deduct_date').prop('disabled', true)
            $('#deduct_doc_dd').prop('disabled', true)
            $('#deduct_all_damage').prop('disabled', true)
            $('#deduct_deductible').prop('disabled', true)
        }
    </script>
@endpush
