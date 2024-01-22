@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('product_prices.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="price" :value="number_format($d->price,2)" :label="__('product_prices.price')" :optionals="['required' => true,'input_class' => 'number-format col-sm-4']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="priority" :value="$d->priority" :label="__('product_prices.priority')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="is_product_additional_free" :value="$d->is_product_additional_free" :list="$yes_no_list"
                            :label="__('product_prices.is_product_additional_free')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.checkbox-inline id="reserve_date" :list="$days" :label="__('product_prices.day')"
                            :value="$booking_day_arr" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.radio-inline id="status" :list="$status_list" :label="__('lang.status')" :value="$d->status" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="start_date" :value="$d->start_date" :label="__('products.start_date')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="end_date" :value="$d->end_date" :label="__('products.end_date')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="car_class_ids[]" :value="$car_class_ids" :list="$car_class_list" :label="__('product_prices.car_class')"
                            :optionals="['multiple' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="origin_ids[]" :value="$origin_ids" :list="$location_list" :label="__('product_prices.origin')"
                            :optionals="['multiple' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="destination_ids[]" :value="$destination_ids" :list="$location_list"
                            :label="__('product_prices.destination')" :optionals="['multiple' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="customer_group_ids[]" :value="$customer_group_ids" :list="$customer_group_list"
                            :label="__('product_prices.customer_group')" :optionals="['multiple' => true]" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="product_id" :value="$product_id" />
                {{-- <x-forms.submit-group :optionals="['url' => "admin.product-prices.index, ['product_id' => $product_id]",'view' => empty($view) ? null : $view]" /> --}}
                <div class="row push">
                    <div class="text-end">
                        @if (!isset($view))
                            <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                        @endif
                        <a class="btn btn-secondary"
                            href="{{ route('admin.product-prices.index', ['product_id' => $product_id]) }}">{{ __('lang.back') }}</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.product-prices.store'),
])

@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#price').prop('disabled', true);
            $('#priority').prop('disabled', true);
            $('#priority').prop('disabled', true);
            $('input[name="is_product_additional_free"]').prop('disabled', true);
            $('input[name="reserve_date[]"]').prop('disabled', true);
            $('input[name="status"]').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('[name="car_class_ids[]"]').prop('disabled', true);
            $('[name="origin_ids[]"]').prop('disabled', true);
            $('[name="destination_ids[]"]').prop('disabled', true);
            $('[name="customer_group_ids[]"]').prop('disabled', true);
        }
    </script>
@endpush
