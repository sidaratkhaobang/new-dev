<div class="modal fade" id="modal-gps-car" tabindex="-1" aria-labelledby="modal-gps-car" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gps-car-modal-label">เพิ่มข้อมูลรถ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="license_plate_field" :value="null" :list="null"
                            :label="__('gps.license_plate')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="chassis_no_field" :value="null" :list="null"
                            :label="__('gps.chassis_no')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="engine_no_field" :value="null" :list="null"
                            :label="__('gps.engine_no')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="vid_field" :value="null" :list="null" :label="__('gps.vid')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="car_class_field" :value="null" :label="__('gps.car_class')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="car_color_field" :value="null" :label="__('gps.car_color')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.radio-inline id="is_check_gps_field" :value="null" :list="[
                            ['name' => __('gps.remove_gps'), 'value' => 1],
                            ['name' => __('gps.stop_gps'), 'value' => 2],
                        ]"
                            :label="__('gps.is_check_gps')" :optionals="['required' => true]"/>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="remark_field" :value="null" :label="__('gps.remark')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveCar()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
