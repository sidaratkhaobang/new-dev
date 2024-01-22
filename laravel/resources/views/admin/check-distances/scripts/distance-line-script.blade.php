@push('scripts')
    <script>
        function add() {
            addCheckDistanceVue.add();
        }

        Vue.component('select-code', {
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
                            width: '100%',
                            allowClear: true,
                            data: this.select2data
                        })
                        .on('select2:clear', function(e) {
                            vm.fetchOptions(null);
                        })
                        .on('change', function(e) {
                            vm.$emit('input', select.val());
                        })
                    // .on('select2:selecting', function(e) {
                    //     // if (e.params.args.data._resultId === undefined) {
                    //         // Fetch options when searching
                    //         // const searchValue = e.params.args.data.text;
                    //         vm.fetchOptions();
                    //     // }
                    // });
                    select.val(this.value).trigger('change');
                },
                async fetchOptions(selectedValue, searchValue = null) {
                    try {
                        const response = await axios.get(
                            "{{ route('admin.util.select2-repair.repair-name-list') }}", {
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

                        // Reinitialize the select2 plugin after fetching new options
                        let select = $(this.$el);
                        select.empty().select2('destroy');
                        this.setupSelect2();
                    } catch (error) {
                        console.error('Error fetching options:', error);
                    }
                }
            }
        });

        Vue.component('select-check', {
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
                    form_list: [],
                    select2data: [],
                    check_list: @if (isset($check_list))
                        @json($check_list)
                    @else
                        []
                    @endif ,
                }
            },
            mounted() {
                this.formatOptions();
                let vm = this
                let select = $(this.$el);
                select
                    .select2({
                        placeholder: 'Select',
                        // theme: 'bootstrap',
                        width: '100%',
                        allowClear: true,
                        data: this.select2data
                    })
                    .on('change', function() {
                        vm.$emit('input', select.val());
                    });
                select.val(this.value).trigger('change');
            },
            methods: {
                formatOptions() {
                    this.select2data.push({
                        id: '',
                        text: 'Select'
                    });
                    for (let item of this.check_list) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        });
                    }
                }
            },
            destroyed: function() {}
        })

        let addCheckDistanceVue = new window.Vue({
            el: '#check-distance',
            data() {
                return {
                    check_distances: @if (isset($check_distances))
                        @json($check_distances)
                    @else
                        []
                    @endif ,
                    form_list: [],
                    del_input_id: [],
                    del_input_sub_id: [],
                    amount: 0,
                    repair_list_data: @if (isset($repair_lists))
                        @json($repair_lists)
                    @else
                        []
                    @endif ,
                }
            },
            mounted: function() {
                $('#code_00').select2({
                    // theme: 'bootstrap',
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -'
                });
                $('#check_00').select2({
                    // theme: 'bootstrap',
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -'
                });
                $('.list').select2({
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -',
                    // theme: 'bootstrap',
                });
                $('.list').one('select2:open', function(e) {
                    $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                });
            },
            methods: {
                add() {
                    this.check_distances.push({
                        status_section: false,
                        distance: '',
                        month: '',
                        amount: '',
                        check_line: [],
                        id: null,
                    })
                },
                addSub(k) {
                    this.check_distances[k].check_line.push({
                        status_list: false,
                        code: '',
                        name: '',
                        check: null,
                        price: '',
                        remark: '',
                        id: null,
                    });
                    this.selectTwo(k);
                },
                async selectTwo(k) {
                    var self = this;
                    await self.$nextTick();
                    var index = self.check_distances[k].check_line.length;
                    index = index - 1;
                    $("#code_" + k + index).select2({
                        data: self.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    });
                    $("#code_" + k + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr(
                            'placeholder', 'ค้นหา...');

                    $("#check_" + k + index).select2({
                        data: self.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    });
                    $("#check_" + k + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr(
                            'placeholder', 'ค้นหา...');

                    $("#code_" + k + index).on('select2:select', function(e) {
                        var repair_data = self.repair_list_data.filter(obj => obj.id == e.params.data
                            .id);
                        var check_line = self.check_distances[k].check_line[index];
                        console.log(check_line.price);
                        repair_data.forEach((el) => {
                            check_line.price = el.price;
                        });
                    });
                },
                hide(k) {
                    if ($("#sub-section" + k).is(":hidden")) {
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-section" + k).show();
                    } else {
                        $("#sub-section" + k).hide();
                        $("hd").addClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-section" + k).hide();

                    }
                },
                setAmount(x) {
                    amount = 0;
                    amount = this.check_distances[x].check_line.length;
                    this.amount = (parseInt(amount) > 0) ? parseInt(amount) : '';
                    return this.amount;
                },
                remove(k) {
                    this.del_input_id.push(this.check_distances[k].id);
                    this.check_distances.splice(k, 1);
                },
                removeList(k, k2) {
                    this.del_input_sub_id.push(this.check_distances[k].check_line[k2].id);
                    this.check_distances[k].check_line.splice(k2, 1);
                    this.check_distances[k].check_line.forEach(function(item, index) {
                        $("#code_" + k + index).val(item.code).trigger('change')
                        $("#check_" + k + index).val(item.check).trigger('change')
                    });
                },
            }
        })
    </script>
@endpush
