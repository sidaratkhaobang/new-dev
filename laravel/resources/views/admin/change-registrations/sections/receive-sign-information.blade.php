<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.receive_sign'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="requester_type_recipient" :value="$d?->requester_type_recipient" :list="null"
                                       :optionals="[
                                            'placeholder' => __('lang.search_placeholder'),
                                           ]"
                                       :label="__('change_registrations.requester_type_contact')"/>
            </div>
            <div class="col-sm-3">
                @if( $d?->requester_type_contact == ChangeRegistrationRequestTypeContactEnum::TLS)
                    <x-forms.input-new-line id="name_recipient" :value="$d?->name_recipient"
                                            :label="__('change_registrations.name_contact')"
                                            :optionals="['required' => false]"/>
                @else
                    <x-forms.select-option id="name_recipient" :value="$d?->name_recipient" :list="null"
                                           :optionals="[
                                                            'placeholder' => __('lang.search_placeholder'),
                                                           ]"
                                           :label="__('change_registrations.name_contact')"/>
                @endif
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel_recipient" :value="$d?->tel_recipient"
                                        :label="__('change_registrations.tel_contact')"
                                        :optionals="['required' => false]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="email_recipient" :value="$d?->email_recipient"
                                        :label="__('change_registrations.email_contact')"
                                        :optionals="['required' => false]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <x-forms.text-area-new-line id="address_recipient" :value="$d?->address_recipient"
                                            :label="__('change_registrations.address_contact')"
                                            :optionals="['required' => false]"/>
            </div>
        </div>
    </div>
</div>
