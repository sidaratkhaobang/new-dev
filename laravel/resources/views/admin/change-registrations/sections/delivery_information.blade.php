<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.delivery_information'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="delivery_channel" :value="$d?->delivery_channel" :list="null"
                                       :optionals="[
                                            'placeholder' => __('lang.search_placeholder'),
                                           ]"
                                       :label="__('change_registrations.delivery_channel')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="ems" :value="$d?->ems"
                                        :label="__('change_registrations.ems')"
                                        :optionals="['required' => false]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="delivery_date" :value="$d?->delivery_date"
                                    :label="__('change_registrations.delivery_date')"/>
            </div>
        </div>
    </div>
</div>
