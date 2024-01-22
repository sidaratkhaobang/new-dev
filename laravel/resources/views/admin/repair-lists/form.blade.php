@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('repair_lists.code')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('repair_lists.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="price" :value="$d->price" :label="__('repair_lists.price')" :optionals="['input_class' => 'number-format col-sm-4']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="[
                            ['name' => __('repair_lists.status_' . STATUS_ACTIVE), 'value' => STATUS_ACTIVE],
                            ['name' => __('repair_lists.status_' . STATUS_INACTIVE), 'value' => STATUS_INACTIVE],
                        ]" :label="__('lang.status')" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />

                <x-forms.submit-group :optionals="[
                    'url' => 'admin.repair-lists.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.repair-lists.store'),
])

@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#code').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#price').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }
    </script>
@endpush
