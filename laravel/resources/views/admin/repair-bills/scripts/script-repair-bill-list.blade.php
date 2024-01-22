@push('scripts')
    <script>
        let VrDetailVue = new Vue({
            el: '#repair_bill_list',
            data: {
                repair_bill_list: @if(isset($bill_slip_line_data) && !empty($bill_slip_line_data))@json($bill_slip_line_data) @else [] @endif,
            },
            mounted: function () {
            },
            watch: {},
            methods: {
                addRepairData: function () {
                    var obj = {
                        worksheet_no: null,
                        total_document: null,
                        repair_bill_price: null,
                        remark: null
                    };
                    this.repair_bill_list.push(obj);
                    // this.repair_bill_list = .push([])
                },
                remove: function (index) {
                    index = index - 1
                    this.repair_bill_list.splice(index, 1);
                }
            }
        })


    </script>
@endpush
