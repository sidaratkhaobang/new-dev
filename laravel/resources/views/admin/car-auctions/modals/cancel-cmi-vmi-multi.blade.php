<div class="modal fade" id="modal-cancel-cmi-vmi-multi" aria-labelledby="modal-cancel-cmi-vmi-multi" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="icon-menu-document-cancle me-1"></i>
                    {{ __('car_auctions.title_cmi') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="save-form-cancel-cmi-vmi-multi">
                    <div class="row push mb-3">
                        <div class="col-sm-4">
                            <x-forms.date-input id="close_cmi_vmi_date" :value="null" :label="__('car_auctions.close_cmi_vmi_date')"
                                :optionals="['date_enable_time' => true, 'required' => true]" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div id="cancel-cmi-vmi-multi" v-cloak data-detail-uri="" data-title="">
                            <div class="table-wrap">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                        <th>{{ __('cars.license_plate') }}</th>
                                        <th>{{ __('cars.chassis_no') }}</th>
                                        <th>{{ __('cars.engine_no') }}</th>
                                    </thead>
                                    <tbody v-if="arr_car.length > 0">
                                        <tr v-for="(item, index) in arr_car">
                                            <td>@{{ item.license_plate }}</td>
                                            <td>@{{ item.chassis_no }}</td>
                                            <td>@{{ item.engine_no }}</td>
                                            <input type="hidden" v-bind:name="'arr_cancel_cmi_vmi['+ index +'][id]'"
                                                id="id" v-bind:value="item.id">
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="table-empty">
                                            <td class="text-center" colspan="4">“
                                                {{ __('lang.no_list') }}“</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-clear-cancel-cmi-vmi-multi"
                                data-dismiss="modal">{{ __('lang.cancel') }}</button>
                            <button type="button" class="btn btn-primary btn-save-cancel-cmi-vmi-multi"><i
                                    class="icon-save me-1"></i> {{ __('lang.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-clear-cancel-cmi-vmi-multi").on("click", function() {
            checkCancelCMIVMIVue.removeAll();
            $('#modal-cancel-cmi-vmi-multi').modal('hide');
        });

        let checkCancelCMIVMIVue = new window.Vue({
            el: '#cancel-cmi-vmi-multi',
            data: {
                arr_car: []
            },
            methods: {
                addByDefault: function(e) {
                    var _this = this;
                    var cars = {};
                    if (e.id) {
                        cars.id = e.id;
                        cars.license_plate = e.license_plate;
                        cars.chassis_no = e.chassis_no;
                        cars.engine_no = e.engine_no;

                        _this.arr_car.push(cars);
                        $("#cancel-cmi-vmi-multi").show();
                    }
                },
                removeAll: function() {
                    this.arr_car = [];
                },
            },
            props: ['title'],
        });

        $(".btn-save-cancel-cmi-vmi-multi").on("click", function() {
            let storeUri = "{{ route('admin.car-auctions.cancel-cmi-vmi') }}";
            var prev_status = '{{ \App\Enums\CarAuctionStatusEnum::PENDING_SALE }}';
            var formData = new FormData(document.querySelector('#save-form-cancel-cmi-vmi-multi'));
            formData.append('prev_status', prev_status);
            axios.post(storeUri, formData).then(response => {
                $('#modal-cancel-cmi-vmi-multi').modal('hide');
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
