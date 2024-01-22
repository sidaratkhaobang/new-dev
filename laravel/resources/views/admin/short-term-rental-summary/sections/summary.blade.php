<div class="form-group row mt-4">
    <div class="col-sm-7"></div>
    <div class="col-sm-5">
        <div class="row">
            <div class="col-sm-6">{{ __('short_term_rentals.summary_subtotal') }} <span class="font-size-xs" >{{ __('short_term_rentals.excl_vat') }}</span></div>
            <div class="col-sm-6 text-end">@{{ summary.subtotal_text }}</div>
        </div>
        <div class="row">
            <div class="col-sm-6">ส่วนลด</div>
        </div>
        <div class="row">
            <div class="col-sm-6">Promotion</div>
            <div class="col-sm-6 text-end text-danger">
                <span v-if="summary.discount_text == 0">@{{ summary.discount_text }}</span>
                <span v-else>-@{{ summary.discount_text }}</span>
            </div>
        </div>
        <div class="row" v-for="promotion_name in summary.promotion_name_list" >
            <div class="col-sm-6">
                <span class="font-size-sm ps-3 fst-italic" >- @{{ promotion_name }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">Voucher</div>
            <div class="col-sm-6 text-end text-danger">
                <span v-if="summary.coupon_discount_text == 0">@{{ summary.coupon_discount_text }}</span>
                <span v-else>-@{{ summary.coupon_discount_text }}</span>
            </div>
        </div>
        <div class="row" v-for="coupon_name in summary.coupon_name_list" >
            <div class="col-sm-6">
                <span class="font-size-sm ps-3 fst-italic" >- @{{ coupon_name }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">ยอดหลังหักส่วนลด</div>
            <div class="col-sm-6 text-end">@{{ summary.subtotal_with_discount_text }}</div>
        </div>
        <div class="row">
            <div class="col-sm-6">VAT</div>
            <div class="col-sm-6 text-end">@{{ summary.vat_text }}</div>
        </div>
        <div class="row">
            <div class="col-sm-6">{{ __('short_term_rentals.summary_total') }} <span class="font-size-xs" >{{ __('short_term_rentals.incl_vat') }}</span></div>
            <div class="col-sm-6 text-end">@{{ summary.subtotal_with_vat_text }}</div>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-check form-check-inline mt-1">
                    <input type="checkbox" class="form-check-input" name="active_tax" id="active_tax" value="1" {{ ((boolval($d->is_withholding_tax)) ? 'checked' : '') }} >
                    <span for="active_tax" >หักภาษี ณ ที่จ่าย</span>
                </div>
            </div>
            <div class="col-sm-6 text-end">
                <div id="active_sub" {!! ((boolval($d->is_withholding_tax)) ? '' : 'style="display: none;"') !!} >
                    <x-forms.radio-inline id="withholding_tax_value" :list="$withholding_tax_list"
                        :label="null" :value="intval($d->withholding_tax_value)" 
                        :optionals="['label_class' => '', 'input_class' => 'checkbox-tax']" 
                    />
                </div>
            </div>
        </div>
        
        <div class="row push">
            <div class="col-sm-6">ภาษี ณ ที่จ่าย</div>
            <div class="col-sm-6 text-end"><span
                    v-if="summary.withholding_tax_text == 0">@{{ summary.withholding_tax_text }}</span>
                <span v-else>-@{{ summary.withholding_tax_text }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 fs-lg">จำนวนเงินรวมทั้งสิ้น</div>
            <div class="col-sm-6 fs-lg text-end">@{{ summary.total_text }}</div>
        </div>
    </div>

</div>