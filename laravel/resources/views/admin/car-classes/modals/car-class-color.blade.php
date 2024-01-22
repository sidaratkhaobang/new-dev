<div class="modal fade" id="modal-car-class-color"  role="dialog" style="overflow:hidden;"  aria-labelledby="modal-car-class-color"   >
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="car-color-modal-label">{{ __('lang.add_data') }}</h5>
        </div>
        <div class="modal-body">
            <h4 class="fw-light text-gray-darker">{{ __('car_classes.color_table_name') }}</h4>
            <hr>
            <div class="row push">
                <div class="col-sm-4">
                    <x-forms.select-option id="color_field" :value="null" :list="null" :label="__('car_classes.color')"
                    :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                </div>
                <div class="col-sm-4">
                  <x-forms.input-new-line id="standard_price_field" :value="$d->standard_price" :label="__('car_classes.standard_price')"
                    :optionals="['type' => 'number', 'placeholder' => '0']" />
                </div>
                <div class="col-sm-4">
                  <x-forms.input-new-line type="number" id="color_price_field" :value="$d->color_price" :label="__('car_classes.color_price')"
                    :optionals="['type' => 'number', 'placeholder' => '0']" />
              </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-4">
                  <x-forms.input-new-line id="total_price_field" :value="$d->total_price" :label="__('car_classes.total_price')" />
                </div>
                <div class="col-sm-8">
                  <x-forms.input-new-line id="remark_field" :value="$d->remark" :label="__('car_classes.remark')" />
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
          <button type="button" class="btn btn-primary"  onclick="addCarColor()">{{ __('lang.save') }}</button>
        </div>
      </div>
    </div>
  </div>