<div class="modal fade" id="modal-request-approve" tabindex="-1" aria-labelledby="modal-request-approve"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="request-approve-modal-label"><i class="fa fa-comment-dollar"></i>
                    {{ __('selling_prices.sale_price') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <form id="save-form-request-approve">
                    <div class="row push mb-4">
                        <div class="col-sm-12">
                            <x-forms.input-new-line id="worksheet_no" :value="null" :label="__('selling_prices.worksheet_no')" />
                        </div>
                    </div>
                    <div id="request-approve" v-cloak data-detail-uri="" data-title="">
                        @include('admin.components.block-header', [
                            'block_header_class' => 'ps-0',
                            'text' => __('lang.total_list'),
                        ])
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <th style="width: 10%;">{{ __('cars.license_plate') }}</th>
                                    <th style="width: 10%;">{{ __('gps.car_class') }}</th>
                                    <th style="width: 10%;">{{ __('cars.chassis_no') }}</th>
                                    <th style="width: 10%;">{{ __('cars.engine_no') }}</th>
                                    <th style="width: 10%;">{{ __('car_classes.manufacturing_year') }}</th>
                                    <th style="width: 10%;">{{ __('selling_prices.car_color') }}</th>
                                    <th style="width: 10%;">{{ __('cars.registration_date') }}</th>
                                </thead>
                                <tbody v-if="arr_car.length > 0">
                                    <tr v-for="(item, index) in arr_car">
                                        <td>@{{ item.license_plate }}</td>
                                        <td>@{{ item.car_class_name }}</td>
                                        <td>@{{ item.chassis_no }}</td>
                                        <td>@{{ item.engine_no }}</td>
                                        <td>@{{ item.manufacturing_year }}</td>
                                        <td>@{{ item.car_color_name }}</td>
                                        <td>@{{ item.registration_date }}</td>
                                        <input type="hidden" v-bind:name="'arr_request_approve['+ index +'][id]'"
                                            id="id" v-bind:value="item.id">
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="7">“
                                            {{ __('lang.no_list') }}“</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-block btn-hide-request-approve"
                                data-dismiss="modal">{{ __('lang.cancel') }}</button>
                            <button type="button" class="btn btn-primary btn-save-request-approve"><i
                                    class="icon-save me-1"></i> {{ __('lang.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true);

        $(".btn-hide-request-approve").on("click", function() {
            requestApprove.removeAll();
            $('#modal-request-approve').modal('hide');
        });

        let requestApprove = new window.Vue({
            el: '#request-approve',
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
                        cars.car_class_name = e.car_class_name;
                        cars.chassis_no = e.chassis_no;
                        cars.engine_no = e.engine_no;
                        cars.manufacturing_year = e.manufacturing_year;
                        cars.car_color_name = e.car_color_name;
                        cars.registration_date = e.registration_date;

                        _this.arr_car.push(cars);
                        $("#request-approve").show();
                    }
                },
                removeAll: function() {
                    this.arr_car = [];
                },
            },
            props: ['title'],
        });

        $(".btn-save-request-approve").on("click", function() {
            let storeUri = "{{ route('admin.selling-prices.request-approve') }}";
            var formData = new FormData(document.querySelector('#save-form-request-approve'));
            axios.post(storeUri, formData).then(response => {
                $('#modal-request-approve').modal('hide');
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
        });
    </script>
@endpush
