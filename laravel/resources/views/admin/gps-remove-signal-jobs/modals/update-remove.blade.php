<div class="modal fade" id="modal-update-remove" tabindex="-1" aria-labelledby="modal-update-remove" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update-remove-modal-label">{{ __('gps.send_remove_gps') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="save-form-remove">
                    <div id="update-remove" v-cloak data-detail-uri="" data-title="">
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
                                        <input type="hidden" v-bind:name="'arr_update_remove['+ index +'][id]'"
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
                        <div class="row push mb-4">
                            <div class="col-sm-4">
                                <x-forms.select-option id="remove_status" :value="null" :list="$remove_status_list"
                                    :label="__('gps.remove_status')" :optionals="['required' => true]" />
                            </div>
                            <div class="col-sm-4">
                                <x-forms.date-input id="remove_date" :value="null" :label="__('gps.remove_date')" />
                            </div>
                            <div class="col-sm-4">
                                <x-forms.input-new-line id="remove_remark" :value="null" :label="__('gps.remark')" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-block btn-hide-update-remove"
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
        $(".btn-hide-update-remove").on("click", function() {
            updateRemoveVue.removeAll();
            $('#modal-update-remove').modal('hide');
        });

        let updateRemoveVue = new window.Vue({
            el: '#update-remove',
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
                        $("#update-remove").show();
                    }
                },
                removeAll: function() {
                    this.arr_remove = [];
                    $("#remove_status").val('').change();
                    $("#remove_date").val('');
                    $("#remove_remark").val('');
                },
            },
            props: ['title'],
        });

        $(".btn-update-status").on("click", function() {
            let storeUri = "{{ route('admin.gps-remove-signal-jobs.update-remove-job') }}";
            var formData = new FormData(document.querySelector('#save-form-remove'));
            axios.post(storeUri, formData).then(response => {
                $('#modal-update-remove').modal('hide');
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
