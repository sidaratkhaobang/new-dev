<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="receive_case_date_cancel_use_car" :value="$d->receive_case_date" :label="__('change_registrations.receive_case_date')" :optionals="['required' => true]" />
    </div>
    {{-- <div class="col-sm-3">
        <x-forms.select-option id="detail_change" :list="[]" :value="$d->detail_change" :label="__('change_registrations.type_change')"
            :optionals="['required' => true]" />
    </div> --}}
</div>
<div class="row mb-4">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="remark_cancel_use_car" :value="$d->remark" :label="__('change_registrations.remark')" :optionals="['placeholder' => __('lang.input.placeholder'), 'row' => 2]" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'optional_cancel_use_car_files'" :label="__('change_registrations.optional_files')" :list="$optional_cancel_use_car_files" />
        @else
            <x-forms.upload-image :id="'optional_cancel_use_car_files'" :label="__('change_registrations.optional_files')" />
        @endif
    </div>

</div>

