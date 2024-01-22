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
                               :value="$d?->amount_tax_sign"
                               :label="__('change_registrations.amount_copy_tax_sing')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.label id="lot_name"
                               :value="$d?->is_license_plate"
                               :label="__('change_registrations.amount_copy_license_plate')"/>
            </div>
        </div>
    </div>
</div>
