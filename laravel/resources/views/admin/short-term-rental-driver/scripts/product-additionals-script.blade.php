@push('scripts')
<script>
    $(document).ready(function () {
        //addInputsArray();
    })
    let carVue2 = new Vue({
        el: '#car-options',
        data() {
            return {
                del_input_id: [],
                car_data: @json($cars) ,
                product_additional_price_list: @if (isset($product_additional_price_list)) @json($product_additional_price_list) @else [] @endif,
            }
        },
        mounted: function () {
            //
        },
        methods: {
            add(index) {
                let data = {
                    id: null,
                    product_additional_id: null,
                    name: null,
                    amount: 0,
                    price_item: null,
                    price: 0,
                    is_from_product: 0,
                }
                this.car_data[index].product_additionals.push(data);
            },
            handleSelectChange(key, keyselect, value) {
                console.log(key, keyselect, value);
                var price = this.product_additional_price_list[value];
                this.car_data[key].product_additionals[keyselect].price = price;
            },
            getTotalProductAdditionals(key, keyselect) {
                let price_item = parseFloat(this.car_data[key].product_additionals[keyselect].price_item)
                let amount = parseFloat(this.car_data[key].product_additionals[keyselect].amount)
                console.log(price_item, amount)
                if (price_item && amount) {
                    let amountPrice = price_item * amount
                    this.car_data[key].product_additionals[keyselect].price = amountPrice.toFixed(2);
                }
            },
            setTotal(key, keyselect, value) {
                console.log('setToal', key, keyselect, value);
                let car_data = [...this.car_data];
                let product_additional = car_data[key].product_additionals[keyselect];
                product_additional.amount = value;
                product_additional.subtotal = (value * parseFloat(product_additional.price)).toFixed(2);
                car_data[key].product_additionals[keyselect] = product_additional;
                this.car_data = car_data;
            },
            remove(car_key, product_key) {
                this.del_input_id.push(this.car_data[car_key].product_additionals[product_key].rental_line_id);
                this.car_data[car_key].product_additionals.splice(product_key, 1);
            },
            number_format(number) {
                return number_format(number);
            },
        }
    })
</script>
@endpush
