@push('scripts')
    <script>

        function addRepairData() {
            addModalExportExcelVue.addRepairData();
        }

        function toggleModalRepairData() {
            addModalExportExcelVue.toggleModalRepairData();
        }

        function getOnlyNumber(string, type_float = true) {
            if (!string) {
                return 0;
            }
            let toString = string.toString();
            let onlyNumbers = toString.replace(/[^0-9.]|(\.(?![0-9]{1,2}))|\.(?=[0-9]*\.\d{3,})/g, '');
            if (type_float === true) {
                onlyNumbers = parseFloat(onlyNumbers)
            }
            return onlyNumbers;
        }

        let addModalExportExcelVue = new Vue({
            el: '#repair-list',
            data: {
                repair_list_data: @if(isset($repair_order_line)) @json($repair_order_line) @else [] @endif,
                summary_add_dept: 0,
                summary_reduce_debt: 0,
                summary_total_price: 0,
                discount_extra: @if(isset($d?->discount)) "{{$d?->discount}}" @else 0 @endif,
                vat: "{{$d?->percent_vat}}",
                vat_price: "{{$d?->vat}}",
                total_repair_price: 0,
                total_discount: 0,
            },
            mounted: function () {
                this.sumDebt()
                this.vat_price = ((this.summary_total_price - this.discount_extra) * this.vat) / 100
                this.total_repair_price = this.summary_total_price + this.vat_price
            },
            watch: {
                discount_extra: function (newVal, oldVal) {
                    discount_extra = getOnlyNumber(newVal)
                    this.vat_price = ((this.summary_total_price - discount_extra) * this.vat) / 100
                    this.total_repair_price = this.summary_total_price + this.vat_price
                },
                vat: function (newVal, oldVal) {
                    vat_price = getOnlyNumber(newVal)
                    this.vat_price = ((this.summary_total_price - this.discount_extra) * vat_price) / 100
                },
                repair_list_data: {
                    handler(newVal, oldVal) {
                        newVal.forEach(item => {

                            this.$watch(() => item.discount, (newDiscount, oldDiscount) => {

                                item.total_discount = this.calDiscount(item);
                                item.total_repair_price = this.calTotalRepairPrice(item);
                                console.log(this.calDiscount(item))
                            });

                            this.$watch(() => item.price_total, (newPriceTotal, oldPriceTotal) => {
                                item.total_discount = this.calDiscount(item);
                                item.total_repair_price = this.calTotalRepairPrice(item);
                            });
                            this.$watch(() => item.add_debt, (newPriceTotal, oldPriceTotal) => {
                                item.total_repair_price = this.calTotalRepairPrice(item);
                                this.sumDebt()
                            });
                            this.$watch(() => item.reduce_debt, (newPriceTotal, oldPriceTotal) => {
                                item.total_repair_price = this.calTotalRepairPrice(item);
                                this.sumDebt()
                            });
                        });
                    },
                    deep: true,
                },
            },
            methods: {
                toggleModalRepairData: function () {
                    $('#modal-repair-list').modal('toggle');
                    this.clearModalData();
                },
                addRepairData: function () {
                    let repair_list_name = $('#modal_repair_list_name').text();
                    let price_total = $('#modal_price_total').val();
                    let amount = $('#modal_amount').val();
                    let discount = $('#modal_discount').val();
                    let add_debt = $('#modal_add_debt').val();
                    let reduce_debt = $('#modal_reduce_debt').val();
                    let repair_list_id = $('#modal_repair_list_name').val();
                    let repair_data = {
                        repair_list_name: repair_list_name,
                        price_total: price_total,
                        amount: amount,
                        discount: discount,
                        add_debt: add_debt,
                        reduce_debt: reduce_debt,
                        total_discount: 0,
                        total_repair_price: 0,
                        repair_list_id: repair_list_id,
                    }
                    repair_data.total_discount = this.calDiscount(repair_data);
                    repair_data.total_repair_price = this.calDiscount(repair_data);
                    this.repair_list_data.push(repair_data)
                    this.sumDebt()
                    this.toggleModalRepairData()
                },
                clearModalData: function () {
                    $('#modal_repair_list_name').val(null);
                    $('#modal_price_total').val(null);
                    $('#modal_amount').val(null);
                    $('#modal_discount').val(null);
                    $('#modal_add_debt').val(null);
                    $('#modal_reduce_debt').val(null);
                },
                calDiscount: function (item) {
                    let price = getOnlyNumber(item.price_total) ?? 0;
                    let amount = getOnlyNumber(item.amount) ?? 0;
                    let discount = getOnlyNumber(item.discount) ?? 0;
                    console.log(price, amount, discount)
                    let total_discount = '0';
                    if (price && discount && amount) {
                        total_discount = (price * amount * discount) / 100;
                    }
                    if (total_discount == 'NaN') {
                        total_discount = 0
                    }
                    return total_discount.toString();
                },
                calTotalRepairPrice: function (item) {
                    let price = getOnlyNumber(item.price_total) ?? 0;
                    let amount = getOnlyNumber(item.amount) ?? 0;
                    let total_discount = getOnlyNumber(item.total_discount) ?? 0;
                    let add_debt = getOnlyNumber(item.add_debt) ?? 0;
                    let reduce_debt = getOnlyNumber(item.reduce_debt) ?? 0;
                    let total_repair_price = 0;
                    if (price) {
                        total_repair_price = (price * amount) - (total_discount + add_debt - reduce_debt);
                    }
                    if (total_repair_price == 'NaN') {
                        total_repair_price = 0
                    }
                    return total_repair_price;
                },
                sumDebt: function () {
                    var total_add_dept = 0;
                    var total_reduce_debt = 0;
                    var total_repair_price = 0;
                    var total_discount = 0;
                    if (this.repair_list_data) {
                        this.repair_list_data.forEach(function (value, index) {
                            var add_debt = getOnlyNumber(value.add_debt)
                            var reduce_debt = getOnlyNumber(value.reduce_debt)
                            var total_repair_price_value = getOnlyNumber(value.total_repair_price)
                            var total_discount_value = getOnlyNumber(value.total_discount)
                            if (add_debt) {
                                total_add_dept = parseFloat(total_add_dept) + add_debt;
                            }
                            if (reduce_debt) {
                                total_reduce_debt = parseFloat(total_reduce_debt) + reduce_debt;
                            }
                            if (total_repair_price_value) {
                                total_repair_price = parseFloat(total_repair_price) + total_repair_price_value
                            }
                            if (total_discount_value) {
                                total_discount = parseFloat(total_discount) + total_discount_value
                            }
                        })
                    }
                    this.summary_add_dept = total_add_dept;
                    this.summary_reduce_debt = total_reduce_debt;
                    this.summary_total_price = total_repair_price;
                    this.total_discount = total_discount;
                },
            },
            props: ['title'],
        });
    </script>
@endpush
