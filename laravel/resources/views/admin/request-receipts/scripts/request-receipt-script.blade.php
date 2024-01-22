@push('scripts')
    <script>
        let addRequestReceiptVue = new Vue({
            el: '#request-receipt-vue',
            data: {
                request_receipt_list: @if (isset($request_receipt_list))
                    @json($request_receipt_list)
                @else
                    []
                @endif ,
                edit_index: null,
                total_car: 0,
                mode: null,
            },
            methods: {
                sumTotal() {
                    var amount = $('#amount').val().replace(/,/g, '');
                    var fee_deducted = $('#fee_deducted').val().replace(/,/g, '');
                    if (!isNaN(amount) && !isNaN(fee_deducted)) {
                        total = amount - fee_deducted;
                        total = this.getNumberWithCommas(total);
                    } else {
                        total = 0;
                    }
                    $('#total').val(total);
                },
                addList: function() {
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editList: function(index) {
                    this.clearModalData();
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#list-modal-label").html('แก้ไขรายการ');
                    this.openModal();
                },
                clearModalData: function() {
                    $('#amount').val('');
                    $('#fee_deducted').val('');
                    $('#list_name').val('');
                    $('#total').val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.request_receipt_list[index];
                    $("#amount").val(temp.amount);
                    $("#fee_deducted").val(temp.fee_deducted);
                    $("#list_name").val(temp.list_name);
                    $("#total").val(temp.total);
                    $("#id_line").val(temp.id);
                },
                openModal: function() {
                    $('#list-modal').modal('show');
                },
                hideModal: function() {
                    $('#list-modal').modal('hide');
                },
                save: function() {
                    var _this = this;
                    var request_receipt_data = _this.getCarDataFromModal();
                    if (_this.validateObject(request_receipt_data)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(request_receipt_data, index);
                        } else {
                            _this.saveAdd(request_receipt_data);
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarDataFromModal: function() {
                    var amount = document.getElementById("amount").value;
                    var fee_deducted = document.getElementById("fee_deducted").value;
                    var total = document.getElementById("total").value;
                    var list_name = document.getElementById("list_name").value;
                    var id_line = document.getElementById("id_line").value;
                    return {
                        amount: amount,
                        fee_deducted: fee_deducted,
                        total: total,
                        list_name: list_name,
                        id: id_line,
                    };
                },
                validateObject: function(request_receipt_data) {
                    if (request_receipt_data.amount && request_receipt_data.fee_deducted && request_receipt_data
                        .list_name) {
                        return true;
                    } else {
                        return false;
                    }
                },
                format_date: function(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-SG', options);
                    return formattedDate;
                },
                saveAdd: function(request_receipt_data) {
                    this.request_receipt_list.push(request_receipt_data);
                },
                saveEdit: function(request_receipt_data, index) {
                    addRequestReceiptVue.$set(this.request_receipt_list, index, request_receipt_data);
                },
                removeList: function(index) {
                    this.request_receipt_list.splice(index, 1);

                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.lawsuit_list.length;
                },
                getNumberWithCommas(x) {
                    return numberWithCommas(x);
                },
            },
            props: ['title'],
        });
        addRequestReceiptVue.display();

        function addList() {
            addRequestReceiptVue.addList();
        }

        function saveList() {
            addRequestReceiptVue.save();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
