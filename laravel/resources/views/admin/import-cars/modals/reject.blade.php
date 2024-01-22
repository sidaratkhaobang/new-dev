<div class="modal fade" id="modal-reject-display" aria-labelledby="modal-edit-purchase" aria-hidden="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">ยืนยันแก้ไขข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row push mb-3">
                <div class="col-sm-10">
                    <input type="hidden" id="car_class_id" value="">
                    <label class="text-start col-form-label">ระบุเหตุผลที่ต้องแก้ไขข้อมูล</label> <label class="text-danger">*</label>
                    <input class="form-control" name="reject_reason" id="reject_reason" type="text"/>
                </div>
            </div>
          </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary" id="saveDetail"
                    onclick="rejectStatus()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>


