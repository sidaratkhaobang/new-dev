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
                               :value="__('change_registrations.is_receipt_roof'.$d?->is_car_alternate_tls)"
                               :label="__('change_registrations.is_tls_car')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->car_owner_type"
                               :label="__('change_registrations.car_owner')"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->car_swap"
                               :label="__('change_registrations.request_licen_plate_for_swap')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_class"
                               :value="$d?->car_class"
                               :label="__('change_registrations.car_model')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->engine_no"
                               :label="__('change_registrations.engine_no')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->chassis_no"
                               :label="__('change_registrations.chassis_no')"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.view-image :id="'registeration_book'" :label="__('change_registrations.registeration_book')"
                                    :list="$media['register_files'] ?? []"/>
            </div>
            <div class="col-sm-3">
                <x-forms.view-image :id="'power_attorney'" :label="__('change_registrations.power_attorney')"
                                    :list="$media['power_attorney_files'] ?? []"/>
            </div>
            <div class="col-sm-3">
                <x-forms.view-image :id="'letter_consent'" :label="__('change_registrations.letter_consent')"
                                    :list="$media['letter_consent_files'] ?? []"/>
            </div>
            <div class="col-sm-3">
                <x-forms.view-image :id="'citizen'" :label="__('change_registrations.citizen')"
                                    :list="$media['citizen_files'] ?? []"/>
            </div>
        </div>
    </div>
</div>
