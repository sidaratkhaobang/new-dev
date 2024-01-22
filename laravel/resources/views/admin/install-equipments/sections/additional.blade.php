@if (in_array($d->status, [
    InstallEquipmentStatusEnum::CONFIRM, 
    InstallEquipmentStatusEnum::INSTALL_IN_PROCESS, 
    InstallEquipmentStatusEnum::OVERDUE,
    InstallEquipmentStatusEnum::DUE,
    InstallEquipmentStatusEnum::INSTALL_COMPLETE,
    InstallEquipmentStatusEnum::INSPECT_IN_PROCESS,
    InstallEquipmentStatusEnum::COMPLETE,
]))
<hr>
<div class="form-group row mb-4">
    <div class="col-sm-3">
        <x-forms.date-input id="start_date" :value="$d->start_date" :label="__('install_equipments.start_date')" :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="install_day_amount" :value="$d->install_day_amount" :label="__('install_equipments.install_day_amount')"
            :optionals="['type' => 'number']" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="expected_end_date" :value="$d->expected_end_date" :label="__('install_equipments.expected_end_date')" :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    @if (in_array($d->status, [
        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS, 
        InstallEquipmentStatusEnum::OVERDUE,
        InstallEquipmentStatusEnum::DUE,
        InstallEquipmentStatusEnum::INSTALL_COMPLETE,
        InstallEquipmentStatusEnum::COMPLETE,
    ]))
        <div class="col-sm-3">
            <x-forms.date-input id="end_date" :value="$d->end_date" :label="__('install_equipments.end_date')" :optionals="['placeholder' => __('lang.select_date')]" />
        </div>
    @endif
</div>
@endif
<div class="block-link-list mb-4">
    <div class="row">
        @foreach ($link_list as $key => $item)
            <div class="col-6 col-lg-3">
                <div class="block">
                    <div class="block-content">
                        <p class="fw-semibold mb-1">{{ __('install_equipments.'. $key) }}</p>
                        @if (isset($item['route']))
                            <a class="fw-bolder mb-0" href="{{ $item['route'] ?? null }}" target="_blank">
                                <i class="fa fa-download"></i> <strong>{{ $item['worksheet_no'] }}</strong>
                            </a>
                        @else 
                            @if (isset($item['worksheet_no']))
                                <strong>{{ $item['worksheet_no'] }}</strong>
                            @else
                                -
                            @endif
                        @endif
                        @if (isset($item['link']))
                            <span class="ms-2">|</span>
                            <a class="fw-bolder mb-0 ms-2" href="{{ $item['link'] ?? null }}" target="_blank">
                                <i class="si si-link text-dark"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>