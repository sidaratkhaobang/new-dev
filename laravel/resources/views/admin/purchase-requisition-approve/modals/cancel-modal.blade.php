<div class="modal fade" id="modal-cancel" tabindex="-1" aria-labelledby="modal-cancel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="cancel-modal-label">ยืนยันยกเลิกใบขอซื้อ</h5>
            </div>
            <div class="modal-body pb-1">
                <p id="cancel-modal-body">กรุณากรอกเหตุผล ยกเลิกใบขอซื้อในครั้งนี้</p>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <label for="reject_reason"
                            class="text-start col-form-label" id="cancel-modal-text">เหตุผลยกเลิกใบขอซื้อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control col-sm-4" id="reject_reason_text" name="reject_reason_text"  >
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-reject"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-submit-reject">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="cancel_status" id="cancel_status">
                        <input type="hidden" name="cancel_id" id="cancel_id" value="">
                        <input type="hidden" name="redirect" id="redirect" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-submit-reject").on("click", function() {
            var ids = document.getElementById("cancel_id").value;
            ids = ids.split(',');
            var data = {
                pr_status: document.getElementById("cancel_status").value,
                reason: document.getElementById("reject_reason_text").value,
                redirect: document.getElementById("redirect").value,
                purchase_requisitions: ids
            };
            $('#modal-cancel').modal('hide');
            updatePurchaseRequisitionStatus(data);
        });

        $(".btn-hide-reject").on("click", function() {
            $('#modal-cancel').modal('hide');
        });
    </script>
@endpush
