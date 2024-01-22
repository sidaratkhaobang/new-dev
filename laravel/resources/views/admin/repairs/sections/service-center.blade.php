<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repairs.service_center_table') }}</h4>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="in_center" :value="$d->in_center ? $d->in_center : null" :list="$service_center_list" :label="__('repairs.in_center')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="in_center_date" :value="$d->in_center_date ? $d->in_center_date : null" :label="__('repairs.in_center_date')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3" id="driver_in_center"
                @if (strcmp($d->in_center, STATUS_DEFAULT) == 0) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.radio-inline id="is_driver_in_center" :value="$d->is_driver_in_center ? $d->is_driver_in_center : null" :list="$is_need_driver" :label="__('repairs.is_driver_in_center')" />
            </div>
            @if (isset($driving_job_in))
                <div class="col-3">
                    <span>{{ __('transfer_cars.driver_worksheet') }}</span><br>
                    <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $driving_job_in->id]) }}"
                        class="mt-1" target="_blank">{{ $driving_job_in ? $driving_job_in->worksheet_no : '' }}</a>
                </div>
            @endif
            @if (isset($inspection_job_in))
                <div class="col-3">
                    <span>{{ __('transfer_cars.qa_sheet_return') }}</span><br>
                    <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_job_in->id]) }}"
                        class="mt-1" target="_blank">{{ $inspection_job_in ? $inspection_job_in->worksheet_no : '' }}</a>
                </div>
            @endif
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="out_center" :value="$d->out_center ? $d->out_center : null" :list="$service_center_list" :label="__('repairs.out_center')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="out_center_date" :value="$d->out_center_date ? $d->out_center_date : null" :label="__('repairs.out_center_date')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3" id="driver_out_center"
                @if (strcmp($d->out_center, STATUS_DEFAULT) == 0) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.radio-inline id="is_driver_out_center" :value="$d->is_driver_out_center ? $d->is_driver_out_center : null" :list="$is_need_driver"
                    :label="__('repairs.is_driver_out_center')" />
            </div>
            @if (isset($driving_job_out))
                <div class="col-3">
                    <span>{{ __('transfer_cars.driver_worksheet') }}</span><br>
                    <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $driving_job_out->id]) }}"
                        class="mt-1" target="_blank">{{ $driving_job_out ? $driving_job_out->worksheet_no : '' }}</a>
                </div>
            @endif
            @if (isset($inspection_job_out))
                <div class="col-3">
                    <span>{{ __('transfer_cars.qa_sheet_pickup') }}</span><br>
                    <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_job_out->id]) }}"
                        class="mt-1" target="_blank">{{ $inspection_job_out ? $inspection_job_out->worksheet_no : '' }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
