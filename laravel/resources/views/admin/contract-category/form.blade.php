@extends('admin.layouts.layout')
@section('page_title', __('contract_category.form.page_title'))
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-5">
                        <x-forms.input-new-line id="condition_name" :value="$data->name" :label="__('contract_category.form.condition_name')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                    </div>
                </div>
                @include('admin.contract-category.sections.question-detail')
                <x-forms.hidden id="id" :value="$data->id"/>
                <div class="row">
                    <div class="text-end">
                        <a class="btn btn-outline-secondary btn-custom-size me-1" href="{{ route('admin.contract-category.index') }}">{{ __('lang.back') }}</a>
                        @if (Route::is('*.edit') || Route::is('*.create'))
                            <button type="button" class="btn btn-primary btn-save-form btn-custom-size">{{ __('contract_category.form.btn-save') }}</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.contract-category.store'),
])
@include('admin.contract-category.scripts.contract-category-script')

@push('scripts')
    <script>
        $('.form-control').prop('disabled' , {{Route::is('*.show')}});
        $('.form-check-input').prop('disabled' , {{Route::is('*.show')}});

        @if(Route::is('*.show'))
            $('input').removeAttr('name');
        @endif
    </script>
@endpush
