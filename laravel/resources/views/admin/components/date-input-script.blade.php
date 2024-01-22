@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        jQuery(function() {
            Dashmix.helpers(['js-flatpickr', 'js-datepicker']);
        });
        Vue.component('input-date-vue', {
            template: '<div class="input-group"><input :name="name" class="form-control input-date" type="text" :value="formattedDate" @input="updateValue" style="background-color: white !important;" readonly/> <span class="input-group-text"><i class="far fa-calendar-check"></i></span></div>',
            props: {
                name: {
                    type: String,
                    default: '',
                },
                value: null,
                date_enable_time: false,
                options: {
                    type: Object,
                    default: () => {
                        return {};
                    },
                },
            },
            computed: {
                formattedDate() {
                    return this.value ? this.formatDate(this.value) : '';
                },
            },
            mounted() {
                let vm = this;
                let format = 'Y-m-d';
                if (vm.date_enable_time === true) {
                    format = 'Y-m-d H:i';
                }
                let input = $(this.$el).find('.input-date');
                let flatpickrInstance = flatpickr(input, {
                    ...this.options,
                    dateFormat: format,
                    enableTime: vm.date_enable_time,
                    time_24hr: vm.date_enable_time,
                    minDate: 'today',
                    onChange: function(selectedDates, dateStr) {
                        vm.$emit('input', dateStr);
                    },
                });
                $(input).css('background-color', 'none')
                if (this.value) {
                    flatpickrInstance.setDate(this.value);
                }
            },
            methods: {
                updateValue(event) {
                    event.preventDefault();
                },
                formatDate(date) {
                    return date;
                },
            },
        });
    </script>
@endpush
