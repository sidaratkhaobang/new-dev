{{-- <h4>{{ __('replacement_cars.info') }}</h4> --}}
<div class="d-md-flex justify-content-md-between align-items-md-center">
    <div>
        <h3 class="h4 mb-1"><i class="fa fa-file-lines me-1"></i>{{ __('replacement_cars.info') }}</h3>
    </div>
    <div>
        @if (in_array($d->replacement_type, [
            ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, 
            ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE,
            ReplacementTypeEnum::SEND_MAIN,
            ReplacementTypeEnum::SEND_REPLACE,
            ] ))
            <a target="_blank" href="{{ route('admin.replacement-cars.print-pdf', ['replacement_job_id' => $d->id, 'worksheet_type' => 'SEND']) }}" class="btn btn-primary me-3">
                {{ __('replacement_cars.worksheet_send_type') }}
            </a>
        @endif
        @if (in_array($d->replacement_type, [
            ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, 
            ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE,
            ReplacementTypeEnum::RECEIVE_MAIN,
            ReplacementTypeEnum::RECEIVE_REPLACE,
            ] ))
            <a target="_blank" href="{{ route('admin.replacement-cars.print-pdf', ['replacement_job_id' => $d->id, 'worksheet_type' => 'RECEIVE']) }}" class="btn btn-primary">
                {{ __('replacement_cars.worksheet_receive_type') }}
            </a>
        @endif

    </div>
</div>
<br>
<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.input-new-line id="worksheet_no_field" :value="$d->worksheet_no" :label="__('replacement_cars.worksheet_no')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="creator" :value="$d->createdBy ? $d->createdBy->name : get_user_name()" :label="__('replacement_cars.created_by')" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="replacement_type" :list="$replacement_type_list" :value="$d->replacement_type" 
            :label="__('replacement_cars.replacement_type')" :optionals="['required' => true]" />
            @if ($mode == MODE_UPDATE)
                <x-forms.hidden id="replacement_type" :value="$d->replacement_type" :label="__('replacement_cars.replacement_type')" />
            @endif
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="job_type" :list="$replacement_job_type_list" :value="$d->job_type" 
            :label="__('replacement_cars.job_type')" :optionals="['required' => true]" />
        @if ($mode == MODE_UPDATE)
            <x-forms.hidden id="job_type" :value="$d->job_type" :label="__('replacement_cars.job_type')" />
        @endif
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="job_id" :list="null" :value="null" 
        :label="__('replacement_cars.job_id')" :optionals="['required' => true]" />
        @if ($mode == MODE_UPDATE)
            <x-forms.hidden id="job_id" :value="$d->job_type" :label="__('replacement_cars.job_id')" />
        @endif
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="contract_no" :value="null" :label="__('replacement_cars.contract_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="main_license_plate" :value="null" :label="__('replacement_cars.main_license_plate')" />
        {{-- <x-forms.hidden id="main_car_id" id="main_car_id" :value="$d->main_car_id" :label="__('replacement_cars.main_license_plate')" /> --}}
            <x-forms.hidden id="main_car_id_" name="main_car_id_" :value="$d->main_car_id" />
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="replacement_expect_date" :value="$d->replacement_expect_date" :label="__('replacement_cars.expect_date')"
            :optionals="['date_enable_time' => true, 'required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_need_driver" :value="$d->is_need_driver" :list="$is_need_driver_list" :label="__('replacement_cars.is_need_driver')" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_need_slide" :value="$d->is_need_slide" :list="$is_need_slide_list" :label="__('replacement_cars.is_need_slide')" />
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.input-new-line id="replacement_expect_place" :value="$d->replacement_expect_place" 
            :label="__('replacement_cars.place')"  :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('replacement_cars.customer_name')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('replacement_cars.tel')" 
            :optionals="['required' => true]" />
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')" />
    </div>
    <div class="col-sm-3">
        <x-forms.upload-image :id="'documents'" :label="__('replacement_cars.document')" />
    </div>
</div>

