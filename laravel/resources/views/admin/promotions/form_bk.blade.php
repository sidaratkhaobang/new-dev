@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('promotions.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="code" :value="$d->code" :label="__('promotions.code')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="status" :value="$d->status" :list="$status_list" :label="__('promotions.status')" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list" :label="__('promotions.branch')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="priority" :value="$d->priority" :label="__('promotions.priority')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="start_date" name="start_date" :value="$d->start_date" :label="__('promotions.start_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="end_date" name="end_date" :value="$d->end_date" :label="__('promotions.end_date')" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="start_sale_date" name="start_sale_date" :value="$d->start_sale_date"
                            :label="__('promotions.start_sale_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="end_sale_date" name="end_sale_date" :value="$d->end_sale_date" :label="__('promotions.end_sale_date')" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="discount_type" :value="$d->discount_type" :list="$discount_type" :label="__('promotions.discount_type')" />
                    </div>
                    <div class="col-sm-6" id="discount_amount_show"
                        @if (in_array($d->discount_type, [DiscountTypeEnum::PERCENT, DiscountTypeEnum::AMOUNT, DiscountTypeEnum::FIXED_PRICE])) style="display: block" @else style="display: none" @endif>
                        <x-forms.input-new-line id="discount_amount" :value="$d->discount_amount" :label="__('promotions.discount_amount')"
                            :optionals="['type' => 'number']" />
                    </div>
                    {{-- <div class="col-sm-6" id="free_product_show"
                        @if ($d->discount_type === DiscountTypeEnum::FREE_PRODUCT) style="display: block" @else style="display: none" @endif>
                        <x-forms.select-option id="free_product[]" :value="$free_product" :list="$product_list" :label="__('promotions.free_product')"
                            :optionals="['multiple' => true]" />
                    </div> --}}
                    <div class="col-sm-6" id="free_car_class_show"
                        @if ($d->discount_type === DiscountTypeEnum::FREE_CAR_CLASS) style="display: block" @else style="display: none" @endif>
                        <x-forms.select-option id="free_car_class[]" :value="$free_car_class" :list="$car_class_list"
                            :label="__('promotions.free_car_class')" :optionals="['multiple' => true]" />
                    </div>
                    <div class="col-sm-6" id="free_product_additional_show"
                        @if ($d->discount_type === DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) style="display: block" @else style="display: none" @endif>
                        <x-forms.select-option id="free_product_additional[]" :value="$free_product_additional" :list="$product_additional_list"
                            :label="__('promotions.free_product_additional')" :optionals="['multiple' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="discount_mode" :value="$d->discount_mode" :list="$discount_mode" :label="__('promotions.discount_mode')" />
                    </div>
                    <div class="col-sm-6" id="promotion_effective_show"
                        @if ($d->discount_mode === DiscountModeEnum::TRANSACTION) style="display: block" @else style="display: none" @endif>
                        <x-forms.select-option id="promotion_effective[]" :value="$promotion_effective" :list="$product_list"
                            :label="__('promotions.promotion_effective')" :optionals="['multiple' => true]" />
                    </div>
                </div>

                <br>
                <h4>{{ __('promotions.condition_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="is_check_min_total" :value="$d->is_check_min_total" :list="$check_list"
                            :label="__('promotions.min_total')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="min_total" :value="$d->min_total" :label="null" :optionals="['type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="is_check_min_hours" :value="$d->is_check_min_hours" :list="$check_list"
                            :label="__('promotions.min_hours')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="min_hours" :value="$d->min_hours" :label="null" :optionals="['type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="is_check_min_days" :value="$d->is_check_min_days" :list="$check_list"
                            :label="__('promotions.min_day')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="min_days" :value="$d->min_days" :label="null" :optionals="['type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="is_check_min_distance" :value="$d->is_check_min_distance" :list="$check_list"
                            :label="__('promotions.min_distance')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="min_distance" :value="$d->min_distance" :label="null"
                            :optionals="['type' => 'number']" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="car_class[]" :value="$car_class" :list="$car_class_list" :label="__('promotions.car_class')"
                            :optionals="['multiple' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="customer_group[]" :value="$customer_group" :list="$customer_group_list"
                            :label="__('promotions.customer_group')" :optionals="['multiple' => true]" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="product[]" :value="$product" :list="$product_list" :label="__('promotions.product')"
                            :optionals="['multiple' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="sale[]" :value="$sale" :list="$sale_list" :label="__('promotions.sale')"
                            :optionals="['multiple' => true]" />
                    </div>
                </div>

                <br>
                <h4>{{ __('promotions.incompatible_section') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="incompatible[]" :value="$incompatible" :list="$incompatible_list" :label="__('promotions.incompatible_promotions')"
                            :optionals="['multiple' => true]" />
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="text-end">
                        @if (isset($view))
                            <a class="btn btn-primary"
                                href="{{ route('admin.promotion-codes.index', ['promotion_id' => $d->id]) }}">{{ __('promotions.view_promotion_code') }}</a>
                        @else
                            @can(Actions::Manage . '_' . Resources::Promotion)
                                <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                                <button type="button"
                                    class="btn btn-primary btn-save-promotion-code">{{ __('promotions.save_code') }}</button>
                            @endcan
                        @endif
                        <a class="btn btn-secondary" href="{{ route('admin.promotions.index') }}">{{ __('lang.back') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.promotions.store'),
])
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $create = '{{ isset($create) }}';
        $view = '{{ isset($view) }}';
        $min_total = '{{ $d->min_total }}';
        $min_hours = '{{ $d->min_hours }}';
        $min_days = '{{ $d->min_days }}';
        $min_distance = '{{ $d->min_distance }}';
        $is_check_min_total = '{{ $d->is_check_min_total }}';
        $is_check_min_hours = '{{ $d->is_check_min_hours }}';
        $is_check_min_days = '{{ $d->is_check_min_days }}';
        $is_check_min_distance = '{{ $d->is_check_min_distance }}';

        if ($is_check_min_total === '{{ BOOL_FALSE }}') {
            $('#min_total').prop('disabled', true);
        }

        if ($is_check_min_hours === '{{ BOOL_FALSE }}') {
            $('#min_hours').prop('disabled', true);
        }

        if ($is_check_min_days === '{{ BOOL_FALSE }}') {
            $('#min_days').prop('disabled', true);
        }

        if ($is_check_min_distance === '{{ BOOL_FALSE }}') {
            $('#min_distance').prop('disabled', true);
        }

        if ($create) {
            $('#min_total').val('');
            $('#min_hours').val('');
            $('#min_days').val('');
            $('#min_distance').val('');
            $('#min_total').prop('disabled', true);
            $('#min_hours').prop('disabled', true);
            $('#min_days').prop('disabled', true);
            $('#min_distance').prop('disabled', true);
        }

        if ($view) {
            $('#min_total').prop('disabled', true);
            $('#min_hours').prop('disabled', true);
            $('#min_days').prop('disabled', true);
            $('#min_distance').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#sku').prop('disabled', true);
            $('#branch_id').prop('disabled', true);
            $('#priority').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#discount_amount').prop('disabled', true);
            $('input[name="status"]').prop('disabled', true);
            $('input[name="discount_type"]').prop('disabled', true);
            $('input[name="discount_mode"]').prop('disabled', true);
            $('input[name="is_check_min_total"]').prop('disabled', true);
            $('input[name="is_check_min_hours"]').prop('disabled', true);
            $('input[name="is_check_min_days"]').prop('disabled', true);
            $('input[name="is_check_min_distance"]').prop('disabled', true);
            // $('[name="free_product[]"]').prop('disabled', true);
            $('[name="free_car_class[]"]').prop('disabled', true);
            $('[name="free_product_additional[]"]').prop('disabled', true);
            $('[name="promotion_effective[]"]').prop('disabled', true);
            $('[name="car_class[]"]').prop('disabled', true);
            $('[name="customer_group[]"]').prop('disabled', true);
            $('[name="product[]"]').prop('disabled', true);
            $('[name="sale[]"]').prop('disabled', true);
            $('[name="incompatible[]"]').prop('disabled', true);
        }

        $('input[name="discount_type"]').on("click", function() {
            var discount_type = $('input[name="discount_type"]:checked').val();
            if (discount_type === '{{ DiscountTypeEnum::PERCENT }}' || discount_type ===
                '{{ DiscountTypeEnum::AMOUNT }}' || discount_type === '{{ DiscountTypeEnum::FIXED_PRICE }}') {
                document.getElementById("discount_amount_show").style.display = "block"
                document.getElementById("free_car_class_show").style.display = "none"
                document.getElementById("free_product_additional_show").style.display = "none"
                $('[name="free_car_class[]"]').val([]).trigger('change');
                $('[name="free_product_additional[]"]').val([]).trigger('change');
                // } else if (discount_type === '{{ DiscountTypeEnum::FREE_PRODUCT }}') {
                //     document.getElementById("discount_amount_show").style.display = "none"
                //     document.getElementById("free_product_show").style.display = "block"
                //     document.getElementById("free_product_additional_show").style.display = "none"
                //     $('[name="free_product_additional[]"]').val([]).trigger('change');
            } else if (discount_type === '{{ DiscountTypeEnum::FREE_CAR_CLASS }}') {
                document.getElementById("discount_amount_show").style.display = "none"
                document.getElementById("free_car_class_show").style.display = "block"
                document.getElementById("free_product_additional_show").style.display = "none"
                $('#discount_amount').val('');
                $('[name="free_product_additional[]"]').val([]).trigger('change');
            } else if (discount_type === '{{ DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT }}') {
                document.getElementById("discount_amount_show").style.display = "none"
                document.getElementById("free_car_class_show").style.display = "none"
                document.getElementById("free_product_additional_show").style.display = "block"
                $('#discount_amount').val('');
                $('[name="free_car_class[]"]').val([]).trigger('change');
            }
        });

        $('input[name="discount_mode"]').on("click", function() {
            var discount_mode = $('input[name="discount_mode"]:checked').val();
            if (discount_mode === '{{ STATUS_INACTIVE }}') {
                document.getElementById("promotion_effective_show").style.display = "block"
            } else {
                document.getElementById("promotion_effective_show").style.display = "none"
                $('[name="promotion_effective[]"]').val([]).trigger('change');
            }
        });

        $('input[name="is_check_min_total"]').on("click", function() {
            if ($('input[name="is_check_min_total"]:checked').val() === '{{ BOOL_TRUE }}') {
                $('#min_total').prop('disabled', false);
                $('#min_total').val($min_total);
            } else {
                $('#min_total').prop('disabled', true);
                $('#min_total').val('');
            }
        });

        $('input[name="is_check_min_hours"]').on("click", function() {
            if ($('input[name="is_check_min_hours"]:checked').val() === '{{ BOOL_TRUE }}') {
                $('#min_hours').prop('disabled', false);
                $('#min_hours').val($min_hours);
            } else {
                $('#min_hours').prop('disabled', true);
                $('#min_hours').val('');
            }
        });

        $('input[name="is_check_min_days"]').on("click", function() {
            if ($('input[name="is_check_min_days"]:checked').val() === '{{ BOOL_TRUE }}') {
                $('#min_days').prop('disabled', false);
                $('#min_days').val($min_days);
            } else {
                $('#min_days').prop('disabled', true);
                $('#min_days').val('');
            }
        });

        $('input[name="is_check_min_distance"]').on("click", function() {
            if ($('input[name="is_check_min_distance"]:checked').val() === '{{ BOOL_TRUE }}') {
                $('#min_distance').prop('disabled', false);
                $('#min_distance').val($min_distance);
            } else {
                $('#min_distance').prop('disabled', true);
                $('#min_distance').val('');
            }
        });

        $(".btn-save-promotion-code").on("click", function() {
            let storeUri = "{{ route('admin.promotions.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('set_promotion_code', true);
            saveForm(storeUri, formData);
        });

    </script>
@endpush
