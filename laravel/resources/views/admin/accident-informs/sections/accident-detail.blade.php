<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.accident_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="accident_date" name="accident_date" :value="$d->accident_date" :label="__('accident_informs.accident_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="accident_date" name="accident_date" :value="get_thai_date_format($d->accident_date, 'd/m/Y')" :label="__('accident_informs.accident_date')" />
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
                <x-forms.select-option id="wrong_type" :value="$d->wrong_type" :list="$mistake_list" :label="__('accident_informs.wrong_type')"/>
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
                {{-- <x-forms.select-option id="cradle" :value="$d->cradle" :list="$garage_list" :label="__('accident_informs.cradle_recommend')"
                    :optionals="['required' => true]" /> --}}
                <div class="row">
                    <div class="col-sm-9">
                        <label for="cradle">{{ __('accident_informs.cradle_recommend') }} <span
                                class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-3 ">
                        <button id="garage-all" class="btn btn-primary btn-sm-rounded mb-1"
                            style="padding: 0.2rem 0.5rem; font-size: 0.8rem; min-width: unset;">
                            <i class="fa-solid fa-search fa-sm me-1"></i>ดูอู่ทั้งหมด
                        </button>
                    </div>
                </div>
                <select name="cradle" id="cradle" class="js-select2-default col-8" style="width: 100%;">
                    {{-- <option value=""> --}}
                    {{-- {{ !empty($select_option) ? $select_option : __('lang.select_option') }} --}}
                    {{-- </option> --}}
                    {{-- @foreach ($garage_list as $key => $item)
                        <option value="{{ $item->id }}">
                            {{ $item->name }}
                        </option>
                    @endforeach --}}
                </select>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-12">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('accident_informs.remark')" />
            </div>
        </div>

        <div class="row push mb-4">
            <div class="col-sm-3">
                @if (isset($view))
                    <x-forms.view-image :id="'optional_files'" :label="__('transfer_cars.optional_file')" :list="$optional_files" />
                @else
                    <x-forms.upload-image :id="'optional_files'" :label="__('transfer_cars.optional_file')" />
                @endif
            </div>
        </div>

    </div>
</div>
