<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repairs.replacement_table') }}</h4>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_replacement" :value="$d->is_replacement ? $d->is_replacement : null" :list="$is_need_driver" :label="__('repairs.is_replacement')" />
            </div>
            <div class="col-sm-3" id="re_date"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.date-input id="replacement_date" :value="$d->replacement_date ? $d->replacement_date : null" :label="__('repairs.replacement_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="re_type"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.select-option id="replacement_type" :list="$replacement_type_list" :value="$d->replacement_type ? $d->replacement_type : null" :label="__('repairs.replacement_type')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-9" id="re_place"
                @if ($d->is_replacement) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="replacement_place" :value="$d->replacement_place ? $d->replacement_place : null" :label="__('repairs.replacement_place')"
                    :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
