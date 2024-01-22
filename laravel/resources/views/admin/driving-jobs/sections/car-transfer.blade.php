<div class="row">
    <div class="col-sm-3">
        <x-forms.label id="transfer_worksheet_no" :value="$d->carParkTransfer?->worksheet_no" :label="__('driving_jobs.transfer_worksheet_no')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label id="transfer_zone_code" :value="$d->carParkTransfer?->carPark?->zone_code" :label="__('car_park_transfers.zone')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label id="transfer_car_park_number" :value="$d->carParkTransfer?->carPark?->car_park_number" :label="__('car_park_transfers.parking_slot')"/>
    </div>
</div>