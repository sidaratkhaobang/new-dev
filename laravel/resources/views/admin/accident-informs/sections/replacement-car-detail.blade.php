<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.replacement_car_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_replacement" :value="$d->is_replacement" :list="$need_list" :label="__('accident_informs.need_replacement')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="replacement_expect_date_id"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="replacement_expect_date" name="replacement_expect_date" :value="$d->replacement_expect_date"
                        :label="__('accident_informs.replacement_date')" :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="replacement_expect_date" name="replacement_expect_date"
                        :value="get_thai_date_format($d->replacement_expect_date, 'd/m/Y')" :label="__('accident_informs.replacement_date')" />
                @endif
            </div>
            <div class="col-sm-3" id="replacement_type_id"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.select-option id="replacement_type" :value="$d->replacement_type" :list="$replace_list" :label="__('accident_informs.replacement_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="is_driver_replacement_id"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.radio-inline id="is_driver_replacement" :value="$d->is_driver_replacement" :list="$need_list"
                    :label="__('accident_informs.need_driver_replacement')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" id="replacement_expect_place_id"
            @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
            <div class="col-sm-12">
                <x-forms.input-new-line id="replacement_expect_place" :value="$d->replacement_expect_place" :label="__('accident_informs.replacement_place')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" id="replacement_car_files_id"
            @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
            <div class="col-sm-3">
                @if (isset($view))
                    <x-forms.view-image :id="'replacement_car_files'" :label="__('accident_informs.replacement_car_files')" :list="$replacement_car_files" />
                @else
                    <x-forms.upload-image :id="'replacement_car_files'" :label="__('accident_informs.replacement_car_files')" />
                @endif
            </div>
        </div>
    </div>
</div>
