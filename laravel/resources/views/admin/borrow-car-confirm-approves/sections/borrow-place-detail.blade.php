{{-- Car Detail --}}
<div class="block {{ __('block.styles') }} select-car-driver" style="display: none">
    <div class="block-content">
        <h4>{{ __('borrow_cars.borrow_place_detail') }}</h4>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="pickup_place" :value="$d->pickup_place != null ? $d->pickup_place : $d->place" :label="__('borrow_cars.pickup_place')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="car_transfer_sheet_label">
                <div class="col-auto mt-2">
                    {{ __('borrow_cars.driving_job') }}
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
        </div>

        <div class="row push mb-4 ">
            <div class="col-sm-3">
                @if ($d->status == BorrowCarEnum::IN_PROCESS)
                    <x-forms.input-new-line id="return_place" :value="$d->return_place" :label="__('borrow_cars.return_place')" :optionals="['required' => true]" />
                @else
                    <x-forms.input-new-line id="return_place" :value="$d->return_place" :label="__('borrow_cars.return_place')" />
                @endif
            </div>
            <div class="col-sm-3" id="car_transfer_sheet_label">
                <div class="col-auto mt-2">
                    {{ __('borrow_cars.driving_job') }}
                </div>
                <div class="col-sm-2 mt-3">
                    @if (isset($driving_job_pickup))
                    <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $driving_job_pickup->id]) }}"
                        class="mt-1">{{ $driving_job_pickup->worksheet_no ? $driving_job_pickup->worksheet_no : '' }}</a>
                @endif
                </div>
            </div>
            <div class="col-sm-3" id="car_transfer_sheet_label">
                <div class="col-auto mt-2">
                    {{ __('transfer_cars.car_transfer_sheet') }}
                </div>
                <div class="col-sm-2 mt-3">
                    @if (isset($car_park_transfer_pickup))
                    <a href="{{ route('admin.car-park-transfers.show', ['car_park_transfer' => $car_park_transfer_pickup->id]) }}"
                        class="mt-1">{{ $car_park_transfer_pickup->worksheet_no ? $car_park_transfer_pickup->worksheet_no : '' }}</a>
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
