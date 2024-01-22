<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.car_accident_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="accident_type" :value="$d->accident_type" :list="$accident_type_list" :label="__('accident_informs.accident_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="claim_type" :value="$d->claim_type" :list="$claim_type_list" :label="__('accident_informs.claim_type')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="claim_by" :value="$d->claim_by" :list="$claimant_list" :label="__('accident_informs.claim_by')"/>
            </div>
            <div class="col-sm-3">
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="report_date" name="report_date" :value="$d->report_date" :label="__('accident_informs.report_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="report_date" name="report_date" :value="get_thai_date_format($d->report_date, 'd/m/Y')" :label="__('accident_informs.report_date')" />
                @endif
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="reporter" :value="$d->reporter" :label="__('accident_informs.reporter')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="report_tel" :value="$d->report_tel" :label="__('accident_informs.tel')" :optionals="['required' => true]" />

            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="report_no" :value="$d->report_no" :label="__('accident_informs.report_no')"/>
            </div>
        </div>
    </div>
</div>
