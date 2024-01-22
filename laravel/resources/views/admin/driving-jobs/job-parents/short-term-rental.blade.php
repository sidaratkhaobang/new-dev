<div class="row push">
    <div class="col-sm-3">
        <x-forms.label id="short_worksheet_no" :value="$d->ref_worksheet_no_attr" :label="__('driving_jobs.worksheet_no')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label id="service_type" :value="$d->job?->serviceType?->name" :label="__('driving_jobs.service_type')"/>
    </div>
    <div class="col-sm-6">
        <x-forms.label id="short_customer" :value="$d->job?->customer_name" :label="__('driving_jobs.customer')"/>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <x-forms.label id="rental_start_date" :value="$d->ref_start_date_attr" :label="__('driving_jobs.rental_start_date')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label id="rental_end_date" :value="$d->ref_end_date_attr" :label="__('driving_jobs.rental_end_date')"/>
    </div>
    @if(in_array($d->self_drive_type, ['PICKUP', 'OTHER']))
    <div class="col-sm-3">
        <x-forms.label id="rental_origin" :value="$d->job?->origin_name" :label="__('driving_jobs.rental_origin')"/>
    </div>
    @endif

    @if(in_array($d->self_drive_type, ['SEND', 'OTHER']))
    <div class="col-sm-3">
        <x-forms.label id="rental_destination" :value="$d->job?->destination_name" :label="__('driving_jobs.rental_destination')"/>
    </div>
    @endif
</div>