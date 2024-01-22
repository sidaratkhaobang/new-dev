@if (count($inspection_job_step_log) > 0)
    <p>{{ __('inspection_cars.result_inspection') }}</p>
    @foreach ($inspection_job_step_log as $index => $job_step_log)
        <div class="row push">
            <div class="col-auto mt-1">
                @if ($job_step_log->inspection_status == InspectionStatusEnum::NOT_PASS)
                    <div class="form-check d-inline-block">
                        <input type="radio" id="inspection_status_pass{{ $index }}"
                            class="form-check-input radio-log" name="inspection_status{{ $index }}"
                            @if ($job_step_log->inspection_status == \App\Enums\InspectionStatusEnum::PASS) checked @endif value="{{ InspectionStatusEnum::PASS }}">

                        <label class="form-check-label">{{ __('inspection_cars.pass') }}</label>&emsp;

                        <div class="form-check d-inline-block">
                            <input type="radio" id="inspection_status_not_pass{{ $index }}"
                                class="form-check-input radio-log" name="inspection_status{{ $index }}"
                                @if ($job_step_log->inspection_status == \App\Enums\InspectionStatusEnum::NOT_PASS) checked @endif
                                value="{{ InspectionStatusEnum::NOT_PASS }}">

                            <label class="form-check-label">{{ __('inspection_cars.fail') }}</label>
                        </div>
                    </div>
            </div>
            <div class="col-sm-3">
                <input class="form-control remark_log" type="type" id="remark_log" name="remark_log"
                    value="{{ $job_step_log->remark }}" placeholder="หมายเหตุ" />
            </div>
            <div class="col-sm-2">
                <input class="form-control remark_reason_log" type="type" id="remark_log" name="remark_reason_log"
                    value="{{ __('inspection_cars.remark_reason_' . $job_step_log->remark_reason) }}" />
            </div>
        @else
            <div class="col-auto mb-3">
                <div class="form-check d-inline-block">
                    <input type="radio" id="inspection_status_pass{{ $index }}"
                        class="form-check-input radio-log" name="inspection_status{{ $index }}"
                        @if ($job_step_log->inspection_status == \App\Enums\InspectionStatusEnum::PASS) checked @endif value="PASS">

                    <label class="form-check-label">{{ __('inspection_cars.pass') }}</label>&emsp;

                    <div class="form-check d-inline-block">
                        <input type="radio" id="inspection_status_not_pass{{ $index }}"
                            class="form-check-input radio-log" name="inspection_status{{ $index }}"
                            @if ($job_step_log->inspection_status == \App\Enums\InspectionStatusEnum::NOT_PASS) checked @endif value="NOT_PASS">

                        <label class="form-check-label">{{ __('inspection_cars.fail') }}</label>
                    </div>
                </div>
            </div>
    @endif
    </div>
@endforeach
@endif
@if (count($inspection_job_step_log) > 0)
    <p class="mt-3">{{ __('inspection_cars.result_inspection_repeat') }}</p>
@else
    <p>{{ __('inspection_cars.result_inspection') }}</p>
@endif
<div class="row push mb-4">
    <div class="col-auto mt-1">
        <div class="form-check d-inline-block">
            <input type="radio" id="inspection_status_pass" class="form-check-input" name="inspection_status"
                value="{{ InspectionStatusEnum::PASS }}">
            <label class="form-check-label">{{ __('inspection_cars.pass') }}</label>&emsp;

            <div class="form-check d-inline-block">
                <input type="radio" id="inspection_status_not_pass" class="form-check-input" name="inspection_status"
                    value="{{ InspectionStatusEnum::NOT_PASS }}">
                <label class="form-check-label">{{ __('inspection_cars.fail') }}</label>
            </div>
        </div>
    </div>
    @if (!isset($view))
        <div class="col-sm-2" id="reason">
            <select name="remark_reason" id="remark_reason" class="js-select2-default" style="width: 100%;">
                <option value="">
                    {{ !empty($select_option) ? $select_option : __('lang.select_option') }}
                </option>
                @foreach ($remark_reason as $key => $item)
                    <option value="{{ $item->id }}">
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>
@if (!isset($view))
    <div class="row push mb-4">
        <div class="col-sm-6">
            {{-- <input class="form-control" type="" id="remark" name="remark" placeholder="สาเหตุ*" /> --}}
            <textarea class="form-control" id="remark" name="remark" placeholder="สาเหตุ*"></textarea>
        </div>
    </div>
@endif
