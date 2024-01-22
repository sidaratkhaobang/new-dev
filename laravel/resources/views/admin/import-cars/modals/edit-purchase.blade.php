<div class="modal fade" id="modal-edit-purchase" aria-labelledby="modal-edit-purchase" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="">แก้ไขข้อมูล</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <input type="hidden" id="car_class_id" value="">
        <div class="modal-body">
          <h4>{{ __('import_cars.car_detail') }}</h4>
          <hr>
          @include('admin.import-cars.sections.purchase-car-detail')
          <h4>{{ __('import_cars.accessory_detail') }}</h4>
          <hr>
          @include('admin.import-cars.sections.accessory-detail',['car_class_id'=>'car_class_id'])
          <h4>{{ __('import_cars.delivery_new_car') }}</h4>
          <hr>
          @include('admin.import-cars.sections.delivery-new-car')
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.back') }}</button>
          <button type="button" class="btn btn-primary" id="saveDetail" onclick="addDetail()">{{ __('lang.save') }}</button>
          <button type="button" class="btn btn-primary" id="saveRemark" onclick="addRemark()">{{ __('lang.save') }}</button>
        </div>
      </div>
    </div>
  </div>
