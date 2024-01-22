<div class="modal fade" id="modal-historical-car" tabindex="-1" aria-labelledby="modal-historical-car" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historical-car-modal-label">เพิ่มข้อมูลการใช้งานรถ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="license_plate_field" :value="null" :list="$license_plate_list"
                            :label="__('gps.license_plate')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <label class="text-start col-form-label">{{ __('gps.date') }}<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="js-flatpickr form-control flatpickr-input" id="start_date"
                                name="start_date" value="" placeholder="{{ __('lang.select_date') }}"
                                 data-autoclose="true" data-today-highlight="true">
                            <input type="text" class="js-flatpickr form-control flatpickr-input" id="end_date"
                                name="end_date" value="" placeholder="{{ __('lang.select_date') }}"
                                data-week-start="1" data-autoclose="true" data-today-highlight="true">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-start col-form-label">{{ __('gps.all_time') }}</label>
                        <div class="input-group">
                            <input type="text" class="js-flatpickr form-control flatpickr-input" id="start_time"
                                name="start_time" data-enable-time="true" value="" data-no-calendar="true"
                                data-date-format="H:i" data-time_24hr="true" readonly="readonly"
                                placeholder="{{ __('lang.select_time') }}" />
                            <input type="text" class="js-flatpickr form-control flatpickr-input" id="end_time"
                                name="end_time" data-enable-time="true" value="" data-no-calendar="true"
                                data-date-format="H:i" data-time_24hr="true" readonly="readonly"
                                placeholder="{{ __('lang.select_time') }}" />
                        </div>
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
