<div class="modal fade" id="modal-car-slot" aria-labelledby="modal-car-slot" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-slot-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="start_slot_no_field" :value="null" :label="__('parking_lots.start_slot_no')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="end_slot_no_field" :value="null" :label="__('parking_lots.end_slot_no')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="car_group_id" :value="null" :list="null" :label="__('parking_lots.group_car')"
                            :optionals="[
                                'select_class' => 'js-select2 js-select2-custom',
                                'ajax' => true,
                                'multiple' => true,
                                'required' => true,
                            ]" />
                    </div>
                </div>
                <div class="row push mb-4">
                <div class="col-sm-6">
                    <x-forms.select-option id="zone_type_id" :value="null" :list="null" :label="__('parking_lots.zone_type')" :optionals="[   
                                'select_class' => 'js-select2 js-select2-custom', 
                                'ajax' => true,
                                'required' => true,
                                ]"  />
                </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.radio-inline id="area_size" :value="null" :label="__('parking_lots.slot_size')" :optionals="['required' => true]"
                            :list="[
                                [
                                    'name' => __('parking_lots.small_slot'),
                                    'value' => \App\Enums\CarParkSlotSizeEnum::SMALL,
                                ],
                                ['name' => __('parking_lots.big_slot'), 'value' => \App\Enums\CarParkSlotSizeEnum::BIG],
                            ]" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addCarSlot()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>