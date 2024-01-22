@push('scripts')
    <script>
        $(document).ready(function () {
            // $('[id^=option]').each(function () {
            //
            //     id = $(this).attr('data-index')
            //     id = parseInt(id)
            //     carVue2.selectTwo(id);
            // })
            carVue2.car_data.forEach(function (v_car, k_car) {

                v_car.product_additionals.forEach(function (v_product, k_product) {
                    carVue2.selectTwo(k_car,k_product)
                    console.log('wow',k_car,k_product)
                })
            })
        })

        function add2(index) {
            carVue2.add(index);
            carVue2.selectTwo();
        }

        /* Vue.component('select-2', {
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
                    form_list: @if (isset($product_list))
                        @json($product_list)
                        @else
                    []
                    @endif ,
                    select2data: [],

                }
            },
            mounted() {
                this.formatOptions();
                let vm = this;
                let select = $(this.$el);

                select
                    .select2({
                        placeholder: 'Select',
                        theme: 'bootstrap',
                        width: '100%',
                        allowClear: true,
                        data: this.select2data
                    })
                    .on('change', function () {
                        vm.$emit('input', select.val()); // Emit the 'input' event
                        vm.$emit('change', select.val()); // Emit the 'change' event
                    });

                select.val(this.value).trigger('change');
            },
            methods: {
                formatOptions() {
                    this.select2data.push({
                        id: '',
                        text: 'Select'
                    })
                    for (let item of this.form_list) {
                        this.select2data.push({
                            id: item.id,
                            text: item.name
                        })
                    }
                }
            },
            destroyed: function () {
            }
        }) */
        
    </script>
@endpush
