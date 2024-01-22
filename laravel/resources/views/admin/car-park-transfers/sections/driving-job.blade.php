<div class="row push">
    <div class="col-sm-3">
        <x-forms.label :value="$d->drivingJob?->worksheet_no" id="driving_job_worksheet_no" :label="__('car_park_transfers.driving_job_no')" />
    </div>
    <div class="col-sm-3">
        @if($d->drivingJob && $d->drivingJob->driver)
        <x-forms.label :value="$d->drivingJob->driver->name" id="driver_name" :label="__('car_park_transfers.driver_name')" />
        @elseif($d->drivingJob)
        <x-forms.label :value="$d->drivingJob->driver_name" id="driver_name" :label="__('car_park_transfers.driver_name')" />
        @endif
    </div>
</div>


@if ($d->status == STATUS_INACTIVE)
<div class="row">
    {{-- Cancel --}}
    
        <div class="col-sm-9">
            <x-forms.input-new-line id="cancel_reason" :value="$d->cancel_reason" :label="__('car_park_transfers.cancel_reason')" />
        </div>
    
    {{-- end Cancel --}}
</div>
@endif