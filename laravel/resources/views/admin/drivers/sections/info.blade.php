<h4>{{ __('drivers.driver_info_table') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('drivers.name')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="code" :value="$d->code" :label="__('drivers.code')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="branch" :value="$d->branch_id" :list="$branch_list" :label="__('drivers.branch')" :optionals="['required' => true]" />
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.select-option id="emp_status" :value="$d->emp_status" :list="$emp_status_list" :label="__('drivers.emp_status')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="position_id" :value="$d->position_id" :list="$position_list" :label="__('drivers.position')" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="province_id" :value="$d->province_id" :list="null" :label="__('drivers.province')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $province_name,
            ]" />
    </div>
    
</div>

<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.input-new-line id="citizen_id" :value="$d->citizen_id" :label="__('drivers.citizen_id')" :optionals="['maxlength' => 20, 'required' => true]" />
    </div>
    {{-- <div class="col-sm-4">
        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('drivers.tel')" />
    </div> --}}
    <div class="col-sm-4">
        <x-forms.input-new-line id="phone" :value="$d->phone" :label="__('drivers.phone')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'profile_image'" :label="__('drivers.profile_image')" :list="$profile_image" />
        @else
            <x-forms.upload-image :id="'profile_image'" :label="__('drivers.profile_image')" />
        @endif
    </div>
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'citizen_file'" :label="__('drivers.citizen_file')" :list="$citizen_files" />
        @else
            <x-forms.upload-image :id="'citizen_file'" :label="__('drivers.citizen_file')" />
        @endif
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.checkbox-inline id="working_day_arr" :list="$days" :label="__('drivers.working_day')" :value="$working_day_arr" :optionals="['required' => true]"/>
    </div>
    <div class="col-sm-4">
        <x-forms.time-input id="start_working_time" :value="$d->start_working_time" :label="__('drivers.start_working_time')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.time-input id="end_working_time" :value="$d->end_working_time" :label="__('drivers.end_working_time')"  :optionals="['required' => true]"/>
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.radio-inline id="status" :value="$d->status" :list="$status_list" :label="__('lang.status')" />
    </div>
</div>
