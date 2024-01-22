<h4 class="mt-4">{{ __('gps.gps_data') }}</h4>
<div class="row push mb-4">
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.vid') }}</p>
        <p class="size-text" id="vid">{{ $d->vid }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.sim') }}</p>
        <p class="size-text" id="sim">{{ $d->sim }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.must_check_date') }}</p>
        <p class="size-text" id="must_check_date">
            {{ $d->must_check_date ? get_thai_date_format($d->must_check_date, 'd/m/Y') : null }}</p>
    </div>
    <div class="col-sm-3">
        @if (strcmp($d->status, GPSStatusEnum::NO_SIGNAL) == 0 || isset($view))
            <p class="grey-text">{{ __('gps.check_date') }}</p>
            <p class="size-text" id="check_date">
                {{ $d->check_date ? get_thai_date_format($d->check_date, 'd/m/Y') : null }}</p>
        @else
            <x-forms.date-input id="check_date" :value="$d->check_date" :label="__('gps.check_date')" :optionals="['placeholder' => __('lang.select_date')]" />
        @endif
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="status" :value="$d->status" :list="$status_approve" :label="__('lang.status')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('gps.remark')" />
    </div>
</div>
@if (strcmp($d->status, GPSStatusEnum::NO_SIGNAL) == 0)
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.radio-inline id="repair_immediately" :value="$d->repair_immediately" :list="$repair_list" :label="__('gps.repair')" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="repair_date" :value="$d->repair_date" :label="__('gps.repair_date')" :optionals="['placeholder' => __('lang.select_date')]" />
        </div>
        <div class="col-sm-6">
            <x-forms.input-new-line id="remark_repair" :value="$d->remark_repair" :label="__('gps.remark_repair')" />
        </div>
    </div>
    <x-forms.hidden id="check_date" :value="$d->check_date" />
@endif
