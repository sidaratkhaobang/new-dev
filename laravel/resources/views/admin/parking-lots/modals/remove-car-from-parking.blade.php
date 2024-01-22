<div class="modal fade" id="modal-remove-car-from-parking" role="dialog" style="overflow:hidden;" aria-labelledby="modal-remove-car-from-parking">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('parking_lots.remove_car_from_parking') }}</h5>
            </div>
            <div class="modal-body pt-0">
                <form id="form-modal-remove-car-from-parking" method="POST" >
                    <div class="row">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="car_id_inside" :value="null" :list="[]" :label="__('parking_lots.car_id')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search me-2" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-remove-car-from-parking">{{ __('parking_lots.remove_car_from_parking') }}</button>
            </div>
        </div>
    </div>
</div>