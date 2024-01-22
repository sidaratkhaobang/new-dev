@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-2"></div>

                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('accessories.code')"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('accessories.name')"
                                                :optionals="['required' => true]"/>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="version" :value="$d->version" :label="__('accessories.version')"
                                                :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="price" :value="$d->price" :label="__('accessories.price')"
                                                :optionals="['required' => true, 'oninput'=> true ]"/>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="creditor_id" :value="$d->creditor_id" :list="$dealers"
                                               :label="__('accessories.dealer')"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="accessory_type" :value="$d?->accessory_type" :list="$type_list"
                                              :label="__('accessories.type')"/>
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id"/>
                <x-forms.submit-group
                    :optionals="['url' => 'admin.accessories.index','view' => empty($view) ? null : $view]"/>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.accessories.store'),
])


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#code').prop('disabled', true);
            $('#version').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#detail').prop('disabled', true);
            $('#price').prop('disabled', true);
            $('#creditor_id').prop('disabled', true);
        }

    </script>
@endpush
