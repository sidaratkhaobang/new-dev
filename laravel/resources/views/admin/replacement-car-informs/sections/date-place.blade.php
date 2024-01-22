<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="replacement_date" :value="$d->replacement_date" :label="__('replacement_cars.replacement_date')" :optionals="['required' => true, 'date_enable_time' => true]" />
    </div>
    @if (isset($allow_update_status))
        @if (in_array($d->status, [ReplacementCarstatusEnum::PENDING, ReplacementCarstatusEnum::IN_PROCESS, ReplacementCarstatusEnum::COMPLETE]))
            <div class="col-sm-3">
                <x-forms.select-option id="status" :list="$update_status_list" :value="$d->status" 
                    :label="__('lang.status')" />
            </div>
            @if (isset($mode) && $mode == MODE_UPDATE)
                {{-- <x-forms.hidden id="replacement_date" :value="$d->replacement_date" /> --}}
                {{-- <x-forms.hidden id="replacement_place" :value="$d->replacement_place" /> --}}
            @endif
        @endif
    @endif
</div>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.input-new-line id="replacement_place" :value="$d->replacement_place" :label="__('replacement_cars.replacement_place')" :optionals="['required' => true]" />
    </div>
</div>
