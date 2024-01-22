<div class="modal fade" id="modal-send-remove-siganl" tabindex="-1" aria-labelledby="modal-send-remove-siganl"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="send-remove-siganl-modal-label">{{ __('gps.send_remove_gps') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-1">
                <form id="save-form-remove">
                    <div id="send-car-to-remove" v-cloak data-detail-uri="" data-title="">
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <th>{{ __('gps.chassis_no') }}</th>
                                    <th>{{ __('gps.license_plate') }}</th>
                                    <th>{{ __('gps.vid') }}</th>
                                </thead>
                                <tbody v-if="arr_remove.length > 0">
                                    <tr v-for="(item, index) in arr_remove">
                                        <td>@{{ item.chassis_no }}</td>
                                        <td>@{{ item.license_plate }}</td>
                                        <td>@{{ item.vid }}</td>
                                        <input type="hidden" v-bind:name="'arr_send_remove['+ index +'][id]'"
                                            id="id" v-bind:value="item.id">
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr class="table-empty">
                                        <td class="text-center" colspan="4">“
                                            {{ __('lang.no_list') . __('gps.send_remove_gps') }} “</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-block btn-hide-send-remove-siganl"
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
        $(".btn-hide-send-remove-siganl").on("click", function() {
            sendCarToRemoveVue.removeAll();
            $('#modal-send-remove-siganl').modal('hide');
        });

        let sendCarToRemoveVue = new window.Vue({
            el: '#send-car-to-remove',
            data: {
                arr_remove: []
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

                        _this.arr_remove.push(send_data);
                        $("#send-car-to-remove").show();
                    }
                },
                removeAll: function() {
                    this.arr_remove = [];
                },
            },
            props: ['title'],
        });

        $(".btn-update-status").on("click", function() {
            let storeUri = "{{ route('admin.gps-remove-stop-signal-jobs.send-remove-job') }}";
            var formData = new FormData(document.querySelector('#save-form-remove'));
            axios.post(storeUri, formData).then(response => {
                $('#modal-send-remove-siganl').modal('hide');
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
