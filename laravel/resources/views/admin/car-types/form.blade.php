@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_brand_id" :value="$d->car_brand_id" :list="$car_brand_lists" :label="__('car_brands.page_title')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('car_types.code')"  />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_types.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_category_id" :value="$d->car_category_id" :list="$car_category_lists"
                                               :label="__('car_categories.page_title')" />
                    </div>

                </div>

                <div class="row push mb-4">

                    <div class="col-sm-3">
                        <x-forms.select-option id="car_group_id" :value="$d->car_group_id" :list="$car_group_lists"
                                               :label="__('car_types.car_group')" />
                    </div>

                </div>
                {{-- <div class="row push mb-4">
                <div class="col-sm-2"></div>
                <div class="col-sm-4">
                    <x-forms.select-option id="car_category_type_id" :value="$d->car_category_type_id" :list="$car_category_types" :label="null" />
                </div>
            </div> --}}

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.car-types.index','view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-types.store'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if($status){
            $('#code').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#car_brand_id').prop('disabled', true);
            $('#car_category_id').prop('disabled', true);
            $('#car_group_id').prop('disabled', true);
        }

    </script>
@endpush