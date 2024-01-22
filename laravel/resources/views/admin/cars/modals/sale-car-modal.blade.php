<div class="modal fade" id="modal-send-sale-car" tabindex="-1" aria-labelledby="modal-send-sale-car" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-xs modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body pb-1 mt-4">
                <h5 class="text-center" style="font-size: 24px;"> คุณต้องการส่งขายรถคันนี้ใช่หรือไม่?</h5>
                <div class="row mb-4 mt-4">
                    <div class="col-6 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-hide-send"
                            style="width:50%;">{{ __('lang.cancel') }}</button>
                    </div>
                    <div class="col-6 text-start">
                        <button type="button" class="btn btn-primary btn-save-send" style="width:50%;"><i
                                class="fa fa-save"></i> {{ __('lang.save') }}</button>
                        <input type="hidden" name="car_data" id="car_data">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-hide-send").on("click", function() {
            $('#modal-send-sale-car').modal('hide');
        });

        $(".btn-save-send").on("click", function() {
            var ids = document.getElementById("car_data").value;
            ids = ids.split(',');
            var data = {
                car_ids: ids,
            };
            let storeUri = "{{ route('admin.cars.update-status') }}";
            showLoading();
            axios.post(storeUri, data).then(response => {
                if (response.data.success) {
                    hideLoading();
                    $('#modal-send-sale-car').modal('hide');
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'ส่งขายรถคันนี้เรียบร้อย',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                }
            }).catch(error => {
                //
            });
        });
    </script>
@endpush
