<div class="modal fade" id="modal-cancel" tabindex="-1" aria-labelledby="modal-cancel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start" id="cancel-modal-label">ยืนยันการปิดใช้งานใบงานนำรถเข้า/ออก</h5>
            </div>
            <div class="modal-body pb-1">
                {{-- <p id="cancel-modal-body">กรุณากรอกเหตุผล ยกเลิกใช้งานใบงานนำรถเข้า/ออกนี้</p> --}}
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <label for="cancel_reason" class="text-start col-form-label"
                            id="cancel-modal-text">กรุณากรอกเหตุผล ยกเลิกใช้งานใบงานนำรถเข้า/ออกนี้ <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control col-sm-4" id="cancel_reason" name="cancel_reason">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-end">
                        <button type="button" class="btn btn-secondary btn-block btn-hide-cancel"
                            data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        <button type="button"
                            class="btn btn-primary btn-block btn-submit-cancel">{{ __('lang.confirm') }}</button>
                        <input type="hidden" name="cancel_status" id="cancel_status">
                        <input type="hidden" name="cancel_id" id="cancel_id" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-submit-cancel").on("click", function() {
            var ids = document.getElementById("cancel_id").value;
            ids = ids.split(',');
            var data = {
                cancel_status: document.getElementById("cancel_status").value,
                cancel_reason: document.getElementById("cancel_reason").value,
                car_park_transfer: ids,
            };
            $('#modal-cancel').modal('hide');
            updateStatus(data);
        });
        $(".btn-hide-cancel").on("click", function() {
            $('#modal-cancel').modal('hide');
        });

        function updateStatus(data) {
            var updateUri = "{{ route('admin.car-park-transfers.update-status') }}";
            axios.post(updateUri, data).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }
    </script>
@endpush
