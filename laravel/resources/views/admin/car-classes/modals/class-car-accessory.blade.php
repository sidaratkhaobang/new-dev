<div class="modal fade" id="modal-class-car-accessory" aria-labelledby="modal-class-car-accessory" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูล</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h4 class="fw-light text-gray-darker">{{ __('car_classes.accessories_table_name') }}</h4>
            <hr>
            <div class="row push">
                <div class="col-sm-12">
                    <x-forms.select-option id="accessory_field" :value="null" :list="null" :label="__('car_classes.accessories')" 
                    :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                </div>
                {{-- <div class="col-sm-4">
                    <x-forms.select-option id="accessory_version_field" :value="null" :list="null" :label="__('car_classes.class')" 
                    :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                </div> --}}
            </div>
            <div class="row push mb-4">
                <div class="col-sm-12">
                    <x-forms.input-new-line id="accessory_remark_field" :value="null" :label="__('car_classes.remark')" />
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