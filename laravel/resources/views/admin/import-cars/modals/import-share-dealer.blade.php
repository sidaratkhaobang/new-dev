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
                        <label for="email" class="text-start col-form-label">{{ __('import_cars.email') }}</label>
                        <div class="tag-field js-tags" id="js-tag-car">
                            <input type="text" class="form-control js-tag-input" id="email" name="email"
                                placeholder="ระบุข้อมูล...">
                        </div>
                        {{-- <x-forms.input-new-line id="email" :value="null" :label="__('import_cars.email')" :optionals="[ 'placeholder' => 'ระบุข้อมูล', 'type' => 'email']"/> --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row flex-grow-1" >
                    <div class="col-6 ps-0">
                        <a href="" class="copy_link">คัดลอกลิงก์</a>
                    </div>
                    <div class="col-6 pe-0 text-end" >
                        <button type="button" class="btn btn-secondary btn-clear-search"
                            data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button" class="btn btn-primary" onclick="sendMail()">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
