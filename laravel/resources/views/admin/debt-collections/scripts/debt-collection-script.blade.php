@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@push('scripts')
    <script>
        function addCollectionLine() {
            addDebtCollectionLineVue.add();
        }

        //channel
        Vue.component('select-channel', {
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
                    channel_list: @if (isset($channel_list))
                        @json($channel_list)
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
                    for (let item of this.channel_list) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        })
                    }
                }
            },
            destroyed: function() {}
        })

        //notification_date
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

        let addDebtCollectionLineVue = new window.Vue({
            el: '#debt-collection-line',
            data() {
                return {
                    form_list: @if (isset($config))
                        []
                    @else
                        []
                    @endif ,
                    debt_collection_line: @if (isset($debt_collection_line))
                        @json($debt_collection_line)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                }
            },
            mounted: function() {
                $('#channel0').select2({
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -',
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
                    this.debt_collection_line.push({
                        channel: null,
                        notification_date: '',
                        detail: null,
                        id: null,
                    });
                    this.selectTwo();
                },
                async selectTwo() {
                    await this.$nextTick()
                    var index = this.debt_collection_line.length;
                    index = index - 1;
                    $("#channel" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                    $("#channel" + index).data('select2').$dropdown.find(':input.select2-search__field')
                        .attr(
                            'placeholder', 'ค้นหา...')
                },
                remove: function(k) {
                    this.del_input_id.push(this.debt_collection_line[k].id);
                    this.debt_collection_line.splice(k, 1);
                    this.debt_collection_line.forEach(function(item, index) {
                        $("#channel" + index).val(item.channel).trigger('change');
                        $("#notification_date" + index).val(item.notification_date).trigger('change');
                    });
                },
            }
        })
    </script>
@endpush
