<div class="modal fade" id="modal-driver-wage" tabindex="-1" aria-labelledby="modal-driver-wage" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-wage-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('drivers.driver_wage_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="driver_wage_field" :value="null" :list="null"
                            :label="__('drivers.wage_name')" :optionals="[
                                'ajax' => true,
                                'select_class' => 'js-select2 js-select2-custom',
                                'required' => true,
                            ]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="service_type_field" :value="null" :list="null" :label="__('drivers.service_type')" />
                        <input type="hidden" id="service_type_id_field_hidden"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="driver_wage_category_field" :value="null" :list="null" :label="__('drivers.wage_category')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="wage_cal_type_field" :value="null" :list="null" :label="__('drivers.wage_cal_type')" />
                    </div>
                    <div class="col-sm-3" id="input_amount" style="display: none">
                        <label class="text-start col-form-label" for="amount_field">{{__('drivers.amount')}}</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="amount_field" name="amount_field" />
                            <input type="hidden" id="amount_type_field" name="amount_type_field"/>
                            <div class="input-group-text" id="dropdown-toggle-select-type" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span id="amount_type_text">฿</span>&nbsp;<i class="fa fa-chevron-down"></i>
                            </div>
                            <div class="dropdown-menu">
                                <span class="dropdown-item" id="select_amount_type_baht" onclick="select_amount_type('baht')" style="display: none">
                                    ฿ {{__('drivers.baht')}}
                                </span>
                                <span class="dropdown-item" id="select_amount_type_percent" onclick="select_amount_type('percent')" style="display: none">
                                    % {{__('drivers.percent')}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveDriverWage()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
