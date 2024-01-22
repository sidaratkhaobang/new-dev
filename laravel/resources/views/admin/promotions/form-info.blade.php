@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <form id="save-form">
        <x-blocks.block>
            <div class="row">
                <div class="col-sm-6">
                    <x-forms.input-new-line id="name" :value="$d->name" :label="$label_name" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3" id="code_show"
                    @if (in_array($promotion_type, [PromotionTypeEnum::PROMOTION])) style="display: block" @else style="display: none" @endif>
                    <x-forms.input-new-line id="code" :value="$d->code" :label="$label_code" />
                </div>
                <div class="col-sm-3">
                    <x-forms.radio-inline id="status" :value="$d->status" :list="$status_list" :label="__('promotions.status')" />
                </div>
            </div>
        </x-blocks.block>
        @if (!in_array($promotion_type, [PromotionTypeEnum::VOUCHER]))
            <x-blocks.block :title="'Promotion - Coupon'">
                <div class="row">
                    @if (in_array($promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PROMOTION, PromotionTypeEnum::PARTNER]))
                        <div class="col-sm-6">
                            <x-forms.radio-inline id="coupon" :value="$coupon_id" :list="$coupon_list" :label="__('promotions.coupon')" />
                        </div>
                    @endif

                    <div class="col-sm-6" id="coupon_type"
                        @if (in_array($promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) style="display: block" @else style="display: none" @endif>
                        <x-forms.radio-inline id="coupon_type" :value="$promotion_type" :list="$coupon_types" :label="__('promotions.coupon_type')" />
                    </div>

                    <div class="col-sm-3" id="quota"
                        @if (in_array($promotion_type, [PromotionTypeEnum::PROMOTION])) style="display: block" @else style="display: none" @endif>
                        <x-forms.input-new-line id="quota" :value="$quota" :label="__('promotions.quota')" :optionals="['required' => true, 'input_class' => 'number-format']" />
                    </div>
                </div>
            </x-blocks.block>
        @endif
        <x-blocks.block :title="'Voucher'">
            <div class="row push">
                @if (in_array($promotion_type, [PromotionTypeEnum::VOUCHER]))
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="voucher_type" :value="$voucher_type_id" :list="$voucher_type" :label="__('promotions.voucher_type')" />
                    </div>
                @endif

                <div class="col-sm-6" id="package_amount_show"
                    @if (in_array($promotion_type, [PromotionTypeEnum::VOUCHER]) && $d->package_amount > 1) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.input-new-line id="package_amount" :value="$d->package_amount" :label="__('promotions.package_amount')" :optionals="['input_class' => 'number-format', 'min' => 2]" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list" :label="__('promotions.branch')"
                        :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="priority" :value="$d->priority" :label="__('promotions.priority')" :optionals="['required' => true, 'input_class' => 'number-format']" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="start_date" name="start_date" :value="$d->start_date" :label="__('promotions.start_date')"
                        :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="end_date" name="end_date" :value="$d->end_date" :label="__('promotions.end_date')"
                        :optionals="['required' => true]" />
                </div>

            </div>
            <div class="row push">
                @if (in_array($promotion_type, [PromotionTypeEnum::VOUCHER]))
                    <div class="col-sm-3">
                        <x-forms.date-input id="start_sale_date" name="start_sale_date" :value="$d->start_sale_date"
                            :label="__('promotions.start_sale_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="end_sale_date" name="end_sale_date" :value="$d->end_sale_date" :label="__('promotions.end_sale_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="branch_expired_id" :value="$d->branch_expired_id" :list="$branch_list"
                            :label="__('promotions.branch_expired')" :optionals="['required' => true]" />
                    </div>
                @endif
            </div>
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="discount_type" :value="$d->discount_type" :list="$discount_type" :label="__('promotions.discount_type')" />
                </div>
                <div class="col-sm-6" id="discount_amount_show"
                    @if (in_array($d->discount_type, [DiscountTypeEnum::PERCENT, DiscountTypeEnum::AMOUNT, DiscountTypeEnum::FIXED_PRICE])) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.input-new-line id="discount_amount" :value="$d->discount_amount" :label="__('promotions.discount_amount')" :optionals="['input_class' => 'number-format']" />
                </div>
                <div class="col-sm-6" id="free_product_show"
                    @if ($d->discount_type === DiscountTypeEnum::FREE_PRODUCT) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.select-option id="free_product[]" :value="$free_product" :list="$product_list" :label="__('promotions.free_product')"
                        :optionals="['multiple' => true]" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-6" id="free_car_class_show"
                    @if ($d->discount_type === DiscountTypeEnum::FREE_CAR_CLASS) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.select-option id="free_car_class[]" :value="$free_car_class" :list="$car_class_list" :label="__('promotions.free_car_class')"
                        :optionals="['multiple' => true]" />
                </div>
                <div class="col-sm-6" id="free_product_additional_show"
                    @if ($d->discount_type === DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.select-option id="free_product_additional[]" :value="$free_product_additional" :list="$product_additional_list"
                        :label="__('promotions.free_product_additional')" :optionals="['multiple' => true]" />
                </div>
                <div class="col-sm-6" id="discount_day_show"
                    @if (in_array($d->discount_type, [DiscountTypeEnum::FREE_CAR_CLASS])) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.input-new-line id="discount_day" :value="$d->discount_day" :label="__('promotions.discount_day')" :optionals="[
                        'input_class' => 'number-format col-sm-4',
                        'label_suffix' => __('promotions.discount_day_helper'),
                    ]" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="discount_mode" :value="$d->discount_mode" :list="$discount_mode" :label="__('promotions.discount_mode')" />
                </div>
                <div class="col-sm-6" id="promotion_effective_show"
                    @if ($d->discount_mode === DiscountModeEnum::TRANSACTION) style="display: block"
                @else style="display: none" @endif>
                    <x-forms.select-option id="promotion_effective[]" :value="$promotion_effective" :list="$car_class_list"
                        :label="__('promotions.car_class')" :optionals="['multiple' => true]" />
                </div>
            </div>
        </x-blocks.block>
        <x-blocks.block :title="__('promotions.condition_table')">
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="is_check_min_total" :value="$d->is_check_min_total" :list="$check_list"
                        :label="__('promotions.min_total')" />
                </div>
                <div class="col-sm-6">
                    <x-forms.input-new-line id="min_total" :value="$d->min_total" :label="null" :optionals="['input_class' => 'number-format']" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="is_check_min_hours" :value="$d->is_check_min_hours" :list="$check_list"
                        :label="__('promotions.min_hours')" />
                </div>
                <div class="col-sm-6">
                    <x-forms.input-new-line id="min_hours" :value="$d->min_hours" :label="null" :optionals="['input_class' => 'number-format']" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="is_check_min_days" :value="$d->is_check_min_days" :list="$check_list" :label="__('promotions.min_day')" />
                </div>
                <div class="col-sm-6">
                    <x-forms.input-new-line id="min_days" :value="$d->min_days" :label="null" :optionals="['input_class' => 'number-format']" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.radio-inline id="is_check_min_distance" :value="$d->is_check_min_distance" :list="$check_list"
                        :label="__('promotions.min_distance')" />
                </div>
                <div class="col-sm-6">
                    <x-forms.input-new-line id="min_distance" :value="$d->min_distance" :label="null" :optionals="['input_class' => 'number-format']" />
                </div>
            </div>

            <div class="row push">
                <div class="col-sm-6">
                    <x-forms.select-option id="car_class[]" :value="$car_class" :list="$car_class_list" :label="__('promotions.car_class')"
                        :optionals="['multiple' => true]" />
                </div>
                <div class="col-sm-6">
                    <x-forms.select-option id="customer_group[]" :value="$customer_group" :list="$customer_group_list" :label="__('promotions.customer_group')"
                        :optionals="['multiple' => true]" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <x-forms.select-option id="product[]" :value="$product" :list="$product_list" :label="__('promotions.product')"
                        :optionals="['multiple' => true]" />
                </div>
                <div class="col-sm-6">
                    <x-forms.select-option id="sale[]" :value="$sale" :list="$sale_list" :label="__('promotions.sale')"
                        :optionals="['multiple' => true]" />
                </div>
            </div>
        </x-blocks.block>
        <x-blocks.block :title="__('promotions.incompatible_section')">
            <div class="row">
                <div class="col-sm-6">
                    <x-forms.select-option id="incompatible[]" :value="$incompatible" :list="$incompatible_list" :label="__('promotions.incompatible_promotions')"
                        :optionals="['multiple' => true]" />
                </div>
            </div>

            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.hidden id="promotion_type" :value="$promotion_type" />
        </x-blocks.block>
        <x-blocks.block>
            <x-forms.submit-group :optionals="[
                'url' => 'admin.promotions.index',
                'view' => empty($view) ? null : $view,
                'manage_permission' => Actions::Manage . '_' . Resources::Promotion,
            ]">
                @if (empty($view))
                    <x-slot name="pos_2">
                        <button type="button" class="btn btn-primary btn-save-promotion-code me-2" id="btn_coupon">
                            {{ $label_btn }}
                        </button>
                    </x-slot>
                @endif
            </x-forms.submit-group>
        </x-blocks.block>
    </form>

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
        $code = '{{ $d->code }}';
        $is_check_min_total = '{{ $d->is_check_min_total }}';
        $is_check_min_hours = '{{ $d->is_check_min_hours }}';
        $is_check_min_days = '{{ $d->is_check_min_days }}';
        $is_check_min_distance = '{{ $d->is_check_min_distance }}';
        $coupon_id = '{{ $coupon_id }}';

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

        if ($coupon_id === '{{ PromotionTypeEnum::PROMOTION }}') {
            $('#btn_coupon').prop('disabled', true);
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
            $('#code').prop('disabled', true);
            $('#branch_id').prop('disabled', true);
            $('#priority').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#start_sale_date').prop('disabled', true);
            $('#end_sale_date').prop('disabled', true);
            $('#discount_amount').prop('disabled', true);
            $('#discount_day').prop('disabled', true);
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
            $('input[name="coupon"]').prop('disabled', true);
            $('input[name="coupon_type"]').prop('disabled', true);
            $('input[name="quota"]').prop('disabled', true);
            $('input[name="voucher_type"]').prop('disabled', true);
            $('#package_amount').prop('disabled', true);
            $('#branch_expired_id').prop('disabled', true);
        }

        $('input[name="discount_type"]').on("click", function() {
            var discount_type = $('input[name="discount_type"]:checked').val();
            if (discount_type === '{{ DiscountTypeEnum::PERCENT }}' || discount_type ===
                '{{ DiscountTypeEnum::AMOUNT }}' || discount_type === '{{ DiscountTypeEnum::FIXED_PRICE }}') {
                document.getElementById("discount_amount_show").style.display = "block"
                document.getElementById("free_car_class_show").style.display = "none"
                document.getElementById("discount_day_show").style.display = "none"
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
                document.getElementById("discount_day_show").style.display = "block"
                document.getElementById("free_product_additional_show").style.display = "none"
                $('#discount_amount').val('');
                $('[name="free_product_additional[]"]').val([]).trigger('change');
            } else if (discount_type === '{{ DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT }}') {
                document.getElementById("discount_amount_show").style.display = "none"
                document.getElementById("free_car_class_show").style.display = "none"
                document.getElementById("discount_day_show").style.display = "none"
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

        $('input[name="coupon"]').on("click", function() {
            var coupon = $('input[name="coupon"]:checked').val();
            if (coupon === '{{ PromotionTypeEnum::COUPON }}') {
                $('#btn_coupon').prop('disabled', false);
                document.getElementById("coupon_type").style.display = "block";
                document.getElementById("quota").style.display = "none";
                document.getElementById("code_show").style.display = "none";
                document.getElementById("promotion_type").value = $('input[name="coupon"]:checked').val();
                $('#code').val('');
            } else {
                $('#btn_coupon').prop('disabled', true);
                document.getElementById("coupon_type").style.display = "none";
                document.getElementById("quota").style.display = "block";
                document.getElementById("code_show").style.display = "block";
                document.getElementById("promotion_type").value = $('input[name="coupon"]:checked').val();
                $('#code').val($code);
            }
        });

        $('input[name="voucher_type"]').on("click", function() {
            var voucher_type = $('input[name="voucher_type"]:checked').val();
            if (voucher_type === '{{ BOOL_TRUE }}') {
                document.getElementById("package_amount_show").style.display = "block"
            } else {
                document.getElementById("package_amount_show").style.display = "none"
            }
        });

        $('input[name="coupon_type"]').on("click", function() {
            document.getElementById("promotion_type").value = $('input[name="coupon_type"]:checked').val();
        });

        $(".btn-save-promotion-code").on("click", function() {
            let storeUri = "{{ route('admin.promotions.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('set_promotion_code', true);
            saveForm(storeUri, formData);
        });

        $(document).ready(function() {
            $("#priority").keypress(function(event) {
                if (event.which != 8 && event.which != 0 && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush
