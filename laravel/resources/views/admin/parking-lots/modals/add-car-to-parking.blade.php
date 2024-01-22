<div class="modal fade" id="modal-add-car-to-parking" role="dialog" style="overflow:hidden;" aria-labelledby="modal-add-car-to-parking">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('parking_lots.add_car_to_parking') }}</h5>
            </div>
            <div class="modal-body pt-0">
                <form id="form-modal-add-car-to-parking" method="POST" >
                    <div class="row mb-2">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="car_park_zone_id_outside" :value="null" :list="[]" :label="__('parking_lots.zone_code')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="car_id_outside" :value="null" :list="[]" :label="__('parking_lots.car_id')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                        <div class="col-sm-6" >
                            <x-forms.select-option id="car_park_id_outside" :value="null" :list="[]" :label="__('parking_lots.car_park_id')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search me-2" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-add-car-to-parking">{{ __('parking_lots.add_car_to_parking') }}</button>
            </div>
        </div>
    </div>
</div>