<div class="modal fade" id="modal-send-auction" aria-labelledby="modal-send-auction" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="icon-menu-car me-1"></i>
                    {{ __('car_auctions.title_send_auction') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form-send-auction">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="null" id="auction_place" :list="$auction_place"
                                :label="__('car_auctions.auction_place')" :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="send_auction_date" :value="null" :label="__('car_auctions.send_auction_date')"
                                :optionals="['required' => true, 'date_enable_time' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.radio-inline id="is_forklift" :value="null" :list="$is_need"
                                :label="__('car_auctions.need_forklift')" />
                        </div>
                        <div class="col-sm-3" id="select_driver" style="display: none;">
                            <x-forms.radio-inline id="is_driver" :value="null" :list="$is_need"
                                :label="__('car_auctions.need_driver')" />
                        </div>
                    </div>
                    {{-- need_forklift --}}
                    <div id="need_forklift" style="display: none;">
                        <h5 class="modal-title mt-4 mb-3"><i class="icon-document me-1"></i>
                            {{ __('car_auctions.original_info') }}
                        </h5>
                        <div class="row">
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="original_place" :value="null" :label="__('car_auctions.original_place')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="original_date" :value="null" :label="__('car_auctions.original_date')"
                                    :optionals="['date_enable_time' => true, 'required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="original_contact" :value="null" :label="__('car_auctions.original_contact')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="original_tel" :value="null" :label="__('car_auctions.original_tel')"
                                    :optionals="['required' => true]" />
                            </div>
                        </div>
                        <h5 class="modal-title mt-4 mb-3"><i class="icon-document me-1"></i>
                            {{ __('car_auctions.destination_info') }}
                        </h5>
                        <div class="row">
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="destination_place" :value="null" :label="__('car_auctions.destination_place')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="destination_date" :value="null" :label="__('car_auctions.destination_date')"
                                    :optionals="['date_enable_time' => true, 'required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="destination_contact" :value="null" :label="__('car_auctions.destination_contact')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="destination_tel" :value="null" :label="__('car_auctions.destination_tel')"
                                    :optionals="['required' => true]" />
                            </div>
                        </div>
                    </div>

                    {{-- need_driver --}}
                    <div id="need_driver" style="display: none;">
                        <div class="row">
                            <div class="col-sm-3">
                                <x-forms.date-input id="driver_date" :value="null" :label="__('car_auctions.driver_date')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="driver_contact" :value="null" :label="__('car_auctions.driver_contact')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="driver_tel" :value="null" :label="__('car_auctions.driver_tel')"
                                    :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="driver_place" :value="null" :label="__('car_auctions.driver_place')"
                                    :optionals="['required' => true]" />
                            </div>
                        </div>
                    </div>

                    {{-- no_need_driver --}}
                    <div id="no_need_driver" style="display: none;">
                        <div class="row">
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="no_driver_contact" :value="null" :label="__('car_auctions.driver_contact')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="no_driver_tel" :value="null" :label="__('car_auctions.driver_tel')" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div id="send-auction" v-cloak data-detail-uri="" data-title="">
                            <div class="table-wrap">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                        <th>{{ __('cars.license_plate') }}</th>
                                        <th>{{ __('gps.car_class') }}</th>
                                        <th>{{ __('car_classes.manufacturing_year') }}</th>
                                        <th>{{ __('selling_prices.mileage') }}</th>
                                    </thead>
                                    <tbody v-if="arr_car.length > 0">
                                        <tr v-for="(item, index) in arr_car">
                                            <td>@{{ item.license_plate }}</td>
                                            <td>@{{ item.car_class_name }}</td>
                                            <td>@{{ item.car_class_year }}</td>
                                            <td>@{{ item.mileage }}</td>
                                            <input type="hidden" v-bind:name="'arr_send_auction['+ index +'][id]'"
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
                    <div class="row mt-4 mb-4">
                        <div class="col-12 text-end">
                            <input name="change_status" type="hidden" id="change_status">
                            <button type="button" class="btn btn-secondary btn-clear-send-auction"
                                data-bs-dismiss="modal" style="width: 150px;">{{ __('lang.back') }}</button>
                            <button type="button" class="btn btn-primary btn-save-send-auction" style="width: 150px;">
                                <i class="icon-save me-1"></i> {{ __('lang.save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(".btn-clear-send-auction").on("click", function() {
            $('#modal-send-auction').modal('hide');
        });

        $('input[name="is_forklift"]').on("click", function() {
            $("#original_place").val('');
            $("#original_date").val('');
            $("#original_contact").val('');
            $("#original_tel").val('');
            $("#destination_place").val('');
            $("#destination_date").val('');
            $("#destination_contact").val('');
            $("#destination_tel").val('');
            $("#driver_date").val('');
            $("#driver_contact").val('');
            $("#driver_tel").val('');
            $("#driver_place").val('');
            $("#no_driver_contact").val('');
            $("#no_driver_tel").val('');
            $('[name="is_driver"]').prop('checked', false);
            if ($('input[name="is_forklift"]:checked').val() === '1') {
                document.getElementById("need_forklift").style.display = "block"
                document.getElementById("select_driver").style.display = "none"
                document.getElementById("need_driver").style.display = "none"
                document.getElementById("no_need_driver").style.display = "none"
            } else {
                document.getElementById("need_forklift").style.display = "none"
                document.getElementById("select_driver").style.display = "block"
                document.getElementById("need_driver").style.display = "none"
                document.getElementById("no_need_driver").style.display = "none"
            }
        });

        $('input[name="is_driver"]').on("click", function() {
            $("#original_place").val('');
            $("#original_date").val('');
            $("#original_contact").val('');
            $("#original_tel").val('');
            $("#destination_place").val('');
            $("#destination_date").val('');
            $("#destination_contact").val('');
            $("#destination_tel").val('');
            $("#driver_date").val('');
            $("#driver_contact").val('');
            $("#driver_tel").val('');
            $("#driver_place").val('');
            $("#no_driver_contact").val('');
            $("#no_driver_tel").val('');
            if ($('input[name="is_driver"]:checked').val() === '1') {
                document.getElementById("need_forklift").style.display = "none"
                document.getElementById("need_driver").style.display = "block"
                document.getElementById("no_need_driver").style.display = "none"
            } else {
                document.getElementById("need_forklift").style.display = "none"
                document.getElementById("need_driver").style.display = "none"
                document.getElementById("no_need_driver").style.display = "block"
            }
        });

        let sendAuctionVue = new window.Vue({
            el: '#send-auction',
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
                        cars.car_class_year = e.car_class_year;
                        cars.mileage = e.mileage;
                        _this.arr_car.push(cars);
                        $("#send-auction").show();
                    }
                },
                removeAll: function() {
                    this.arr_car = [];
                },
            },
            props: ['title'],
        });

        $(".btn-save-send-auction").on("click", function() {
            let storeUri = "{{ route('admin.car-auctions.send-auction') }}";
            var prev_status = '{{ \App\Enums\CarAuctionStatusEnum::READY_AUCTION }}';
            var auction_place = document.getElementById("auction_place").value;
            var formData = new FormData(document.querySelector('#save-form-send-auction'));
            formData.append('prev_status', prev_status);
            formData.append('auction_place', auction_place);
            axios.post(storeUri, formData).then(response => {
                $('#modal-send-auction').modal('hide');
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
