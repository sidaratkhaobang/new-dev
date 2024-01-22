@push('scripts')
    <script>
        function addManualLine() {
            addTorLineVue.addManual();
        }

        function addBomCar() {
            document.getElementById("bom_car_show").style.display = "block"
            document.getElementById("manual_car_show").style.display = "none"
            addTorLineVue.removeAll();
            $("#bom_id").val('').change();
            $("#modal-tor-line").modal("show");
        }

        function addManualCar() {
            document.getElementById("bom_car_show").style.display = "none"
            document.getElementById("manual_car_show").style.display = "block"
            addTorLineVue.removeAll();
            $("#bom_id").val('').change();
            $("#modal-tor-line").modal("show");
        }

        function editCarTor(tor_id) {
            axios.get("{{ route('admin.long-term-rental.specs.default-tor-line') }}", {
                params: {
                    tor_id: tor_id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        addTorLineVue.removeAll();
                        response.data.data.tor_line.forEach((e, index) => {
                            addTorLineVue.editByDefault(e, index, response.data.data.tor_remark, response
                                .data.tor_id);
                        });
                    }
                }
            });
        }

        $("#bom_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.long-term-rental.specs.default-bom-car') }}", {
                params: {
                    bom_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    addTorLineVue.removeAll();
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e, index) => {
                            addTorLineVue.addByDefault(e, index, response.data.bom_id);
                        });
                    }
                }
            });
        });

        Vue.component('select-car-class', {
            template: '<select v-bind:name="name" class="form-control"></select>',
            props: {
                name: '',
                value: null,
            },
            data() {
                return {
                    select2data: [],
                }
            },
            async mounted() {
                showLoading();
                await this.fetchOptions();
                await this.setupSelect2();
                await hideLoading();
            },
            watch: {
                value: {
                    immediate: true,
                    handler(newValue) {
                        this.fetchOptions(newValue);
                    }
                }
            },
            methods: {
                async setupSelect2() {
                    let vm = this;
                    let select = $(this.$el);
                    await select
                        .select2({
                            placeholder: "{{ __('lang.select_option') }}",
                            allowClear: true,
                            data: this.select2data,
                            dropdownParent: $("#modal-tor-line"),
                        })
                        .on('select2:clear', function(e) {
                            vm.fetchOptions(null);
                        })
                        .on('change', function(e) {
                            vm.$emit('input', select.val());
                        })
                    select.val(this.value).trigger('change');
                },
                async fetchOptions(selectedValue, searchValue = null) {
                    try {
                        const response = await axios.get(
                            "{{ route('admin.util.select2-rental.car-class') }}", {
                                params: {
                                    id: this.value,
                                    s: searchValue
                                }
                            });
                        const options = response.data;
                        this.select2data = options.map(option => ({
                            id: option.id,
                            text: option.text
                        }));

                        this.setupSelect2();
                    } catch (error) {
                        console.error('Error fetching options:', error);
                    }
                }
            }
        });

        Vue.component('select-car-color', {
            template: '<select v-bind:name="name" class="form-control"></select>',
            props: {
                name: '',
                value: null,
                multiple: {
                    type: Boolean,
                    default: false
                }
            },
            data() {
                return {
                    select2data: [],
                }
            },
            async mounted() {
                showLoading();
                await this.fetchOptions();
                await this.setupSelect2();
                await hideLoading();
            },
            watch: {
                value: {
                    immediate: true,
                    handler(newValue) {
                        this.fetchOptions(newValue);
                    }
                }
            },
            methods: {
                async setupSelect2() {
                    let vm = this;
                    let select = $(this.$el);
                    await select
                        .select2({
                            placeholder: "{{ __('lang.select_option') }}",
                            allowClear: true,
                            data: this.select2data,
                            dropdownParent: $("#modal-tor-line"),
                        })
                        .on('select2:clear', function(e) {
                            vm.fetchOptions(null);
                        })
                        .on('change', function(e) {
                            vm.$emit('input', select.val());
                        })
                    select.val(this.value).trigger('change');
                },
                async fetchOptions(selectedValue, searchValue = null) {
                    try {
                        const response = await axios.get(
                            "{{ route('admin.util.select2.car-colors') }}", {
                                params: {
                                    id: this.value,
                                    s: searchValue
                                }
                            });
                        const options = response.data;
                        this.select2data = options.map(option => ({
                            id: option.id,
                            text: option.text
                        }));

                        this.setupSelect2();
                    } catch (error) {
                        console.error('Error fetching options:', error);
                    }
                }
            }
        });

        let addTorLineVue = new Vue({
            el: '#tor-line',
            data: {
                tor_lines: [],
                tor_id: null,
                form_list: [],
            },
            mounted: function() {
                $('#car_class_0').select2({
                    // theme: 'bootstrap',
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -'
                });
                $('#car_color_0').select2({
                    // theme: 'bootstrap',
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -'
                });
                $('.list').select2({
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -'
                });
                $('.list').one('select2:open', function(e) {
                    $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                });
            },
            methods: {
                display: function() {
                    $("#tor-line").show();
                },
                addByDefault: function(e, index, bom_id) {
                    var _this = this;
                    var bom_line = {};
                    if (e.bom_line_id) {
                        bom_line.bom_line_id = e.bom_line_id;
                        bom_line.car_name = e.car_name;
                        bom_line.car_class_id = e.car_class_id;
                        bom_line.car_color_id = e.car_color_id;
                        bom_line.color_name = e.color_name;
                        bom_line.amount = e.amount;
                        bom_line.is_have = 0;
                        bom_line.remark = e.remark;
                        bom_line.tor_line_id = null;

                        _this.tor_lines.push(bom_line);
                        $("#tor-line").show();

                        $("#bom_id").val(bom_id).change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                    this.selectTwo();
                },
                addManual() {
                    this.tor_lines.push({
                        car_class_id: '',
                        car_color_id: '',
                        amount: '',
                        is_have: '',
                        remark: '',
                        tor_line_id: null,
                    })
                    this.selectTwo();
                },
                editByDefault: function(e, index, tor_remark, tor_id) {
                    var _this = this;
                    var tor_line = {};
                    if (e.tor_line_id) {
                        tor_line.tor_line_id = e.tor_line_id;
                        tor_line.car_class_id = e.car_class_id;
                        tor_line.car_color_id = e.car_color_id;
                        tor_line.amount = e.amount;
                        tor_line.is_have = e.have_accessories;
                        tor_line.remark = e.remark;
                        $("#remark_tor").val(tor_remark);
                        $("#tor_id").val(tor_id);

                        _this.tor_lines.push(tor_line);
                        $("#tor-line-modal-label").html('แก้ไข/เพิ่มรถ');
                        $("#manual-car-body-modal-label").html('แก้ไข/เพิ่มรายการรถ');
                        $("#modal-tor-line").modal("show");
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                    this.selectTwo();
                },
                async selectTwo() {
                    var self = this;
                    await self.$nextTick();
                    var index = self.tor_lines.length;
                    index = index - 1;
                    $("#car_class_" + index).select2({
                        data: self.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    });
                    $("#car_class_" + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr(
                            'placeholder', 'ค้นหา...');

                    $("#car_color_" + index).select2({
                        data: self.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    });
                    $("#car_color_" + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr(
                            'placeholder', 'ค้นหา...');
                },
                removeAll: function() {
                    this.tor_lines = [];
                },
                getTorLine: function() {
                    return this.tor_lines;
                },
                setTorId: function(id) {
                    this.tor_id = id;
                },
                getTorId: function(id) {
                    return this.tor_id;
                },
                hide: function() {
                    var _this = this;
                    _this.removeAll();
                    $("#bom_id").val('').change();
                    $('#modal-tor-line').modal('hide');
                    window.location.reload();
                },
            },
            props: ['title'],
        });
        addTorLineVue.display();

        function saveTorLine() {
            var tor_lines = addTorLineVue.getTorLine();
            var lt_rental_id = document.getElementById('id').value;
            var remark_tor = document.getElementById('remark_tor').value;
            var tor_id = document.getElementById('tor_id');
            if (tor_id) {
                tor_id = document.getElementById('tor_id').value;
            }
            if (tor_lines) {
                var data = {
                    tor_lines: tor_lines,
                    lt_rental_id: lt_rental_id,
                    remark_tor: remark_tor,
                    tor_id: tor_id,
                };
                var updateUri = "{{ route('admin.long-term-rental.specs.store-bom-car') }}";
                axios.post(updateUri, data).then(response => {
                    if (response.data.success) {
                        addTorLineVue.hide();
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            window.location.reload();
                        })
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
                });
            } else {
                warningAlert('{{ __('lang.required_field_inform') }}');
            }
        }
    </script>
@endpush
