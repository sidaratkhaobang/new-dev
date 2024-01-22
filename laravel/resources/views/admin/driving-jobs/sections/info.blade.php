<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="job_type" :value="__('driving_jobs.job_type_' . $d->job_type)" :label="__('driving_jobs.worksheet_type')" />
    </div>
    <div class="col-sm-3" id="self_drive_show">
        <x-forms.select-option id="self_drive_type" :value="$d->self_drive_type" :list="$self_drive_types" :label="__('driving_jobs.job_type')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="start_date" :value="$d->start_date" :label="__('driving_jobs.start_date')" :optionals="['placeholder' => __('lang.select_date'), 'date_enable_time' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="end_date" :value="$d->end_date" :label="__('driving_jobs.end_date')" :optionals="['placeholder' => __('lang.select_date'), 'date_enable_time' => true]" />
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('driving_jobs.remark')" />
    </div>

    @if(in_array($d->self_drive_type, ['PICKUP', 'OTHER']))
    <div class="col-sm-6">
        <x-forms.input-new-line id="origin" :value="$d->origin" :label="__('driving_jobs.origin')" />
    </div>
    @endif

    @if(in_array($d->self_drive_type, ['SEND', 'OTHER']))
    <div class="col-sm-6">
        <x-forms.input-new-line id="destination" :value="$d->destination" :label="__('driving_jobs.destination')" />
    </div>
    @endif
</div>
