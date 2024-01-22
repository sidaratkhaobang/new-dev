<div class="block {{ __('block.styles') }}">
    @section('block_options_1')
        <div class="block-options">
            @if (!isset($view))
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <button type="button" class="btn btn-primary add-appointment" onclick="appointmentModal()" id="openModal"><i class="icon-menu-calendar-2"></i>
                        {{ __('accident_orders.appointment') }}</button>
                @endcan
            </div>
            @endif
        </div>
    @endsection

    @include('admin.components.block-header', [
        'text' => __('accident_orders.appointment_detail'),
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.checkbox-inline id="is_appointment" :value="[$d->is_appointment]" :list="[['name' => __('accident_orders.informed'), 'value' => 1]]" :label="__('accident_orders.appointment')" />
            </div>
            <div class="col-sm-9" id="appointment"
                @if ([$d->is_appointment][0] == STATUS_ACTIVE) style="display: block;" @else  style="display: none;" @endif>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.date-input id="appointment_date" :value="null" :label="__('accident_orders.appointment_date')" :optionals="['date_enable_time' => true]" />
                            <x-forms.hidden id="appointment_date_hidden" :value="null" />
                    </div>
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="appointment_place" :value="null" :label="__('accident_orders.appointment_place')" />
                        <x-forms.hidden id="appointment_place_hidden" :value="null" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
