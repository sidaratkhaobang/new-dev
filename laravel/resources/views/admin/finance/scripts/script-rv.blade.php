@push('scripts')
    <script>
        let VrDetailVue = new Vue({
            el: '#vr_detail',
            data: {
                car_total: @if(isset($d?->purchase_order?->total)) {{$d?->purchase_order?->total}} @else 0 @endif,
                car_accessory_total: @if(isset($total_accessory_price)) {{$total_accessory_price}} @else 0 @endif,
                rv_car_percent: @if(isset($d?->rv_car_percent)) {{$d?->rv_car_percent}} @else null @endif,
                rv_car_accessory_percent: @if(isset($d?->rv_accessory_percent)) {{$d?->rv_accessory_percent}} @else null @endif,
                rv_car_summary_total: 0,
                rv_car_accessory_summary_total: 0,
                rv_total: 0,

            },
            mounted: function () {
                if (this.rv_car_percent) {
                    let calculate = (parseFloat(this.car_total) * parseFloat(this.rv_car_percent)) / 100
                    this.rv_car_summary_total = calculate.toLocaleString()
                } else {
                    this.rv_car_summary_total = 0
                }
                if (this.rv_car_accessory_percent) {
                    let calculate = (parseFloat(this.car_total) * parseFloat(this.rv_car_accessory_percent)) / 100
                    this.rv_car_accessory_summary_total = calculate.toLocaleString()
                } else {
                    this.rv_car_accessory_summary_total = 0
                }
                this.rv_summary()
            },
            watch: {
                rv_car_percent: function (newVal, oldVal) {
                    if (newVal) {
                        let calculate = (parseFloat(this.car_total) * parseFloat(newVal)) / 100
                        this.rv_car_summary_total = calculate.toLocaleString()
                    } else {
                        this.rv_car_summary_total = 0
                    }
                    this.rv_summary()
                },
                rv_car_accessory_percent: function (newVal, oldVal) {
                    if (newVal) {
                        let calculate = (parseFloat(this.car_total) * parseFloat(newVal)) / 100
                        this.rv_car_accessory_summary_total = calculate.toLocaleString()
                    } else {
                        this.rv_car_accessory_summary_total = 0
                    }
                    this.rv_summary()
                }
            },
            methods: {
                rv_summary: function () {
                    if(!this.rv_car_summary_total){
                        var rv_car_summary_total = '0';
                    }else{
                        var rv_car_summary_total = this.rv_car_summary_total;
                    }
                    if(!this.rv_car_accessory_summary_total){
                        var rv_car_accessory_summary_total = '0';
                    }else{
                        var rv_car_accessory_summary_total = this.rv_car_accessory_summary_total;
                    }
                    let calculate = parseFloat(rv_car_summary_total.replace(/,/g, '')) + parseFloat(rv_car_accessory_summary_total.replace(/,/g, ''))
                    this.rv_total = calculate.toLocaleString()

                }
            }
        })


    </script>
@endpush
