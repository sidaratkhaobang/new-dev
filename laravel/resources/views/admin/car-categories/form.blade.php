@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('car_categories.code')"
                            :optionals="['required' => true, 'maxlength' => 3]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_categories.name')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="car_group_id" :value="$d->car_group_id" :list="$car_group_name_list" :label="__('car_categories.car_group')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="reserve_small_size" :value="$d->reserve_small_size" :label="__('car_categories.reserve_small_size')" :optionals="['required' => true,'type' => 'number', 'min' => 1]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="reserve_big_size" :value="$d->reserve_big_size" :label="__('car_categories.reserve_big_size')" :optionals="['required' => true,'type' => 'number', 'min' => 1]" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.car-categories.index','view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-categories.store'),
])

@push('scripts')
<script>
    $status = '{{ isset($view) }}';
    if($status){
    $('#code').prop('disabled', true);
    $('#name').prop('disabled', true);
    $('#car_group_id').prop('disabled', true);
    $('#reserve_small_size').prop('disabled', true);
    $('#reserve_big_size').prop('disabled', true);
    }

</script>
@endpush
