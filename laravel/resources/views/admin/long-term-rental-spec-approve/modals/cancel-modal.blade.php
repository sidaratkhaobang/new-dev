<div class="modal fade" id="modal-cancel" tabindex="-1" aria-labelledby="modal-cancel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="cancel-modal-label">ยืนยันไม่อนุมัติ สเปครถและอุปกรณ์</h5>
            </div>
            <div class="modal-body pb-1">
                <p id="approve-modal-body">เมื่อยืนยันไม่อนุมัติสเปครถและอุปกรณ์แล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการต่อไป</p>
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
            var id = document.getElementById("cancel_id").value;
            var data = {
                spec_status: document.getElementById("cancel_status").value,
                redirect: document.getElementById("redirect").value,
                lt_rental_id: id
            };
            $('#modal-cancel').modal('hide');
            updateSpecStatus(data);
        });

        $(".btn-hide-reject").on("click", function() {
            $('#modal-cancel').modal('hide');
        });
    </script>
@endpush
