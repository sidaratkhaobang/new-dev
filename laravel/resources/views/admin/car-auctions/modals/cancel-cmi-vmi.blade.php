<div class="modal fade" id="modal-cancel-cmi-vmi" tabindex="-1" aria-labelledby="modal-cancel-cmi-vmi"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancel-cmi-vmi-modal-label"><i class="icon-menu-document-cancle me-1"></i>
                    {{ __('car_auctions.title_cmi') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1 mt-4">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="" class="text-start col-form-label">{{ __('cars.license_plate') }}</label>
                        <input class="form-control" name="license_plate" type="text" id="license_plate" disabled>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.date-input id="close_cmi_vmi_date" :value="null" :label="__('car_auctions.close_cmi_vmi_date')"
                            :optionals="['date_enable_time' => true]" />
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-12 text-end">
                        <x-forms.hidden id="id" :value="$d->id" />
                        <x-forms.hidden id="status" :value="$d->status" />
                        <button type="button" class="btn btn-secondary btn-clear-cancel-cmi-vmi"
                            data-bs-dismiss="modal" style="width: 150px;">{{ __('lang.back') }}</button>
                        <button type="button" class="btn btn-primary btn-save-cancel-cmi-vmi" style="width: 150px;">
                            <i class="icon-save me-1"></i> {{ __('lang.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(".btn-clear-cancel-cmi-vmi").on("click", function() {
            $('#modal-cancel-cmi-vmi').modal('hide');
        });

        $(".btn-save-cancel-cmi-vmi").on("click", function() {
            var close_cmi_vmi_date = document.getElementById("close_cmi_vmi_date").value;
            var id = document.getElementById("id").value;
            var status = '{{ \App\Enums\CarAuctionStatusEnum::READY_AUCTION }}';
            var prev_status = document.getElementById("status").value;
            var type_modal = 'cmi_vmi';
            var data = {
                close_cmi_vmi_date: close_cmi_vmi_date,
                id: id,
                status: status,
                prev_status: prev_status,
                type_modal: type_modal,
            };
            let storeUri = "{{ route('admin.car-auctions.store') }}";
            showLoading();
            axios.post(storeUri, data).then(response => {
                if (response.data.success) {
                    hideLoading();
                    $('#modal-cancel-cmi-vmi').modal('hide');
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
                }
            }).catch(error => {
                //
            });
        });
    </script>
@endpush
