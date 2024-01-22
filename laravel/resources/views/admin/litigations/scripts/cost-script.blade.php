@push('scripts')
    <script>
        let addCostVue = new Vue({
            el: '#cost-list',
            data: {
                status: @if (isset($d->status)) @json($d->status) @else null @endif,
                cost_list: @if (isset($cost_list)) @json($cost_list) @else [] @endif,
                pending_delete_cost_ids: [],
                edit_index: null,
                summary: @if (isset($summary)) @json($summary) @else 0 @endif,
            },
            watch: {
                cost_list: function () {
                    this.getSum();
                },
            },
            methods: {
                display: function() {
                    $("#cost-list").show();
                },
                add: function() {
                    var _this = this;
                    var list = document.getElementById("temp_list").value;
                    var number = document.getElementById("temp_number").value;
                    var bank_id = document.getElementById("temp_bank_id").value;
                    var bank_text = (bank_id) ? document.getElementById('temp_bank_id').selectedOptions[0]
                        .text : '';
                    var payment_channel = document.getElementById("temp_payment_channel").value;
                    var payment_channel_text = (payment_channel) ? document.getElementById(
                        'temp_payment_channel').selectedOptions[0].text : '';
                    var amount_text = document.getElementById("temp_amount").value;
                    var amount = parseFloat(amount_text.replace(/,/g, '')).toFixed(2);
                    var date = document.getElementById("temp_payment_date").value;
                    var cost = {};

                    if (list && number && bank_id && payment_channel && amount && date) {
                        cost.list = list;
                        cost.number = number;
                        cost.bank_id = bank_id;
                        cost.bank_text = bank_text;
                        cost.payment_channel = payment_channel;
                        cost.payment_channel_text = payment_channel_text;
                        cost.date = date;
                        cost.amount = amount;
                        if (_this.edit_index != null) {
                            index = _this.edit_index;
                            temp = this.cost_list[index];
                            cost.id = temp.id;
                            addCostVue.$set(this.cost_list, index, cost);
                        } else {
                            _this.cost_list.push(cost);
                        }
                        _this.display();

                        $("#temp_list").val(null);
                        $("#temp_number").val(null);
                        $("#temp_bank_id").val(null).change();
                        $("#temp_payment_channel").val(null).change();
                        $("#temp_amount").val(null);
                        $("#temp_payment_date").val(null);
                        $("#modal-cost").modal("hide");
                        this.edit_index = null;
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.cost_list[index];
                    $("#temp_list").val(temp.list);
                    $("#temp_number").val(temp.number);
                    $("#temp_bank_id").val(temp.bank_id).change();
                    $("#temp_payment_channel").val(temp.payment_channel).change();
                    $("#temp_amount").val(temp.amount);
                    $("#temp_payment_date").val(temp.date);
                    this.edit_index = index;
                    $("#modal-cost").modal("show");
                    $("#cost-modal-label").html('แก้ไขข้อมูลค่าใช้จ่าย');
                },
                remove: function(index) {
                    if (this.cost_list[index] && this.cost_list[index].id) {
                        this.pending_delete_cost_ids.push(this.cost_list[index].id);
                    }
                    this.cost_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                numberWithCommas: function(x) {
                    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                },
                getSum: function() {
                    _this = this;
                    let sums = _this.cost_list.reduce((acc, cost) => {
                        return acc + parseFloat(cost.amount);
                    }, 0);
                    this.summary = parseFloat(sums).toFixed(2);
                },
                formatDate(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-US', options);
                    return formattedDate;
                },
                editable: function(item) {
                    can_edit = true;
                    if (this.status != '{{ LitigationStatusEnum::PENDING }}') {
                        can_edit = false;
                    }
                    if (!item.id) {
                        can_edit = true;
                    }
                    return can_edit;
                },
            },
            props: ['title'],
        });
        addCostVue.display();

        function addCost() {
            addCostVue.add();
        }

        function hideCostModal() {
            $("#modal-cost").modal("hide");
        }

        function openCostModal() {
            addCostVue.setIndex();
            $("#cost-modal-label").html('เพิ่มข้อมูลค่าใช้จ่าย');
            $("#temp_list").val(null);
            $("#temp_number").val(null);
            $("#temp_bank_id").val(null).change();
            $("#temp_payment_channel").val(null).change();
            $("#temp_amount").val(null);
            $("#modal-cost").modal("show");
        }
    </script>
@endpush
