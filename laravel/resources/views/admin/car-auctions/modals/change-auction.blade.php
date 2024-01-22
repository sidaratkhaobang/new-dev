<div class="modal fade" id="modal-change-auction" tabindex="-1" aria-labelledby="modal-change-auction"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="change-auction-modal-label"><i class="icon-menu-building me-1"></i>
                    {{ __('car_auctions.title_change_auction') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1 mt-4">
                <div class="row">
                    <div class="col-sm-6">
                        <label for=""
                            class="text-start col-form-label">{{ __('car_auctions.original_auction_place') }}</label>
                        <input class="form-control" name="auction_old" type="text" id="auction_old" disabled>
                        <input name="auction_old_id" type="hidden" id="auction_old_id">
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option :value="null" id="auction_new_id" :list="null" :label="__('car_auctions.new_auction_place')"
                            :optionals="[
                                'ajax' => true,
                                'required' => true,
                            ]" />
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-12 text-end">
                        <input name="car_auction_id" type="hidden" id="car_auction_id">
                        <input name="status_old" type="hidden" id="status_old">
                        <button type="button" class="btn btn-secondary btn-clear-change-auction"
                            data-bs-dismiss="modal" style="width: 150px;">{{ __('lang.back') }}</button>
                        <button type="button" class="btn btn-primary btn-save-change-auction" style="width: 150px;">
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
        $(".btn-clear-change-auction").on("click", function() {
            $('#modal-change-auction').modal('hide');
        });

        $(".btn-save-change-auction").on("click", function() {
            var car_auction_id = document.getElementById("car_auction_id").value;
            var status_old = document.getElementById("status_old").value;
            var auction_old_id = document.getElementById("auction_old_id").value;
            var auction_new_id = document.getElementById("auction_new_id").value;
            var data = {
                id: car_auction_id,
                status_old: status_old,
                auction_old_id: auction_old_id,
                auction_new_id: auction_new_id,
            };
            let storeUri = "{{ route('admin.car-auctions.change-auction') }}";
            axios.post(storeUri, data).then(response => {
                if (response.data.success) {
                    hideLoading();
                    $('#modal-change-auction').modal('hide');
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
        });
    </script>
@endpush
