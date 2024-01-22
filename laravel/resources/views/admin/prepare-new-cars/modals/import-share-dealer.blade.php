<div class="modal fade" id="modal-import-cars" aria-labelledby="modal-import-cars" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="car-accessory-modal-label">แชร์ให้ Dealer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row push mb-4">
                <div class="col-sm-12">
                    <x-forms.input-new-line id="email" :value="null" :label="__('import_cars.email')" :optionals="[ 'placeholder' => 'ระบุข้อมูล', 'type' => 'email']"/>
                </div>
            </div>
      
                
        </div>
        <div class="modal-footer">
          <div class="col-sm-9"><a href="" class="copy_link">คัดลอกลิงก์</a></div>
          <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
          <button type="button" class="btn btn-primary"  onclick="shareDealer()">{{ __('lang.save') }}</button>
        </div>
      </div>
    </div>
  </div>