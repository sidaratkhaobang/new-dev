{{-- car --}}
<x-blocks.block :title="__('registers.car_detail')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="car_id" :value="$d->car_id" :list="null" :label="__('m_flows.license_plate_chassis_engine')"
                :optionals="[
                    'ajax' => true,
                    'default_option_label' => $car_license,
                ]" />
        </div>
    </div>
</x-blocks.block>
{{-- rental --}}
<x-blocks.block :title="__('debt_collections.table_rental')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="rental_no" :value="$d->rental_no" :label="__('m_flows.rental_no')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="rental_name" :value="$d->rental_name" :label="__('m_flows.rental_name')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="business_line" :value="null" :label="__('m_flows.business_line')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="car_type" :value="$d->car_type" :label="__('m_flows.car_type')" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="contract_no" :value="$d->contract_no" :label="__('m_flows.contract_no')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="contract_start_date" :value="$d->contract_start_date" :label="__('m_flows.contract_start_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="contract_end_date" :value="$d->contract_end_date" :label="__('m_flows.contract_end_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="customer_group" :value="$d->customer_group" :label="__('m_flows.customer_group')" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-12">
            <x-forms.label id="customer_address" :value="$d->customer_address" :label="__('m_flows.customer_address')" />
        </div>
    </div>
</x-blocks.block>
{{-- driver --}}
<x-blocks.block :title="__('m_flows.driver_data')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="full_name" :value="$d->full_name" :label="__('m_flows.full_name')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="agency" :value="null" :label="__('m_flows.agency')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="driver_tel" :value="$d->driver_tel" :label="__('m_flows.driver_tel')" />
        </div>
    </div>
</x-blocks.block>
{{-- offense --}}
<x-blocks.block :title="__('m_flows.offense_data')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="overdue_date" :value="$d->overdue_date" :label="__('m_flows.overdue_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.time-input id="offense_time" :value="$d->offense_time" :label="__('m_flows.offense_time')" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="document_date" :value="$d->document_date" :label="__('m_flows.document_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="expressway_id" :value="$d->expressway_id" :list="$express_way_list" :label="__('m_flows.station_place')" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.input-new-line id="fee" :value="$d->fee" :label="__('m_flows.fee')" :optionals="[
                'input_class' => 'number-format',
            ]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="fine" :value="$d->fine" :label="__('m_flows.fine')" :optionals="[
                'input_class' => 'number-format',
            ]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="maximum_fine" :value="$d->maximum_fine" :label="__('m_flows.maximum_fine')" :optionals="['input_class' => 'number-format']" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.radio-inline id="is_payment" :value="$d->is_payment" :list="$payment_list" :label="__('m_flows.is_payment')" />
        </div>
        <div class="col-sm-3">
            @if (in_array($d->status, [
                    MFlowStatusEnum::PENDING,
                    MFlowStatusEnum::IN_PROCESS,
                    MFlowStatusEnum::COMPLETE,
                    MFlowStatusEnum::CLOSE,
                ]))
                <x-forms.view-image :id="'overdue_file'" :label="__('m_flows.overdue_file')" :list="$overdue_file" />
            @else
                <x-forms.upload-image :id="'overdue_file'" :label="__('m_flows.overdue_file')" />
            @endif
        </div>
    </div>
</x-blocks.block>
