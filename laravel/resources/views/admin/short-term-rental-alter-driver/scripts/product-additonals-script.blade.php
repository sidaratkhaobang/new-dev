@push('scripts')
    <script>
        let addProductAdditionalVue = new Vue({
            el: '#product-additionals',
            data: {
                product_additional_list: @if (isset($product_additional_list)) @json($product_additional_list) @else [] @endif,
                product_additionals: @if (isset($product_additionals)) @json($product_additionals) @else [] @endif,
                cars: @if (isset($cars)) @json($cars) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#product-additionals").show();
                },
                async add() {
                    var _this = this;
                    var product_additional_id = $("#product_additional_id").val();
                    var name = (product_additional_id) ? document.getElementById('product_additional_id').selectedOptions[0].text : '';
                    var price = $("#product_additional_price").val();
                    var amount = $("#product_additional_amount").val();
                    var car_id = $("#product_additional_car_id").val();
                    var car_name = (car_id) ? document.getElementById('product_additional_car_id').selectedOptions[0].text : '';
                    
                    var data = await getProductAdditionalDetail(product_additional_id);
                    var product_data = data.data;
                    if (product_data.is_stock == true && amount > product_data.amount) {
                        return warningAlert("{{ __('short_term_rentals.product_amount_invalid') }}");
                    }
                    var product = {};
                    if (product_additional_id && price && amount && car_id) {
                        product.product_additional_id = product_additional_id;
                        product.name = name;
                        product.price = price;
                        product.amount = amount;
                        product.car_id = car_id;
                        product.car_name = car_name;
                        product.is_free = false;
                        product.is_from_product = false;
                        product.is_from_promotion = false;
                        product.price_format = this.numberWithCommas(price * amount);

                        if (_this.edit_index != null) {
                            index = _this.edit_index;
                            temp = this.product_additional_list[index];
                            product.id = temp.id;
                            product.is_free = temp.is_free;
                            product.is_from_product = temp.is_from_product;
                            product.is_from_promotion = temp.is_from_promotion;
                            addProductAdditionalVue.$set(this.product_additional_list, index, product);
                        } else {
                            _this.product_additional_list.push(product);
                        }
                        _this.display();

                        $("#product_additional_id").val(null).change();
                        $("#product_additional_price").val('');
                        $("#product_additional_amount").val('');
                        $("#product_additional_car_id").val(null).change();
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
                    $("#product_additional_id").val(temp.product_additional_id).change();
                    $("#product_additional_price").val(temp.price);
                    $("#product_additional_amount").val(temp.amount);
                    $("#product_additional_car_id").val(temp.car_id).change();                

                    var defaultProductOption = {
                        id: temp.product_additional_id,
                        text: temp.name,
                    };
                    var tempProductOption = new Option(defaultProductOption.text, defaultProductOption.id, false, false);
                    $("#product_additional_id").append(tempProductOption).trigger('change');

                    this.edit_index = index;

                    $("#modal-product-additional").modal("show");
                    $("#product-additional-label").html('แก้ไขข้อมูล');
                },
                remove: function(index) {
                    this.product_additional_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                numberWithCommas: function(x) {
                    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                },
                setDefaultCar: function() {
                    if (this.cars.length == 1) {
                        car = this.cars[0];
                        $("#product_additional_car_id").val(car.id).change();  
                        $("#product_additional_car_id").prop('disabled', true);  
                    }
                },
            },
            props: ['title'],
        });
        addProductAdditionalVue.display();

        function addProductAdditional() {
            addProductAdditionalVue.add();
        }
        function deleteProductAdditional() {
            addProductAdditionalVue.remove();
        }

        function openProductAdditionalModal() {
            $("#product_additional_id").val(null).change();
            $("#product_additional_price").val('');
            $("#product_additional_price").prop('disabled', true);
            $("#product_additional_amount").val('');
            addProductAdditionalVue.setIndex();
            addProductAdditionalVue.setDefaultCar();
            $("#product-additional-label").html('เพิ่มข้อมูล');
            $("#modal-product-additional").modal("show");
        }

        $('#product_additional_id').on('select2:select', function (e) {
            var data = e.params.data;
            getProductAdditionalDetail(data.id).then( response => {
                if (response.success) {
                    data = response.data;
                    $("#product_additional_price").val(data.price);
                }
            });
        });

        async function getProductAdditionalDetail(id) 
        {
            const url = "{{ route('admin.util.select2.product-additional-detail') }}";
            const response = await axios.get(url, {
                params: { product_additional_id: id }
            });
            return response.data;
        }
    </script>
@endpush
