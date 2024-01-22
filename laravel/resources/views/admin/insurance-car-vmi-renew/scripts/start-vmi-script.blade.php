@push('scripts')
    <script>
        $(document).ready(function() {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function() {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function(e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });

        async function opencreateVMIModal() {
            var checked_ids = [];
            $('input[name="row_checkbox"]:checked').each(function() {
                checked_ids.push($(this).val());
            });
            vmiListVue.add(checked_ids);
             $('#_term_start_date').val(null);
             $('#_term_end_date').val(null);
            $('#modal-start-vmi').modal('show');
        }

        let vmiListVue = new Vue({
            el: '#modal-start-vmi',
            data: {
                vmi_list: @if (isset($list)) @json($list) @else [] @endif,
                edit_index: null,
                selected_list: [],
                term_start_date: null,
                term_end_date: null,
            },
            methods: {
                display: function(id,index) {
                },
                add: function(_array) {
                    this.selected_list = [];
                    _array.forEach(element => {
                        const object = this.vmi_list.data.find(o => o.id === element);
                        this.selected_list.push(object);
                    });
                },
                remove: function(index) {
                    this.selected_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                startCMI: function() {
                    if (this.validate()) {
                        this.makeInProcessVMIs();
                    }
                },
                validate: function() {
                    var _term_start_date = $('#_term_start_date').val();
                    var _term_end_date = $('#_term_end_date').val();
                    if (!_term_start_date || !_term_end_date) {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                        return false;
                    }
                    if (this.selected_list.length <= 0) {
                        warningAlert("{{ __('lang.field_required') . __('lang.no_list') }}");
                        return false;
                    }
                    for (let i = 0; i < this.selected_list.length; i++) {
                        if (!this.selected_list[i].sum_insured_car ||
                            !this.selected_list[i].sum_insured_accessory ||
                            !this.selected_list[i].insurer_id ||
                            !this.selected_list[i].beneficiary_id ||
                            !this.selected_list[i].insurance_type ||
                            !this.selected_list[i].insurance_package_id ||
                            !this.selected_list[i].car_class_insurance_id ||
                            !this.selected_list[i].type_vmi ||
                            !this.selected_list[i].type_cmi
                        ) {
                            warningAlert("{{ __('vmi_cars.data_required_make_vmi') }}");
                            return false;
                        }

                        if (this.selected_list[i].status != '{{ InsuranceStatusEnum::PENDING }}') {
                            warningAlert("{{ __('vmi_cars.status_fail_make_vmi') }}");
                            return false;
                        }
                    }
                    return true;
                },
                makeInProcessVMIs: async function() {
                    var _term_start_date = $('#_term_start_date').val();
                    var _term_end_date = $('#_term_end_date').val();
                    var data = {
                        vmi_list: this.selected_list,
                        term_start_date: _term_start_date,
                        term_end_date: _term_end_date,
                    };
                    var updateUri = "{{ route('admin.insurance-vmi-renew.make-in-process-vmis') }}";
                    await axios.post(updateUri, data).then(response => {
                        if (response.data.success) {
                            mySwal.fire({
                                title: "{{ __('lang.store_success_title') }}",
                                text: "{{ __('lang.store_success_message') }}",
                                icon: 'success',
                                confirmButtonText: "{{ __('lang.ok') }}"
                            }).then(value => {
                                $('#modal-start-vmi').modal('hide');
                                window.location.reload();
                            });
                        } else {
                            errorAlert(response.data.message);
                        }
                    }).catch(error => {
                        errorAlert(error.response.data.message);
                    });
                }
            },
            props: ['title'],
        });
    </script>
@endpush
