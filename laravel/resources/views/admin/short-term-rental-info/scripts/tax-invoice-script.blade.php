@push('scripts')
    <script>
        let addTaxInvoiceVue = new Vue({
            el: '#toggle-tax-invoices',
            data: {
                /* tax_invoice_list: @if (isset($tax_invoice_list))@json($tax_invoice_list)@else[]@endif ,
                tax_invoice_list_id : @if(isset($tax_invoice_list_id)) @json($tax_invoice_list_id) @else [] @endif, */
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#tax-invoices").show('slow');
                },
                addTaxInvoice: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    $('.toggle-div').hide();
                    // branchToggleDisabled(true);
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#tax-invoice-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#tax_customer_type").val(null).trigger('change');
                    $("#tax_tax_no").val('');
                    $("#tax_branch_office").val(null).trigger('change');
                    $("#tax_branch_name").val('');
                    $("#tax_branch_no").val('');
                    $("#tax_customer_name").val('');
                    $("#tax_customer_email").val('');
                    $("#tax_customer_tel").val('');
                    $("#tax_customer_province_id").val(null).trigger('change');
                    $("#tax_customer_zipcode").val('');
                    $("#tax_customer_address").val('');
                    // branchToggleDisabled(true);
                },
                setOption: function(_id, _text) {
                    var defaultOption = {
                        id: _id,
                        text: _text,
                    };
                    var tempOption = new Option(defaultOption.text, defaultOption.id, true, true);
                    return tempOption;
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.tax_invoice_list[index];
                    if (temp.tax_customer_type_id === '{{ \App\Enums\CustomerTypeEnum::CORPORATION }}') {
                        // branchToggleDisabled(false);
                    }

                    $("#tax_branch_office").val(temp.tax_branch_office_id).trigger('change');
                    $("#tax_customer_type").val(temp.tax_customer_type_id).trigger('change');
                    $("#tax_tax_no").val(temp.tax_tax_no);
                    $("#tax_branch_name").val(temp.tax_branch_name);
                    $("#tax_branch_no").val(temp.tax_branch_no);
                    $("#tax_customer_name").val(temp.tax_customer_name);
                    $("#tax_customer_email").val(temp.tax_customer_email);
                    $("#tax_customer_tel").val(temp.tax_customer_tel);
                    $("#tax_customer_province_id").val(temp.tax_customer_province_id);
                    $("#tax_customer_zipcode").val(temp.tax_customer_zipcode);
                    $("#tax_customer_address").val(temp.tax_customer_address);
                },
                openModal: function() {
                    $("#modal-tax-invoice").modal("show");
                },
                hideModal: function() {
                    $('.toggle-div').hide();
                    // branchToggleDisabled(true);
                    $("#modal-tax-invoice").modal("hide");
                },
                save: function() {
                    var _this = this;
                    if (_this.mode == 'edit') {
                        var index = _this.edit_index;
                        _this.saveEdit(index);
                    } else {
                        _this.saveAdd();
                    }
                },
                getDataFromModalAdd: function() {
                    var tax_customer_type_id = document.getElementById("tax_customer_type").value;
                    var tax_customer_type_text = (tax_customer_type_id) ? document.getElementById(
                        'tax_customer_type').selectedOptions[0].text : '';
                    var tax_tax_no = document.getElementById("tax_tax_no").value;
                    var tax_branch_office_id = document.getElementById("tax_branch_office").value;
                    var tax_branch_office_text = (tax_branch_office_id) ? document.getElementById(
                        'tax_branch_office').selectedOptions[0].text : '';
                    var tax_branch_name = document.getElementById("tax_branch_name").value;
                    var tax_branch_no = document.getElementById("tax_branch_no").value;
                    var tax_customer_name = document.getElementById("tax_customer_name").value;
                    var tax_customer_email = document.getElementById("tax_customer_email").value;
                    var tax_customer_tel = document.getElementById("tax_customer_tel").value;
                    var tax_customer_province_id = document.getElementById("tax_customer_province_id").value;
                    var tax_customer_province_text = (tax_customer_province_id) ? document.getElementById(
                        'tax_customer_province_id').selectedOptions[0].text : '';
                    var tax_customer_zipcode = document.getElementById("tax_customer_zipcode").value;
                    var tax_customer_address = document.getElementById("tax_customer_address").value;

                    return {
                        // id: id,
                        tax_customer_type_id: tax_customer_type_id,
                        tax_customer_type_text: tax_customer_type_text,
                        tax_tax_no: tax_tax_no,
                        tax_branch_office_id: tax_branch_office_id,
                        tax_branch_office_text: tax_branch_office_text,
                        tax_branch_name: tax_branch_name,
                        tax_branch_no: tax_branch_no,
                        tax_customer_name: tax_customer_name,
                        tax_customer_email: tax_customer_email,
                        tax_customer_tel: tax_customer_tel,
                        tax_customer_province_id: tax_customer_province_id,
                        tax_customer_province_text: tax_customer_province_text,
                        tax_customer_zipcode: tax_customer_zipcode,
                        tax_customer_address: tax_customer_address
                    };
                },
                validateDataObject: function(tax_invoice) {
                    if (tax_invoice.tax_customer_type_id && tax_invoice.tax_tax_no && tax_invoice.tax_customer_name && tax_invoice.tax_customer_address) {
                        return true;
                    }
                    return false;
                },
                saveAdd: function() {
                    var tax_invoice = this.getDataFromModalAdd();
                    if (this.validateDataObject(tax_invoice)) {
                        this.tax_invoice_list.push(tax_invoice);
                        this.edit_index = null;
                        return this.tax_invoice_list;
                        // this.display();
                        // this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var tax_customer_type_id = document.getElementById("tax_customer_type").value;
                    var tax_customer_type_text = (tax_customer_type_id) ? document.getElementById(
                        'tax_customer_type').selectedOptions[0].text : '';
                    var tax_tax_no = document.getElementById("tax_tax_no").value;
                    var tax_branch_office_id = document.getElementById("tax_branch_office").value;
                    var tax_branch_office_text = (tax_branch_office_id) ? document.getElementById(
                        'tax_branch_office').selectedOptions[0].text : '';
                    var tax_branch_name = document.getElementById("tax_branch_name").value;
                    var tax_branch_no = document.getElementById("tax_branch_no").value;
                    var tax_customer_name = document.getElementById("tax_customer_name").value;
                    var tax_customer_email = document.getElementById("tax_customer_email").value;
                    var tax_customer_tel = document.getElementById("tax_customer_tel").value;
                    var tax_customer_province_id = document.getElementById("tax_customer_province_id").value;
                    var tax_customer_province_text = (tax_customer_province_id) ? document.getElementById(
                        'tax_customer_province_id').selectedOptions[0].text : '';
                    var tax_customer_zipcode = document.getElementById("tax_customer_zipcode").value;
                    var tax_customer_address = document.getElementById("tax_customer_address").value;
                    var tax_invoice = this.tax_invoice_list[index];

                    tax_invoice.tax_customer_type_id = tax_customer_type_id;
                    tax_invoice.tax_customer_type_text = tax_customer_type_text;
                    tax_invoice.tax_tax_no = tax_tax_no;
                    tax_invoice.tax_branch_office_id = tax_branch_office_id;
                    tax_invoice.tax_branch_name = tax_branch_name;
                    tax_invoice.tax_branch_no = tax_branch_no;
                    tax_invoice.tax_customer_name = tax_customer_name;
                    tax_invoice.tax_customer_email = tax_customer_email;
                    tax_invoice.tax_customer_tel = tax_customer_tel;
                    tax_invoice.tax_customer_province_id = tax_customer_province_id;
                    tax_invoice.tax_customer_province_text = tax_customer_province_text;
                    tax_invoice.tax_customer_zipcode = tax_customer_zipcode;
                    tax_invoice.tax_customer_address = tax_customer_address;
                    if (this.validateDataObject(tax_invoice)) {
                        addTaxInvoiceVue.$set(this.tax_invoice_list, index, tax_invoice);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                remove: function(index) {
                    this.tax_invoice_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.tax_invoice_list.length;
                },
                resetData: function() {
                    this.tax_invoice_list = [];
                    this.edit_index = null;
                    this.mode = null;
                    return true;
                },
                addById: function(e, index) {
                    var _this = this;
                    var tax_invoice = {};
                    if (e.id) {
                        tax_invoice.tax_customer_type_id = e.billing_customer_type;
                        tax_invoice.tax_customer_type_text = e.billing_customer_type_text;
                        tax_invoice.tax_tax_no = e.tax_no;
                        tax_invoice.tax_branch_office_id = '';
                        tax_invoice.tax_branch_name = '';
                        tax_invoice.tax_branch_no = '';
                        tax_invoice.tax_customer_name = e.name;
                        tax_invoice.tax_customer_email = e.email;
                        tax_invoice.tax_customer_tel = e.tel;
                        tax_invoice.tax_customer_province_id = e.province_id;
                        tax_invoice.tax_customer_province_text = e.province_text;
                        tax_invoice.tax_customer_zipcode = e.zipcode;
                        tax_invoice.tax_customer_address = e.address;
                        tax_invoice.id = e.id;

                        _this.tax_invoice_list.push(tax_invoice);
                    }
                },
                removeAll: function() {
                    this.tax_invoice_list = [];
                },
                billingById(customer_billing_address_id) {
                    document.getElementById("customer_billing_address_id").value = customer_billing_address_id;
                    $('.block-customer').click(function() {
                        if ($('.customer-active').length) {
                            $('.customer-active').not($(this)).removeClass('customer-active').addClass('block-customer');
                        }
                        $(this).removeClass('block-customer').addClass('customer-active');
                    });
                },
            },
            props: ['title'],
        });
        addTaxInvoiceVue.display();

        function addTaxInvoice() {
            addTaxInvoiceVue.addTaxInvoice();
        }

        function saveTaxInvoice() {
            var tax_invoice = addTaxInvoiceVue.getDataFromModalAdd();
            var customer_id = $('#customer_id').val();
            if (!customer_id) {
                warningAlert('กรุณาเลือกรหัสลูกค้า');
            }
            if (tax_invoice.tax_customer_type_id && tax_invoice.tax_tax_no && tax_invoice.tax_customer_name && tax_invoice.tax_customer_address) {
                var data = {
                    tax_invoice: tax_invoice,
                    customer_id: customer_id,
                };
                var updateUri = "{{ route('admin.short-term-rental.info.store-customer-billing') }}";
                axios.post(updateUri, data).then(response => {
                    if (response.data.success) {
                        addTaxInvoiceVue.hideModal();
                        getDataCustomerBillingAddress(customer_id);
                    } else {
                        mySwal.fire({
                            title: "{{ __('lang.store_error_title') }}",
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: "{{ __('lang.ok') }}",
                        }).then(value => {
                            if (value) {
                                //
                            }
                        });
                    }
                });
            } else {
                warningAlert('{{ __('lang.required_field_inform') }}');
            }
        }

        function getDataCustomerBillingAddress(customer_id, cb) {
            showLoading();
            axios.get("{{ route('admin.short-term-rental.info.default-data-customer-billing-address') }}", {
                params: {
                    customer_id: customer_id
                }
            }).then(response => {
                hideLoading();
                if (response.data.success) {
                    addTaxInvoiceVue.resetData();
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e, index) => {
                            addTaxInvoiceVue.addById(e, index);
                        });
                    }
                    var customer_billing_address_id_selected = $('#customer_billing_address_id_selected').val();
                    if(customer_billing_address_id_selected != ""){
                        $("#customer_billing_address_id").val(customer_billing_address_id_selected);
                    }
                    if(typeof cb != 'undefined'){
                        cb();
                    }
                }
            });
        }
    </script>
@endpush
