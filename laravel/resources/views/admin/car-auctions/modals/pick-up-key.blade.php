<div class="modal fade" id="modal-pick-up-key" tabindex="-1" aria-labelledby="modal-pick-up-key" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pick-up-key-modal-label"><i class="icon-menu-key me-1"></i>
                    {{ __('car_auctions.title_key') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1 mt-4">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="" class="text-start col-form-label">{{ __('cars.license_plate') }}</label>
                        <input class="form-control" name="license_plate_key" type="text" id="license_plate_key"
                            disabled>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.date-input id="pick_up_date" :value="null" :label="__('car_auctions.pick_up_date')" :optionals="['date_enable_time' => true]" />
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-12 text-end">
                        <x-forms.hidden id="id" :value="$d->id" />
                        <x-forms.hidden id="status" :value="$d->status" />
                        <button type="button" class="btn btn-secondary btn-clear-pick-up-key" data-bs-dismiss="modal"
                            style="width: 150px;">{{ __('lang.back') }}</button>
                        <button type="button" class="btn btn-primary btn-save-pick-up-key" style="width: 150px;">
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
        $(".btn-clear-pick-up-key").on("click", function() {
            $('#modal-pick-up-key').modal('hide');
        });

        $(".btn-save-pick-up-key").on("click", function() {
            var pick_up_date = document.getElementById("pick_up_date").value;
            var id = document.getElementById("id").value;
            var status = '{{ \App\Enums\CarAuctionStatusEnum::READY_AUCTION }}';
            var prev_status = document.getElementById("status").value;
            var type_modal = 'pick_up_key';
            var data = {
                pick_up_date: pick_up_date,
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
                    $('#modal-pick-up-key').modal('hide');
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
