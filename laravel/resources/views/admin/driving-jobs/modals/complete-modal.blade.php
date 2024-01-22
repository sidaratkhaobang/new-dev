<div class="modal fade" id="modal-complete" tabindex="-1" aria-labelledby="modal-complete" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="complete-modal-label">ยืนยันรายจ่าย</h5>
            </div>
            <div class="modal-body pb-1">
                <p id="complete-modal-body">เมื่อบันทึกและยืนยันรายจ่ายงานคนขับแล้ว ไม่สามารถกลับมาแก้ไขรายละเอียดได้</p>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-complete"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-save-complete">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="is_confirm_wage" id="is_confirm_wage">
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        $(".btn-save-complete").on("click", function() {
            let storeUri = "{{ route('admin.driving-jobs.update-status') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var is_confirm_wage = document.getElementById("is_confirm_wage").value;
            formData.append('is_confirm_wage', is_confirm_wage);
            $('#modal-complete').modal('hide');
            saveForm(storeUri, formData);
        });

        $(".btn-hide-complete").on("click", function() {
            $('#modal-complete').modal('hide');
        });
    </script>
@endpush
