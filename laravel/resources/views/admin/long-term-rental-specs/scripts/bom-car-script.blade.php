@push('scripts')
    <script>
        Vue.component('select-3', {
            template: '<select v-bind:name="name" class="form-control" ></select>',
            props: {
                name: '',
                options: {
                    Object
                },
                value: null,
                multiple: {
                    Boolean,
                    default: false

                }
            },
            data() {
                return {
                    select2data: [],
                    is_haves: @if (isset($is_haves))@json($is_haves)@else[]@endif ,
                }
            },
            mounted() {
                this.formatOptions()
                let vm = this
                let select = $(this.$el)
                select
                    .select2({
                        placeholder: '- กรุณาเลือก -',
                        width: '100%',
                        allowClear: true,
                        dropdownParent: $('#modal-bom-car'),
                        data: this.select2data,
                        
                    })
                    .on('change', function() {
                        vm.$emit('input', select.val())
                    })
                    .on('select2:open', function(e) {
                        $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                    });
                select.val(this.value).trigger('change')
            },
            methods: {
                formatOptions() {
                    this.select2data.push({
                        id: '',
                        text: 'Select'
                    })
                    for (let item of this.is_haves) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        })
                    }
                }
            },
            destroyed: function() {}
        })

        let bomCarVue = new Vue({
            el: '#bom-car-line',
            data: {
                bom_cars: [],
            },
            mounted: function() {
                $('#is_have0').select2({
                    data: this.bom_cars,
                    placeholder: '- กรุณาเลือก -'
                })
            },
            methods: {
                display: function() {
                    $("#bom-car-line").show();
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
                        bom_line.remark = '';

                        _this.bom_cars.push(bom_line);
                        $("#bom-car-line").show();
                        this.selectHave(index);

                        $("#bom_id").val(bom_id).change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                // async selectHave(index) {
                //     await this.$nextTick()
                //     $("#is_have" + index).select2({
                //         data: this.bom_cars,
                //         placeholder: '- กรุณาเลือก -'
                //     })
                // },
                removeAll: function() {
                    this.bom_cars = [];
                },
                getBomCar: function() {
                    return this.bom_cars;
                },
                hide: function() {
                    var _this = this;
                    _this.removeAll();
                    $("#bom_id").val('').change();
                    $('#modal-bom-car').modal('hide');
                    window.location.reload();
                },
            },
            props: ['title'],
        });
        bomCarVue.display();

        function saveBomCar() {
            var bom_cars = bomCarVue.getBomCar();
            var lt_rental_id = document.getElementById('id').value;
            if (bom_cars) {
                var data = {
                    bom_cars: bom_cars,
                    lt_rental_id: lt_rental_id,
                };
                var updateUri = "{{ route('admin.long-term-rental.specs.store-bom-car') }}";
                axios.post(updateUri, data).then(response => {
                    if (response.data.success) {
                        bomCarVue.hide();
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
                    warningAlert('{{ __('lang.required_field_inform') }}');
                });
            } else {
                warningAlert('{{ __('lang.required_field_inform') }}');
            }
        }

        function addBomCarByDefault(e, index, bom_id) {
            bomCarVue.addByDefault(e, index, bom_id);
        }

        function removeAllBomCar() {
            bomCarVue.removeAll();
        }

        $("#bom_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.long-term-rental.specs.default-bom-car') }}", {
                params: {
                    bom_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    removeAllBomCar();
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e, index) => {
                            addBomCarByDefault(e, index, response.data.bom_id);
                        });
                    }
                }
            });
        });
    </script>
@endpush
