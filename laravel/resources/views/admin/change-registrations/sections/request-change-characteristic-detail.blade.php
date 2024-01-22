<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.request_change_tital'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="__('change_registrations.request_type_'.$d?->type.'_text')"
                               :label="__('change_registrations.job_type')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->receive_case_date"
                               :label="__('change_registrations.receive_case_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->detail_change"
                               :label="__('change_registrations.change_characteristic_request')"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.view-image :id="'car_bodyfiles'" :label="__('change_registrations.car_picture')"
                                    :list="$media['car_body_files'] ?? []"/>
            </div>
            <div class="col-sm-3">
                <x-forms.view-image :id="'receipt_file'"
                                    :label="__('change_registrations.car_change_characteristic_slip')"
                                    :list="$media['receipt_file'] ?? []"/>
            </div>
        </div>
    </div>
</div>
