<div class="modal fade" id="excel-modal" aria-labelledby="excel-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excel-modal-label">ดาวน์โหลด Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="excel_type_id" :value="null" :list="$excel_type_list"
                            :label="__('gps.type')" />
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-6" id="install_date" style="display: none;">
                        <label class="text-start col-form-label"
                            for="from_install_date">{{ __('gps.install_gps_date') }}</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                data-autoclose="true" data-today-highlight="true">
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="from_install_date" name="from_install_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="to_install_date" name="to_install_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="revoke_date" style="display: none;">
                        <label class="text-start col-form-label"
                            for="from_revoke_date">{{ __('gps.stop_signal_date') }}</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                data-autoclose="true" data-today-highlight="true">
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="from_revoke_date" name="from_revoke_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="to_revoke_date" name="to_revoke_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="service_charge" style="display: none;">
                        <label class="text-start col-form-label"
                            for="from_revoke_date">{{ __('gps.stop_signal_date') }}</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                data-autoclose="true" data-today-highlight="true">
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="from_revoke_date" name="from_revoke_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text font-w600">
                                        <i class="fa fa-fw fa-arrow-right"></i>
                                    </span>
                                </div>
                                <input type="text" class="js-flatpickr form-control flatpickr-input"
                                    id="to_revoke_date" name="to_revoke_date" value=""
                                    placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                    data-today-highlight="true">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-hide-excel"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="exportExcel()">{{ __('lang.download') }}</button>
            </div>
        </div>
    </div>
</div>
