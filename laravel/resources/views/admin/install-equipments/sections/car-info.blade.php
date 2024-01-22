<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('install_equipments.car_info'),
        'block_icon_class' => 'icon-settings'
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="po_id" :value="$d->po_id" :list="null" :label="__('install_equipments.po')"
                    :optionals="[
                        'placeholder' => __('lang.search_placeholder'),
                        'ajax' => true,
                        'default_option_label' => $d->purchaseOrder?->po_no,
                    ]" />
            </div>
            {{-- <div class="col-sm-2">
                <x-forms.select-option id="car_code" :value="$d->car_id" 
                    :list="null"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $d->car_code,
                    ]"
                    :label="__('install_equipments.car_code')" />
            </div> --}}
            <div class="col-sm-4">
                <x-forms.select-option id="license_plate" :value="$d->car_id" 
                    :list="null"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $d->license_plate,
                    ]"
                    :label="__('install_equipments.car_detail')" />
            </div>
            {{-- <div class="col-sm-3">
                <x-forms.select-option id="engine_no" :value="$d->car_id" 
                    :list="null"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $d->engine_no,
                    ]"
                    :label="__('install_equipments.engine_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="chassis_no" :value="$d->car_id" 
                    :list="null"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $d->chassis_no,
                    ]"
                    :label="__('install_equipments.chasis_no')" />
            </div> --}}
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.upload-image :id="'attachment'" :label="__('install_equipments.document')" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('install_equipments.remark')" />
            </div>
            {{-- @if (strcmp($mode, MODE_UPDATE) === 0) --}}
                @if (in_array($d->status, [
                    InstallEquipmentStatusEnum::INSTALL_IN_PROCESS, 
                    InstallEquipmentStatusEnum::OVERDUE,
                    InstallEquipmentStatusEnum::DUE,
                    InstallEquipmentStatusEnum::INSTALL_COMPLETE,
                    InstallEquipmentStatusEnum::COMPLETE,
                ]))
                <div class="col-sm-3">
                    <x-forms.select-option id="temp_status" :list="$status_list" :value="$d->status" :label="__('lang.status')" />
                </div>
                @endif
            {{-- @endif --}}
        </div>
        @if (in_array($mode, [MODE_UPDATE, MODE_VIEW]))
            @include('admin.install-equipments.sections.additional')
        @endif
    </div>
</div>
