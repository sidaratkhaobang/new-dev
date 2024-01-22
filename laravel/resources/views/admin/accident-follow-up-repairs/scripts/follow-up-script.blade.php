@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script> --}}
    <script>
        function add2() {
            carVue2.add();
            carVue2.selectTwo();
        }

        function addSub2() {
            carVue2.addSub();
        }

        function remove2() {
            carVue2.remove();
        }

        function removeList2(k) {
            carVue2.removeList(k);
        }

        Vue.component('flatpickr', {
            template: '<input :name="name" class="form-control" type="text" style="background-color: white !important;" />',
            props: {
                name: {
                    type: String,
                    default: '',
                },
                value: null,
                options: {
                    type: Object,
                    default: () => {
                        return {}
                    }
                }
            },
            mounted() {
                let vm = this;
                let currentYear = new Date().getFullYear();
                let flatpickrInstance = flatpickr(this.$el, {
                    ...this.options,
                    dateFormat: "d/m/Y",
                    onChange: function(selectedDates, dateStr) {
                        vm.$emit('input', dateStr);
                    },
                    // locale: "th"
                });

                if (this.value) {
                    flatpickrInstance.setDate(this.value);
                }
            }
        });

        Vue.component('select-2-repair', {
            template: '<select v-bind:name="name" class="form-control"></select>',
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
                    repair_status_list: @if (isset($repair_status_list))
                        @json($repair_status_list)
                    @else
                        []
                    @endif ,
                    select2data: [],

                    // wound_list: @if (isset($wound_list))
                    //     @json($wound_list)
                    // @else
                    //     []
                    // @endif ,


                }
            },
            mounted() {
                this.formatOptions()
                let vm = this
                let select = $(this.$el)
                select
                    .select2({
                        placeholder: 'Select',
                        theme: 'bootstrap',
                        width: '100%',
                        allowClear: true,
                        data: this.select2data
                    })
                    .on('change', function() {
                        vm.$emit('input', select.val())
                    })
                select.val(this.value).trigger('change')

            },
            methods: {
                formatOptions() {
                    this.select2data.push({
                        id: '',
                        text: 'Select'
                    })
                    for (let item of this.repair_status_list) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        })
                    }
                }
            },
            destroyed: function() {},
        })


        let carVue2 = new Vue({
            el: '#app2',
            data() {
                return {
                    inputs: @if (isset($repair_list))
                        @json($repair_list)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                }
            },
            mounted: function() {

                $('#follow_up_status0').select2({
                    // data: this.inputs,
                    placeholder: '- กรุณาเลือก -',
                })

                $('.list_in').select2({
                    // data: this.inputs,
                    placeholder: '- กรุณาเลือก -',
                    allowClear: true,
                })

                $('.list').one('select2:open', function(e) {
                    $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                });

                // $('.form-control:disabled, .form-control[readonly]').attr("readonly", false);
                $('.flatpickr-input').css("cursor", 'pointer');

            },


            methods: {
                add() {
                    this.inputs.push({
                        repair: '',
                        recieve_date: '',
                        spare_part_cost: '',
                        spare_part_discount: '',
                    })
                },

                async selectTwo() {
                    await this.$nextTick()
                    var index = this.inputs.length;
                    index = index - 1;

                    $("#follow_up_status" + index).select2({
                        data: this.repair_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#follow_up_status" + index).data('select2').$dropdown.find(
                        ':input.select2-search__field').attr('placeholder', 'ค้นหา...')

                    $('.form-control:disabled, .form-control[readonly]').attr("readonly", false);
                    $('.flatpickr-input').css("cursor", 'pointer');
                },
                remove(k) {
                    this.del_input_id.push(this.inputs[k].id);
                    this.inputs.splice(k, 1);
                    this.inputs.forEach(function(item, index) {
                        $("#received_data_date" + index).val(item.received_data_date).trigger('change')
                        $("#follow_up_status" + index).val(item.follow_up_status).trigger('change')

                    });

                },
               

            }
        })

        function openModal() {
            $("#modal-confirm").modal("show");
        }
    </script>
@endpush
