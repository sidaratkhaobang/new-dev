@if ($promotion->promotion_type === PromotionTypeEnum::PARTNER)
    <div class="row push mb-4">
        <div class="col-sm-3" id="build_at">
            <x-forms.radio-inline id="build_at" :value="$build" :list="$yes_no_list" :label="__('promotions.build')"
                :optionals="['required' => true]" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-6" id="build_yes"
            @if ($d->can_reuse === BOOL_TRUE) style="display: block" @else style="display: none" @endif>
            <x-forms.radio-inline id="can_reuse" :value="$d->can_reuse" :list="$reuse_list" :label="__('promotions.can_reuse')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-6" id="code_img"
            @if ($build === BOOL_FALSE) style="display: block" @else style="display: none" @endif>
            <x-forms.upload-image :id="'code_file'" :label="__('promotions.code_files')" :optionals="['required' => true]" />
            {{-- <input type="file" id="code_file_1" name="code_file_1"> --}}
        </div>
    </div>
@endif

@if ($promotion->promotion_type === PromotionTypeEnum::COUPON)
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.radio-inline id="can_reuse" :value="$d->can_reuse" :list="$reuse_list" :label="__('promotions.can_reuse')"
                :optionals="['required' => true]" />
        </div>
    </div>
@endif

<div class="row" id="can_reuse_yes"
    @if ($d->can_reuse === BOOL_TRUE) style="display: block" @else style="display: none" @endif>
    <div class="col-12">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="code" :value="$d->code" :label="__('promotions.coupon_code')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="quota" :value="null" :label="__('promotions.quota')" :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
        </div>
    </div>
</div>

<div class="row" id="can_reuse_no"
    @if ($d->can_reuse === BOOL_FALSE) style="display: block" @else style="display: none" @endif>
    <div class="col-12">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="pattern_code" :value="$d->pattern_code" :list="$pattern_list" :label="__('promotions.pattern_code')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="prefix_code" :value="$d->prefix_code" :label="__('promotions.prefix_code')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="code_digit" :value="0" :label="__('promotions.code_digit')" :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_code" :value="$d->amount_code" :label="__('promotions.amount_code')" :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
        </div>
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="start_sale_date" name="start_sale_date" :value="$d->start_sale_date" :label="__('promotions.start_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="end_sale_date" name="end_sale_date" :value="$d->end_sale_date" :label="__('promotions.end_date')" />
    </div>
</div>
