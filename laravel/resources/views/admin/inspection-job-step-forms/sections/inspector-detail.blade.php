{{-- <div class="ms-auto">
    <a target="_blank"
        href="{{ route('admin.inspection-job-step-forms.pdf', ['inspection_job_step_form' => $d, 'type' => 'PDF']) }}"
        class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
        {{ __('inspection_cars.print') }}
    </a>
</div> --}}

<hr>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="inspector_department" :value="$step_form_check_condition->name" :label="__('inspection_cars.department')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="inspector" :value="$step_form_status->inspector_id ? $step_form_status->inspector_id : null" :list="$users_select" :label="__('inspection_cars.fullname_inspector')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="inspector_fullname" :value="$step_form_status->inspector_fullname" :label="__('inspection_cars.fullname_inspector_other')" />
    </div>
    @if ($car_park)
        <div class="col-sm-3">
            <x-forms.input-new-line id="inspection_location" :value="$car_park ? $car_park->code . $car_park->car_park_number : ''" :label="__('inspection_cars.inspection_place')" />
        </div>
    @else
        <div class="col-sm-3">
            <x-forms.input-new-line id="inspection_location_text" :value="$step_form_status->inspection_location ? $step_form_status->inspection_location : ''" :label="__('inspection_cars.inspection_place')" />
        </div>
    @endif
</div>
<div class="row">
    <div class="col-sm-3">
        <x-forms.date-input id="inspection_date" :value="$step_form_status->inspection_date ? $step_form_status->inspection_date : null" :label="__('inspection_cars.inspection_date')" :optionals="['required' => true]" />
    </div>
</div>
