<div class="modal fade" id="modal-send-check-siganl" tabindex="-1" aria-labelledby="modal-send-check-siganl"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update-remove-modal-label">{{ __('gps.send_check_signal') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <form id="save-form-check">
                    <div id="send-car-to-check" v-cloak data-detail-uri="" data-title="">
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <th>{{ __('gps.chassis_no') }}</th>
                                    <th>{{ __('gps.license_plate') }}</th>
                                    <th>{{ __('gps.vid') }}</th>
                                    <th>{{ __('gps.must_check_date') }}</th>
                                </thead>
                                <tbody v-if="arr_check.length > 0">
                                    <tr v-for="(item, index) in arr_check">
                                        <td>@{{ item.chassis_no }}</td>
                                        <td>@{{ item.license_plate }}</td>
                                        <td>@{{ item.vid }}</td>
                                        <td>@{{ item.must_check_date }}</td>
                                        <input type="hidden" v-bind:name="'arr_send_check['+ index +'][id]'"
                                            id="id" v-bind:value="item.id">
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="4">“
                                            {{ __('lang.no_list') . __('gps.send_check_signal') }} “</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-block btn-hide-send-check-siganl"
                                data-dismiss="modal">{{ __('lang.cancel') }}</button>
                            <button type="button"
                                class="btn btn-primary btn-block btn-update-status">{{ __('lang.save') }}</button>
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
        $(".btn-hide-send-check-siganl").on("click", function() {
            sendCarToCheckVue.removeAll();
            $('#modal-send-check-siganl').modal('hide');
        });

        let sendCarToCheckVue = new window.Vue({
            el: '#send-car-to-check',
            data: {
                arr_check: []
            },
            methods: {
                addByDefault: function(e) {
                    var _this = this;
                    var send_data = {};
                    if (e.id) {
                        send_data.id = e.id;
                        send_data.chassis_no = e.chassis_no;
                        send_data.license_plate = e.license_plate;
                        send_data.vid = e.vid;
                        send_data.must_check_date = e.must_check_date;

                        _this.arr_check.push(send_data);
                        $("#send-car-to-check").show();
                    }
                },
                removeAll: function() {
                    this.arr_check = [];
                },
            },
            props: ['title'],
        });

        $(".btn-update-status").on("click", function() {
            let storeUri = "{{ route('admin.gps-check-signal-jobs.send-check-job') }}";
            var formData = new FormData(document.querySelector('#save-form-check'));
            axios.post(storeUri, formData).then(response => {
                $('#modal-send-check-siganl').modal('hide');
                showLoading();
                if (response.data.success) {
                    hideLoading();
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
