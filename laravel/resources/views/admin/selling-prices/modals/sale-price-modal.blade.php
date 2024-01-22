<div class="modal fade" id="modal-sale-price" tabindex="-1" aria-labelledby="modal-sale-price" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sale-price-modal-label"><i class="fa fa-comment-dollar"></i>
                    {{ __('selling_prices.sale_price') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <form id="save-form-sale-price">
                    <div class="row push mb-4">
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="price" :value="null" :label="__('selling_prices.price')"
                                :optionals="[
                                    'input_class' => 'number-format col-sm-4',
                                ]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="vat_value" :value="null" :label="__('selling_prices.vat')" />
                            <x-forms.hidden id="vat" :value="null" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="total_value" :value="null" :label="__('selling_prices.total')" />
                            <x-forms.hidden id="total" :value="null" />
                        </div>
                    </div>
                    <div id="sale-price" v-cloak data-detail-uri="" data-title="">
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
                                        <input type="hidden" v-bind:name="'arr_sale_price['+ index +'][id]'"
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
                            <button type="button" class="btn btn-secondary btn-hide-sale-price"
                                data-dismiss="modal">{{ __('lang.cancel') }}</button>
                            <button type="button" class="btn btn-primary btn-save-sale-price"><i
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
        $('#vat_value').prop('disabled', true);
        $('#total_value').prop('disabled', true);

        $("#price").on("input", function() {
            price = $(this).val();
            price = parseFloat(price.replace(/,/g, ''));
            vat = 0;
            total = 0;
            if ((price)) {
                vat = parseFloat(parseFloat(price) * 7 / 107).toFixed(2);
                total = (parseFloat(parseFloat(price) + parseFloat(vat)).toFixed(2));
            }
            $('#vat').val(numberWithCommas(vat));
            $('#vat_value').val(numberWithCommas(vat));
            $('#total').val(numberWithCommas(total));
            $('#total_value').val(numberWithCommas(total));
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }

        $(".btn-hide-sale-price").on("click", function() {
            salePriceVue.removeAll();
            $('#modal-sale-price').modal('hide');
        });

        let salePriceVue = new window.Vue({
            el: '#sale-price',
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
                        $("#sale-price").show();
                    }
                },
                removeAll: function() {
                    this.arr_car = [];
                },
            },
            props: ['title'],
        });

        $(".btn-save-sale-price").on("click", function() {
            let storeUri = "{{ route('admin.selling-prices.sale-price') }}";
            var formData = new FormData(document.querySelector('#save-form-sale-price'));
            axios.post(storeUri, formData).then(response => {
                $('#modal-sale-price').modal('hide');
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
