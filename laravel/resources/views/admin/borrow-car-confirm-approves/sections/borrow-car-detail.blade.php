{{-- Car Detail --}}
<div class="block {{ __('block.styles') }}" id="car_borrow" style="display: none">
    <div class="block-content">
<h4>{{ __('borrow_cars.borrow_car_detail') }}</h4>
<hr>
    <div class="row push mb-4">
        {{-- <div class="col-sm-3">
            <x-forms.select-option id="borrow_branch_id" :value="$d->borrow_branch_id" :list="$branch_list" :label="__('transfer_cars.branch')" :optionals="['required' => true]"/>
        </div> --}}
        <div class="col-sm-3">
            <x-forms.select-option id="car_id" :value="$d->car_id" :list="$car_lists" :label="__('transfer_cars.license_plate_chassis')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_class" :value="$d->car && $d->car->carClass ? $d->car->carClass->full_name : null" :label="__('car_classes.class')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_color" :value="$d->car && $d->car->carColor ? $d->car->carColor->name : null" :label="__('car_classes.color')" :optionals="['required' => true]"/>
        </div>
    </div>

<div class="row push mb-4 select-car-no-driver" style="display: none" > 
    <div class="col-sm-3" id="driver_worksheet_label">
        <div class="col-auto mt-2">
            {{ __('transfer_cars.driver_worksheet') }}
        </div>
        <div class="col-sm-2 mt-3">
            @if (isset($driving_job_send))
            <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $driving_job_send->id]) }}"
                class="mt-1">{{ $driving_job_send->worksheet_no ? $driving_job_send->worksheet_no : '' }}</a>
        @endif
        </div>
    </div>
    <div class="col-sm-3" id="car_transfer_sheet_label">
        <div class="col-auto mt-2">
            {{ __('transfer_cars.car_transfer_sheet') }}
        </div>
        <div class="col-sm-2 mt-3">
            @if (isset($car_park_transfer_send))
            <a href="{{ route('admin.car-park-transfers.show', ['car_park_transfer' => $car_park_transfer_send->id]) }}"
                class="mt-1">{{ $car_park_transfer_send->worksheet_no ? $car_park_transfer_send->worksheet_no : '' }}</a>
        @endif
        </div>
    </div>
    <div class="col-sm-3" id="qa_sheet_pickup_label">
        <div class="col-auto mt-2">
            {{ __('transfer_cars.qa_sheet_pickup') }}
        </div>
        <div class="col-auto mt-3">
            @if (isset($inspection_pickup))
                        <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_pickup->id]) }}"
                            class="mt-1">{{ $inspection_pickup->worksheet_no ?  $inspection_pickup->worksheet_no : '' }}</a><br>
                @endif
        </div>
    </div>
    <div class="col-sm-3" id="qa_sheet_return_label">
        <div class="col-auto mt-2">
            {{ __('transfer_cars.qa_sheet_return') }}
        </div>
        <div class="col-sm-2 mt-3">
            @if (isset($inspection_return))
            <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_return->id]) }}"
                class="mt-1">{{ $inspection_return->worksheet_no ?  $inspection_return->worksheet_no : '' }}</a><br>
    @endif
        </div>
    </div>

</div>
</div>
</div>
