<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="receive_case_date_characteristic" :value="$d->receive_case_date" :label="__('change_registrations.receive_case_date')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="detail_change_characteristic" :list="$characteristic_transport_lists" :value="$d->detail_change" :label="__('change_registrations.characteristic_change')"
            :optionals="['required' => true]" />
    </div>
</div>

<div class="row mb-4">
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'car_body_characteristic_files'" :label="__('change_registrations.car_body_files')" :list="$car_body_characteristic_files" />
        @else
            <x-forms.upload-image :id="'car_body_characteristic_files'" :label="__('change_registrations.car_body_files')" />
        @endif
    </div>
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'receipt_change_characteristic_files'" :label="__('change_registrations.receipt_change_characteristic_files')" :list="$receipt_change_characteristic_files" />
        @else
            <x-forms.upload-image :id="'receipt_change_characteristic_files'" :label="__('change_registrations.receipt_change_characteristic_files')" />
        @endif
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="remark_characteristic" :value="$d->remark" :label="__('change_registrations.remark')" :optionals="['placeholder' => __('lang.input.placeholder'), 'row' => 2]" />
    </div>
</div>
