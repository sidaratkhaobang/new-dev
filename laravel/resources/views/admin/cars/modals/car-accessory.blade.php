<div class="modal fade" id="car-accessory-modal" aria-labelledby="car-accessory-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูลอุปกรณ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('cars.accessory_car_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-8">
                        <x-forms.select-option id="accessory_field" :value="null" :list="null" :label="__('cars.accessory')" 
                        :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount_field" :value="null" :label="__('cars.amount')" :optionals="['type' => 'number']"/>
                </div>
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="remark_field" :value="null" :label="__('lang.remark')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
            <button type="button" class="btn btn-primary"  onclick="addCarAccessory()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
  </div>