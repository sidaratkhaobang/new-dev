<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.car_claim_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="report_no" :value="$d->report_no" :label="__('accident_informs.inform_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="claim_no" :value="$d->claim_no" :label="__('accident_informs.claim_no')" />
            </div>
            <div class="col-sm-3" id="claim_type_id_label">
                <x-forms.select-option id="claim_type_id" :value="$d->claim_type" :list="$claim_type_list" :label="__('accident_informs.claim_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="responsible_id">
                <x-forms.select-option id="responsible" :value="$d->responsible" :list="$responsible_list" :label="__('accident_informs.responsible_person')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3" id="is_except_deductible_id">
                <x-forms.select-option id="is_except_deductible" :value="$d->is_except_deductible" :list="$rights_list"
                    :label="__('accident_informs.except_damages')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="reason_except_deductible_id" @if ($d->is_except_deductible == RightsEnum::NOT_USE_RIGHTS || $d->is_except_deductible == null) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="reason_except_deductible" :value="$d->reason_except_deductible" :label="__('accident_informs.right_reason')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3"
                @if ($d->wrong_type == MistakeTypeEnum::FALSE) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="deductible" :value="$d->deductible" :label="__('accident_informs.first_damage_cost')" :optionals="['required' => true, 'input_class' => 'number-format']" />
            </div>
            {{-- @dd($is_withdraw_true) --}}
            <div class="col-sm-3" id="tls_cost_label"
                @if ($is_withdraw_true > 0) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="tls_cost" :value="$tls_cost_total" :label="__('accident_informs.tls_cost')" :optionals="['required' => true, 'input_class' => 'number-format']" />
            </div>
        </div>
    </div>
</div>
