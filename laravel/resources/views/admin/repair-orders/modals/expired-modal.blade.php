<div class="modal fade" id="modal-repair-order-expired" aria-labelledby="modal-repair-order-expired" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repair-order-expired-modal-label"><i class="far fa-clock"></i>
                    ต่ออายุใบสั่งซ่อม
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="repair_order_no" :value="$d->worksheet_no" :label="__('repair_orders.worksheet_no')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="repair_type_modal" :value="$d->repair ? __('repairs.repair_type_' . $d->repair->repair_type) : null" :label="__('repairs.repair_type')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="repair_date_old" :value="$d->repair ? $d->repair->repair_date : null" :label="__('repair_orders.repair_date_old')" :optionals="['date_enable_time' => true]"
                            :optionals="[
                                'date_enable_time' => true,
                                'placeholder' => __('lang.select_date'),
                            ]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="repair_date_new" :value="null" :label="__('repair_orders.repair_date_new')" :optionals="['date_enable_time' => true]"
                            :optionals="[
                                'date_enable_time' => true,
                                'placeholder' => __('lang.select_date'),
                            ]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="center_date_old" :value="null" :label="__('repair_orders.center_date_old')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="center_date_new" :value="null" :label="__('repair_orders.center_date_new')"
                            :optionals="[
                                'required' => true,
                            ]" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-hide-modal"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-expired"
                    data-status="EXPIRED">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $index_uri = '{{ $index_uri }}';
        $(".btn-hide-modal").on("click", function() {
            $('#modal-repair-order-expired').modal('hide');
            window.location.href = $index_uri;
        });

        $(".btn-close").click(function() {
            $('#modal-repair-order-expired').modal('hide');
            window.location.href = $index_uri;
        });

        $(".btn-save-expired").on("click", function() {
            var route_update = "{{ route('admin.repair-orders.update-status') }}";
            var data = {
                status: '{{ \App\Enums\RepairStatusEnum::EXPIRED }}',
                id: document.getElementById("id").value,
                repair_date_new: document.getElementById("repair_date_new").value,
                center_date_new: document.getElementById("center_date_new").value,
            };
            axios.post(route_update, data).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                    $('#modal-copy').modal('hide');
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
                    text: response.data.message,
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
