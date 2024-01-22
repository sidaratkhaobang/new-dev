<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.dlt_processing_information'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="processing_date" :value="$d?->processing_date"
                                    :label="__('change_registrations.processing_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="completion_date" :value="$d?->completion_date"
                                    :label="__('change_registrations.completion_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="completion_duration_date" :value="$d?->completion_duration_date"
                                        :label="__('change_registrations.completion_duration_date')"
                                        :optionals="['required' => false]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="return_registration_book_date" :value="$d?->return_registration_book_date"
                                    :label="__('change_registrations.return_registration_book_date')"/>
            </div>
            @if($d?->type != ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY && $d?->type != ChangeRegistrationTypeEnum::CANCEL_USE_CAR)
                <div class="col-sm-9">
                    <x-forms.input-new-line id="link" :value="$d?->link"
                                            :label="__('change_registrations.link_registration')"
                                            :optionals="['required' => false]"/>
                </div>
            @endif
            @if($d?->type == ChangeRegistrationTypeEnum::CANCEL_USE_CAR)
                <div class="col-sm-3">
                    <x-forms.date-input id="completion_registration_date" :value="null"
                                        :label="__('change_registrations.completion_registration_date')"/>
                </div>
                <div class="col-sm-6">
                    <x-forms.checkbox-inline id="recive_licen_list" name="recive_licen_list" :list="$recive_licen_list"
                                             :label="__('change_registrations.recive_licen')" :value="[]"/>
                </div>
            @endif

        </div>
    </div>
</div>
