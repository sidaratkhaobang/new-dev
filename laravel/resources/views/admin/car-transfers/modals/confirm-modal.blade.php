<div class="modal fade" id="modal-confirm" tabindex="-1" aria-labelledby="modal-confirm" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="confirm-modal-label">ยืนยันการบันทึกข้อมูล</h5>
            </div>
            <div class="modal-body pb-1">
                <p id="confirm-modal-body">กรุณากด ’ยืนยัน’ เพื่อเปิดใบงาน</p>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-confirm"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-submit-confirm">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="confirm_status" id="confirm_status">
                        <input type="hidden" name="confirm_id" id="confirm_id" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        $(".btn-submit-confirm").on("click", function() {

            $('#modal-confirm').modal('hide');
            window.location.href = "{{ route('admin.car-transfers.index')}}";
        });

        $(".btn-hide-confirm").on("click", function() {
            $('#modal-confirm').modal('hide');
        });
    </script>
@endpush
