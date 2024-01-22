<div class="modal fade" id="modal-approve" tabindex="-1" aria-labelledby="modal-approve" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="approve-modal-label">ยืนยันอนุมัติ สเปครถและอุปกรณ์</h5>
            </div>
            <div class="modal-body pb-1">
                <p id="approve-modal-body">เมื่อยืนยันสเปครถและอุปกรณ์แล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป</p>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-approve"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-submit-approve">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="approve_status" id="approve_status">
                        <input type="hidden" name="approve_id" id="approve_id" value="">
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

        $(".btn-submit-approve").on("click", function() {
            var id = document.getElementById("approve_id").value;
            var data = {
                spec_status: document.getElementById("approve_status").value,
                redirect: document.getElementById("redirect").value,
                lt_rental_id: id
            };
            $('#modal-approve').modal('hide');
            updateSpecStatus(data);
        });

        $(".btn-hide-approve").on("click", function() {
            $('#modal-approve').modal('hide');
        });
    </script>
@endpush
