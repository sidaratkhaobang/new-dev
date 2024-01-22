@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@push('scripts')
    <script>
        function addBillingStatusLine() {
            addCheckBillingStatusLineVue.add();
        }

        //status
        Vue.component('select-status', {
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
                    status_list: @if (isset($status_list))
                        @json($status_list)
                    @else
                        []
                    @endif ,
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
                    for (let item of this.status_list) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        })
                    }
                }
            },
            destroyed: function() {}
        })

        Vue.component('flatpickr', {
            template: '<input :name="name" class="form-control" type="text"  />',
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
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr) {
                        vm.$emit('input', dateStr);
                    },
                });

                if (this.value) {
                    flatpickrInstance.setDate(this.value);
                }
            }
        });

        let addCheckBillingStatusLineVue = new window.Vue({
            el: '#check-billing-status-line',
            data() {
                return {
                    form_list: [],
                    check_billing_status_line: @if (isset($check_billing_status_line))
                        @json($check_billing_status_line)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                }
            },
            mounted: function() {
                $('#status0').select2({
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
                add() {
                    this.check_billing_status_line.push({
                        sending_billing_date: '',
                        check_billing_date: '',
                        status: null,
                        detail: null,
                        id: null,
                    });
                    this.selectTwo();
                },
                async selectTwo() {
                    await this.$nextTick()
                    var index = this.check_billing_status_line.length;
                    index = index - 1;
                    $("#status" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#status" + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr('placeholder', 'ค้นหา...')
                },
                remove: function(k) {
                    this.del_input_id.push(this.check_billing_status_line[k].id);
                    this.check_billing_status_line.splice(k, 1);
                    this.check_billing_status_line.forEach(function(item, index) {
                        $("#status" + index).val(item.status).trigger('change');
                        $("#sending_billing_date" + index).val(item.sending_billing_date).trigger(
                            'change');
                        $("#check_billing_date" + index).val(item.check_billing_date).trigger('change');
                    });
                },
            }
        })
    </script>
@endpush
