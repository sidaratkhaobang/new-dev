<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.contact_detail'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="requester_type_contact" :value="$d?->requester_type_contact"
                                       :list="$type_contact_list"
                                       :optionals="[
                                            'placeholder' => __('lang.search_placeholder'),
                                           ]"
                                       :label="__('change_registrations.requester_type_contact')"/>
            </div>
            <div class="col-sm-3">
                {{--                @if( $d?->requester_type_contact == ChangeRegistrationRequestTypeContactEnum::TLS)--}}
                <x-forms.input-new-line id="name_contact" :value="$d?->name_contact"
                                        :label="__('change_registrations.name_contact')"
                                        :optionals="['required' => false]"/>
                {{--                @else--}}
                {{--                    <x-forms.select-option id="name_contact" :value="$d?->name_contact" :list="null"--}}
                {{--                                           :optionals="[--}}
                {{--                                                            'placeholder' => __('lang.search_placeholder'),--}}
                {{--                                                           ]"--}}
                {{--                                           :label="__('change_registrations.name_contact')"/>--}}
                {{--                @endif--}}
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel_contact" :value="$d?->tel_contact"
                                        :label="__('change_registrations.tel_contact')"
                                        :optionals="['required' => false]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="email_contact" :value="$d?->email_contact"
                                        :label="__('change_registrations.email_contact')"
                                        :optionals="['required' => false]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <x-forms.text-area-new-line id="address_contact" :value="$d?->address_contact"
                                            :label="__('change_registrations.address_contact')"
                                            :optionals="['required' => false]"/>
            </div>
        </div>
    </div>
</div>
