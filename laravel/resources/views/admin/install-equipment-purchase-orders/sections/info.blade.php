<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  'ข้อมูลใบขอซื้อ',
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="time_of_delivery" :value="$d->time_of_delivery" :label="__('install_equipment_pos.time_of_delivery')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="payment_term" :value="$d->payment_term" :label="__('install_equipment_pos.payment_term')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="contact" :value="$d->contact" :label="__('install_equipment_pos.contact')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_user" :value="$d->car_user" :label="__('install_equipment_pos.car_user')" />
            </div>
        </div>

        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="quotation_remark" :value="$d->quotation_remark" :label="__('install_equipment_pos.quotation')" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')" />
            </div>
        </div>
    </div>
</div>