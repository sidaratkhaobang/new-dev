<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.insurance_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="insurance_company" :value="null" :label="__('accident_informs.insurance_company')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="policy_no" :value="null" :label="__('accident_informs.policy_no')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="coverage_start_date" :value="null" :label="__('accident_informs.coverage_start_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="coverage_end_date" :value="null" :label="__('accident_informs.coverage_end_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="replacement_type_id"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
            </div>
            <div class="col-sm-3" id="is_driver_replacement_id"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
            </div>
        </div>
    </div>
</div>
