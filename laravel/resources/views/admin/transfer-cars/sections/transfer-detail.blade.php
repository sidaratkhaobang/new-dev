{{-- <div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push my-auto">
            <div class="col-auto">
                <div class="row push ">
                    <div class="col-sm-3">
                        <img src="{{ asset('images/user/user.png') }}" alt="Profile Image"
                            style=" width:70px; height:70px;">
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row push ">

                    <div class="col-auto">
                        <span>{{ __('transfer_cars.creator') }}</span>
                    </div>
                    <div class="col-sm-2">
                        <b>{{ $d->createdBy ? $d->createdBy->name : get_user_name() }}</b>
                    </div>

                    <div class="col-auto">
                        <span>{{ __('transfer_cars.role') }}</span>
                    </div>
                    <div class="col-sm-2">
                        <b>{{ $d->createdBy && $d->createdBy->role ? $d->createdBy->role->name : get_role_name() }}</b>
                    </div>
                </div>
                <div class="row push ">
                    <div class="col-auto">
                        <span>{{ __('transfer_cars.created_date') }}</span>
                    </div>
                    <div class="col-sm-2">
                        <b>{{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y H:i') : get_thai_date_format(null, 'd/m/Y H:i') }}</b>
                    </div>
                    <div class="col-auto">
                        <span>{{ __('transfer_cars.branch') }}</span>
                    </div>
                    <div class="col-sm-2">
                        <b>{{ $d->branch && $d->branch->name ? $d->branch->name : get_branch_name() }}</b>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div> --}}
@include('admin.components.creator')
{{-- Detail --}}
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center">
            <div>
                <h4>{{ __('transfer_cars.transfer_detail') }}</h4>
            </div>
            <div>
                @if (in_array($d->status, [TransferCarEnum::CONFIRM_RECEIVE, TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
                    <a target="_blank" href="{{ route('admin.transfer-cars.print-pdf', ['transfer_car_id' => $d->id]) }}"
                        class="btn btn-primary">
                        {{ __('transfer_cars.worksheet_print') }}
                    </a>
                @endif
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="car_id" :value="$d->car_id" :list="$car_lists" :label="__('transfer_cars.license_plate_chassis')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_class" :value="$d->car && $d->car->carClass ? $d->car->carClass->full_name : null" :label="__('car_classes.class')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_color" :value="$d->car && $d->car->carColor ? $d->car->carColor->name : null" :label="__('car_classes.color')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="transfer_branch_id" :value="$d->transfer_branch_id" :list="$branch_list" :label="__('transfer_cars.branch_receive')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('purchase_requisitions.remark')" />
            </div>
            <div class="col-sm-3">
                @if (isset($view))
                    <x-forms.view-image :id="'optional_files'" :label="__('transfer_cars.optional_file')" :list="$optional_files" />
                @else
                    @if (Route::is('*.edit') &&
                            in_array($d->status, [
                                TransferCarEnum::WAITING_RECEIVE,
                                TransferCarEnum::CONFIRM_RECEIVE,
                                TransferCarEnum::IN_PROCESS,
                                TransferCarEnum::REJECT_RECEIVE,
                            ]))
                        <x-forms.view-image :id="'optional_files'" :label="__('transfer_cars.optional_file')" :list="$optional_files" />
                    @else
                        <x-forms.upload-image :id="'optional_files'" :label="__('transfer_cars.optional_file')" />
                    @endif
                @endif
            </div>
        </div>

        @if (in_array($d->status, [TransferCarEnum::REJECT_RECEIVE]))
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('transfer_cars.reason')" />
                </div>
            </div>
        @endif

        @if (in_array($d->status, [TransferCarEnum::CONFIRM_RECEIVE, TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="contact" :value="$d->contact" :label="__('transfer_cars.contact')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('transfer_cars.tel')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.radio-inline id="is_driver" :value="$d->is_driver" :list="$need_driver_list" :label="__('transfer_cars.is_need_driver')"
                        :optionals="['required' => true]" />
                </div>


            </div>
            <div class="row push mb-4">
                <div class="col-sm-3" id="place_label">
                    <x-forms.input-new-line id="place" :value="$d->place" :label="__('transfer_cars.place')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3" id="delivery_date_label">
                    @if (Route::is('*.edit') || Route::is('*.create'))
                        <x-forms.date-input id="delivery_date" name="delivery_date" :value="$d->delivery_date"
                            :label="__('transfer_cars.delivery_date')" :optionals="['required' => true]" />
                    @else
                        <x-forms.input-new-line id="delivery_date" name="delivery_date" :value="get_thai_date_format($d->delivery_date, 'd/m/Y')"
                            :label="__('transfer_cars.delivery_date')" />
                    @endif
                </div>
                <div class="col-sm-3" id="driver_worksheet_label">
                    <div class="col-auto mt-2">
                        {{ __('transfer_cars.driver_worksheet') }}
                    </div>
                    <div class="col-sm-2 mt-3">
                        @if ($driving_job)
                            <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $driving_job->id]) }}"
                                class="mt-1">{{ $driving_job->worksheet_no ? $driving_job->worksheet_no : '' }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-sm-3" id="car_transfer_sheet_label">
                    <div class="col-auto mt-2">
                        {{ __('transfer_cars.car_transfer_sheet') }}
                    </div>
                    <div class="col-sm-2 mt-3">
                        @if ($car_park_transfer)
                            <a href="{{ route('admin.car-park-transfers.show', ['car_park_transfer' => $car_park_transfer->id]) }}"
                                class="mt-1">{{ $car_park_transfer->worksheet_no ? $car_park_transfer->worksheet_no : '' }}</a>
                        @endif
                    </div>
                </div>

            </div>
            <div class="row push mb-4">
                <div class="col-sm-3" id="qa_sheet_pickup_label">
                    <div class="col-auto mt-2">
                        {{ __('transfer_cars.qa_sheet_pickup') }}
                    </div>
                    <div class="col-auto mt-3">
                        @if ($inspection_pickup)
                            <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_pickup->id]) }}"
                                class="mt-1">{{ $inspection_pickup->worksheet_no ? $inspection_pickup->worksheet_no : '' }}</a><br>
                        @endif
                    </div>
                </div>
                <div class="col-sm-3" id="qa_sheet_return_label">
                    <div class="col-auto mt-2">
                        {{ __('transfer_cars.qa_sheet_return') }}
                    </div>
                    <div class="col-sm-2 mt-3">
                        @if ($inspection_return)
                            <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_return->id]) }}"
                                class="mt-1">{{ $inspection_return->worksheet_no ? $inspection_return->worksheet_no : '' }}</a><br>
                        @endif
                    </div>
                </div>
            </div>

        @endif
        @if (!in_array($d->status, [TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
            @include('admin.transfer-cars.submit')
        @endif
    </div>
</div>
