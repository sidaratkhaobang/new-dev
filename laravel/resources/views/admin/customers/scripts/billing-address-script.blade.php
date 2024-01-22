@push('scripts')
    <script>
        let addCustomerBillingAddressVue = new Vue({
            el: '#customer-billing-address',
            data: {
                customer_billing_address_list: @if (isset($customer_billing_address_list)) @json($customer_billing_address_list) @else [] @endif,
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#customer-billing-address").show();
                },
                addBillingAddress: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editBillingAddress: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#billing-address-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#name_field").val('');
                    $("#tax_no_field").val('');
                    $("#address_field").val('');
                    $("#email_field").val('');
                    $("#tel_field").val('');
                    $("#province_field").val('').change();
                    $("#district_field").val('').change();
                    $("#subdistrict_field").val('').change();
                },
                loadModalData: function(index){
                    var temp = this.customer_billing_address_list[index];
                    $("#name_field").val(temp.name);
                    $("#tax_no_field").val(temp.tax_no);
                    $("#address_field").val(temp.address);
                    $("#email_field").val(temp.email);
                    $("#tel_field").val(temp.tel);
                    //$("#province_field").val(temp.province_id).change();

                    if(temp.province_id){
                        set_select2($("#province_field"), temp.province_id, temp.province_name);
                    }
                    if(temp.district_id){
                        set_select2($("#district_field"), temp.district_id, temp.district_name);
                    }
                    if(temp.subdistrict_id){
                        set_select2($("#subdistrict_field"), temp.subdistrict_id, temp.subdistrict_name);
                    }
                },
                openModal: function(){
                    $("#modal-billing-address").modal("show");
                },
                hideModal: function(){
                    $("#modal-billing-address").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var billing_address = _this.getDataFromModal();
                    if (_this.validateDataObject(billing_address)) {
                        if(_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(billing_address, index);
                        }else {
                            _this.saveAdd(billing_address);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getDataFromModal: function(){
                    var name = document.getElementById("name_field").value;
                    var tax_no = document.getElementById("tax_no_field").value;
                    var address = document.getElementById("address_field").value;
                    var email = document.getElementById("email_field").value;
                    var tel = document.getElementById("tel_field").value;
                    var province_id = document.getElementById("province_field").value;
                    var province_name = (province_id) ? document.getElementById('province_field').selectedOptions[0].text : '';
                    var district_id = document.getElementById("district_field").value;
                    var district_name = (district_id) ? document.getElementById('district_field').selectedOptions[0].text : '';
                    var subdistrict_id = document.getElementById("subdistrict_field").value;
                    var subdistrict_name = (subdistrict_id) ? document.getElementById('subdistrict_field').selectedOptions[0].text : '';
                    return {
                        name: name,
                        tax_no: tax_no,
                        address: address,
                        email: email,
                        tel: tel,
                        province_id: province_id,
                        province_name: province_name,
                        district_id: district_id,
                        district_name: district_name,
                        subdistrict_id: subdistrict_id,
                        subdistrict_name: subdistrict_name,
                    };
                },
                validateDataObject: function(billing_address){
                    if (billing_address.name && billing_address.tax_no && billing_address.address) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(billing_address){
                    this.customer_billing_address_list.push(billing_address);
                    console.log(this.customer_billing_address_list);
                },
                saveEdit: function(billing_address, index) {
                    addCustomerBillingAddressVue.$set(this.customer_billing_address_list, index, billing_address);
                },
                removeBillingAddress: function(index) {
                    this.customer_billing_address_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.customer_billing_address_list.length;
                },
                /* set_select2: function(selector, id, value){
                    set_select2(selector, id, value);
                } */
            },
            props: ['title'],
        });
        addCustomerBillingAddressVue.display();

        function addBillingAddress(){
            addCustomerBillingAddressVue.addBillingAddress();
        }

        function saveBillingAddress() {
            addCustomerBillingAddressVue.save();
        }

    </script>
@endpush
