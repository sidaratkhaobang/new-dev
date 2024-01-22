<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.document_registration_information'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="request_registration_book_date" :value="$d?->request_registration_book_date"
                                    :label="__('change_registrations.request_registration_book_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_registration_book_date" :value="$d?->receive_registration_book_date"
                                    :label="__('change_registrations.receive_registration_book_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="wait_registration_book_duration_day"
                                        :value="$d?->wait_registration_book_duration_day"
                                        :label="__('change_registrations.wait_registration_book_duration_day')"
                                        :optionals="['required' => false]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_power_attorney_tls" :value="$d?->is_power_attorney_tls"
                                      :list="getYesNoList()"
                                      :label="__('change_registrations.is_power_attorney_tls')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="request_power_attorney_tls_date" :value="$d?->request_power_attorney_tls_date"
                                    :label="__('change_registrations.request_power_attorney_tls_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_power_attorney_tls_date" :value="$d?->receive_power_attorney_tls_date"
                                    :label="__('change_registrations.receive_power_attorney_tls_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="wait_power_attorney_tls_duration_day"
                                        :value="$d?->wait_power_attorney_tls_duration_day"
                                        :label="__('change_registrations.wait_power_attorney_tls_duration_day')"
                                        :optionals="['required' => false]"/>
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="is_power_attorney" :value="$d?->is_power_attorney"
                                      :list="getYesNoList()"
                                      :label="__('change_registrations.is_power_attorney')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="request_power_attorney_date" :value="$d?->request_power_attorney_date"
                                    :label="__('change_registrations.request_power_attorney_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_power_attorney_date" :value="$d?->receive_power_attorney_date"
                                    :label="__('change_registrations.receive_power_attorney_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="wait_power_attorney_duration_day"
                                        :value="$d?->wait_power_attorney_duration_day"
                                        :label="__('change_registrations.wait_power_attorney_duration_day')"
                                        :optionals="['required' => false]"/>
            </div>

        </div>
    </div>
</div>
