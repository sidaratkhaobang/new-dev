@push('scripts')
    <script>
        let PaymentDetailVue = new Vue({
            el: '#payment_detail',
            data: {
                car_total: @if(isset($d?->purchase_order?->total)) {{$d?->purchase_order?->total ?? 0}} @else 0 @endif,
                down_payment_percent: @if(isset($d?->down_payment_percent)) {{$d?->down_payment_percent ?? 0}} @else 0 @endif,

                down_payment_percent_total: 0,

            },
            mounted: function () {
                if (this.down_payment_percent) {
                    let calculate = (parseFloat(this.car_total) * parseFloat(this.down_payment_percent)) / 100
                    this.down_payment_percent_total = calculate.toLocaleString()
                } else {
                    this.down_payment_percent_total = 0
                }
            },
            watch: {
                down_payment_percent: function (newVal, oldVal) {
                    if (newVal) {
                        let calculate = (parseFloat(this.car_total) * parseFloat(newVal)) / 100
                        this.down_payment_percent_total = calculate.toLocaleString()
                    } else {
                        this.down_payment_percent_total = 0
                    }
                },
            },
            methods: {}
        })


    </script>
@endpush
