<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.accident_detail'),
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
                <x-forms.select-option id="claim_by" :value="$d->claim_by" :list="$claimant_list" :label="__('accident_informs.claim_by')" />
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
                <x-forms.input-new-line id="report_no" :value="$d->report_no" :label="__('accident_informs.report_no')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="accident_date" name="accident_date" :value="$d->accident_date" :label="__('accident_informs.accident_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="accident_date" name="accident_date" :value="get_thai_date_format($d->accident_date, 'd/m/Y')"
                        :label="__('accident_informs.accident_date')" />
                @endif
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="driver" :value="$d->driver" :label="__('accident_informs.driver')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="main_area" :value="$d->main_area" :list="$province_list" :label="__('accident_informs.main_area')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="case" :value="$d->case" :list="$case_list" :label="__('accident_informs.case')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="accident_description" :value="$d->accident_description" :label="__('accident_informs.accident_description')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="accident_place" :value="$d->accident_place" :label="__('accident_informs.accident_place')" :optionals="['required' => true]" />

            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="current_place" :value="$d->current_place" :label="__('accident_informs.current_place')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="region" :value="$d->region" :list="$region" :label="__('garages.sector')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="province" :value="$d->province_id" :list="null" :label="__('garages.province')"
                    :optionals="['ajax' => true, 'default_option_label' => $province_name, 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="district" :value="$d->district_id" :list="null" :label="__('garages.amphure')"
                    :optionals="['ajax' => true, 'default_option_label' => $amphure_name]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="subdistrict" :value="$d->subdistrict_id" :list="null" :label="__('garages.district')"
                    :optionals="['ajax' => true, 'default_option_label' => $district_name]" />
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_parties" :value="$d->is_parties" :list="$status_list" :label="__('accident_informs.partie')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="wrong_type_id"
                @if ($d->wrong_type) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.select-option id="wrong_type" :value="$d->wrong_type" :list="$mistake_list" :label="__('accident_informs.wrong_type')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_wounded" :value="$d->is_wounded" :list="$status_list" :label="__('accident_informs.wounded')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3 " id="amount_wounded_driver_id"
                @if ($d->is_wounded) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_wounded_driver" :value="$d->amount_wounded_driver" :label="__('accident_informs.amount_wounded_driver')"
                    :optionals="['required' => true, 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3 amount_wounded_parties_id" id="amount_wounded_parties_id"
                @if ($d->is_wounded) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_wounded_parties" :value="$d->amount_wounded_parties" :label="__('accident_informs.amount_wounded_parties')"
                    :optionals="['required' => true, 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3 " id="amount_wounded_total_id"
                @if ($d->is_wounded) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_wounded_total" :value="$d->amount_wounded_total" :label="__('accident_informs.amount_wounded_total')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_deceased" :value="$d->is_deceased" :list="$status_list" :label="__('accident_informs.deceased')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="amount_deceased_driver_id"
                @if ($d->is_deceased) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_deceased_driver" :value="$d->amount_deceased_driver" :label="__('accident_informs.amount_deceased_driver')"
                    :optionals="['required' => true, 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3" id="amount_deceased_parties_id"
                @if ($d->is_deceased) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_deceased_parties" :value="$d->amount_deceased_parties" :label="__('accident_informs.amount_deceased_parties')"
                    :optionals="['required' => true, 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3" id="amount_deceased_total_id"
                @if ($d->is_deceased) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="amount_deceased_total" :value="$d->amount_deceased_total" :label="__('accident_informs.amount_deceased_total')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_repair" :value="$d->is_repair" :list="$status_list" :label="__('accident_informs.repair')"
                    :optionals="['required' => true]" />
            </div>

            <div class="col-sm-3" id="cradle_id"
                @if ($d->is_repair) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.select-option id="cradle" :value="$d->cradle" :list="$garage_list" :label="__('accident_informs.cradle_recommend')"
                    :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
