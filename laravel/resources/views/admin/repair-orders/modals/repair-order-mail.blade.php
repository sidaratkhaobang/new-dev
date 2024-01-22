<div class="modal fade" id="modal-repair-order-mail" aria-labelledby="modal-repair-order-mail" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repair-order-mail-modal-label"><i class="fa fa-location-arrow"></i>ส่งอีเมล
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <label for="email" class="text-start col-form-label">กรอกอีเมลที่ต้องการ</label>
                        <div class="tag-field js-tags" id="js-tag-car">
                            <input type="text" class="form-control js-tag-input" id="email" name="email"
                                placeholder="ระบุข้อมูล...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="sendMail()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
