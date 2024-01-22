@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('long_term_rental_types.name')" :optionals="['required' => true], 'maxlength' => 100" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="type" :value="$d->type" :list="$type_lists" :label="__('long_term_rental_types.type')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.long-term-rental-types.index', 
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::LongTermRentalType
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rental-types.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#type').prop('disabled', true);
        }
    </script>
@endpush
