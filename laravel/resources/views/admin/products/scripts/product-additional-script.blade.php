@push('scripts')
    <script>
        let addProductAdditionalVue = new Vue({
            el: '#product-additionals',
            data: {
                product_additional_list: @if (isset($product_additional_list)) @json($product_additional_list) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#product-additionals").show();
                },
                add: function() {
                    var _this = this;
                    var product_additional_id = document.getElementById('product_additional_id_field').value;
                    var product_additional_text = (product_additional_id) ? document.getElementById('product_additional_id_field').selectedOptions[0].text : '';
                    var price = document.getElementById('price_field').value; 
                    var amount = document.getElementById('amount_field').value; 
                    var is_free = $('input[name="free_field"]:checked').val();

                    var product_additional = {};
                    const product_additional_exist = this.product_additional_list.some(function(el) { return el.product_additional_id === product_additional_id;});
                    var temp_data = this.product_additional_list[_this.edit_index];
                    var temp_product_additional = temp_data ? temp_data.product_additional_id : '';
                    if (product_additional_exist && temp_product_additional != product_additional_id ) {
                        return warningAlert("{{ __('products.product_additional_existed') }}");
                    }
                    if (product_additional_id && amount) {
                        product_additional.product_additional_id = product_additional_id;
                        product_additional.product_additional_text = product_additional_text;
                        product_additional.price = price;
                        product_additional.amount = amount;
                        product_additional.is_free = is_free;
                        
                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            addProductAdditionalVue.$set(this.product_additional_list, index, product_additional);
                        } else {
                            _this.product_additional_list.push(product_additional);
                        }
                        _this.display();

                        $('#product_additional_id_field').val('').change();
                        $('#price_field').val('');
                        $('#amount_field').val('');
                        $('input[name="free_field"][value="0"]').prop('checked', true);
                        $("#modal-product-additional").modal("hide");
                        this.edit_index = null;
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.product_additional_list[index];

                    $("#product_additional_id_field").val(temp.product_additional_id).change(); 
                    var defaultProductAdditionalOption = {
                            id: temp.product_additional_id,
                            text: temp.product_additional_text,
                    };
                    var tempProductAdditionalOption = new Option(defaultProductAdditionalOption.text, defaultProductAdditionalOption.id, true, true);
                    $("#product_additional_id_field").append(tempProductAdditionalOption).trigger('change');

                    $('#price_field').val(temp.price);
                    $('#amount_field').val(temp.amount);
                    $('input[name="free_field"][value="'+ temp.is_free +'"]').prop('checked', true);
                    this.edit_index = index;
                    $("#modal-product-additional").modal("show");
                    $("#product-additional-modal-label").html('แก้ไขข้อมูล');
                },
                remove: function(index) {
                    this.product_additional_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
            },
            props: ['title'],
        });
        addProductAdditionalVue.display();

        function addProductAdditional() {
            addProductAdditionalVue.add();
        }

        function openProductAdditionalModal() {
            addProductAdditionalVue.setIndex();
            $('#product_additional_id_field').val('').change();
            $('#price_field').val('');
            $('#amount_field').val('');
            $('input[name="free_field"][value="0"]').prop('checked', true);
            $("#product-additional-modal-label").html('เพิ่มข้อมูล');
            $("#modal-product-additional").modal("show");
        }
    </script>
@endpush
