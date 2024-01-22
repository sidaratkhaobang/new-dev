<div class="row push">
    <div class="col-sm-3">
        <x-forms.label :value="($d->drivingJob) ? __('driving_jobs.job_type_' . $d->drivingJob->job_type) : null" id="rental_type" :label="__('car_park_transfers.rental_type')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label :value="($d->drivingJob) ? $d->drivingJob->ref_worksheet_no_attr : null" id="rental_no" :label="__('car_park_transfers.rental_no')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.date-range start-id="start_date" :start-value="$d->start_date" end-id="end_date" :end-value="$d->end_date" :label="__('car_park_transfers.period_date')" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-6">
        <x-forms.input-new-line :value="$d->reason" id="reason" :label="__('car_park_transfers.reason')" :optionals="['maxlength' => '200']" />
    </div>
</div>

<div class="row push" >
    <div class="col-sm-3" >
        <x-forms.radio-inline id="is_difference_branch" :value="$d->is_difference_branch" :list="$is_difference_branch_list" :label="__('car_park_transfers.is_difference_branch')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3 difference-branch-wrap" style="display: none;" >
        <x-forms.select-option id="origin_branch_id" :value="$d->origin_branch_id" :list="$branch_list"
            :label="__('car_park_transfers.origin_branch_id')" />
    </div>
    <div class="col-sm-3 difference-branch-wrap" style="display: none;" >
        <x-forms.select-option id="destination_branch_id" :value="$d->destination_branch_id" :list="$branch_list"
            :label="__('car_park_transfers.destination_branch_id')" />
    </div>
</div>

<div class="row" >
    <div class="col-sm-3" >
        <x-forms.radio-inline id="is_singular" :value="$d->is_singular" :list="$is_singular_list" :label="__('car_park_transfers.is_singular')" :optionals="['required' => true]" />
    </div>
</div>