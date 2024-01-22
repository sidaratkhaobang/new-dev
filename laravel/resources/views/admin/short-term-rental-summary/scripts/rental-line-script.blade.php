@push('scripts')
    <script>
        let addRentalVue = new Vue({
            el: '#rental-lines',
            data: {
                rental_line_list: [],
                cars: @json($cars) ,
                modal_car: null,
                extra_product: [],
                modal_extra_product: [],
                pending_delete_extra_product_ids: [],
                product_add: [],
                modal_product_additional: [],
                edit_index: null,
                extracost: '{{ OrderLineTypeEnum::EXTRA }}',
                othercost: '{{ OrderLineTypeEnum::OTHER }}',
                rental_id: '{{ $rental_id }}',
                product: "App\\Models\\Product",
                summary: @if (isset($summary))
                    @json($summary)
                @else
                    []
                @endif ,
                summary_init: @if (isset($summary))
                    @json($summary)
                @else
                    []
                @endif ,

                is_withholding_tax: @if (isset($rental->is_withholding_tax))
                    true
                @else
                    false
                @endif ,
                withholding_tax_value: @if (isset($rental->withholding_tax_value))
                    true
                @else
                    0
                @endif ,
            },
            watch: {
                rental_line_list: function(_rental_line_list) {
                    /* console.log('rental_line_list change');
                    var _subtotal = 0;
                    _rental_line_list.forEach(function(item) {
                        _subtotal = parseFloat(_subtotal) + parseFloat(item.total);
                    });
                    var withholding_tax_val = $('input[name="withholding_tax[]"]:checked').val(); */
                    /* this.calculateWithHoldingTax(withholding_tax_val, _subtotal);
                    this.validatePriceChange(); */
                },
                is_withholding_tax: function(_is_withholding_tax) {
                    /* this.validatePriceChange(); */
                },
                withholding_tax_value: function(_withholding_tax_value) {
                    /* this.validatePriceChange(); */
                },
            },
            methods: {
                // car
                editProduct: function(index, car, car_id){
                    console.log('edit', index, car, car_id);

                    // prepare modal for edit
                    $('#rental_line_name').val(car.product_name);
                    $('#rental_line_description').val(car.license_plate);
                    $('#rental_line_amount').val(car.amount);
                    $('#rental_line_subtotal').val(car.unit_price);
                    this.modal_car = car;

                    $('#car-modal').modal('show');
                },
                saveCar: function() {
                    var params = {
                        car_id: this.modal_car.car_id,
                        rental_line_id: this.modal_car.rental_line_id,
                        unit_price: $('#rental_line_subtotal').val(),
                        rental_id: this.rental_id,
                    };
                    console.log('params', params);
                    var _this = this;
                    axios.post("{{ route('admin.short-term-rental.summary.update-rental-car') }}", params).then(response => {
                        if (response.data.success) {
                            console.log(response.data.data);
                            _this.summary = response.data.summary;
                            _this.cars = response.data.cars;
                            $('#car-modal').modal('hide');
                        }
                    });
                },


                // extra
                pushExtraLine(rental_line_id, name, amount, unit_price){
                    let data = {
                        rental_line_id: rental_line_id,
                        name: name,
                        amount: amount,
                        unit_price: unit_price,
                        total: 0,
                    }
                    this.modal_extra_product.push(data);
                    $(".add-product-empty").hide();
                },
                addExtraLine() {
                    this.pushExtraLine(null, null, 0, 0);
                },
                removeExtraLine: function(index2, rental_line_id) {
                    this.modal_extra_product.splice(index2, 1);
                    if(rental_line_id != null && rental_line_id != ""){
                        this.pending_delete_extra_product_ids.push(rental_line_id);
                    }
                },
                addExtra(){
                    $('input[type=checkbox][name=car_modal_extra_id]').prop('checked', true);
                    this.modal_extra_product = [];
                    $('input[type=checkbox][name=car_modal_extra_id]').unbind('click');
                    $('#extra-modal').modal('show');
                },
                editExtra: function(index, car, car_id) {
                    console.log('edit', index, car, car_id);

                    // prepare modal for edit
                    $('input[type=checkbox][name=car_modal_extra_id]').prop('checked', false);
                    $('input[type=checkbox][name=car_modal_extra_id][value="'+ car_id +'"]').prop('checked', true);
                    $('input[type=checkbox][name=car_modal_extra_id]').on('click', function(){
                        return false;
                    });

                    this.modal_extra_product = [];
                    var extras = [...car.extras];
                    extras.forEach((extra, index) => {
                        this.pushExtraLine(extra.rental_line_id, extra.extra_name, extra.amount, extra.unit_price);
                        this.setTotalExtra(index);
                    });
                    $('#extra-modal').modal('show');
                },
                saveExtra() {
                    var cars_selected = [];
                    $('input[type=checkbox][name=car_modal_extra_id]:checked').each(function(){
                        cars_selected.push($(this).val());
                    });
                    var _modal_extra_product = [...this.modal_extra_product];

                    // validate name empty
                    var validate_data = _modal_extra_product.filter((item) => {
                        return (item.name == "" || item.name == null) || (parseFloat(item.amount) < 0) || (parseFloat(item.unit_price) < 0);
                    });
                    if(validate_data.length > 0){
                        warningAlert('กรุณากรอกข้อมูลให้ถูกต้อง/ครบถ้วน');
                        return false;
                    }

                    _modal_extra_product = _modal_extra_product.filter((item) => {
                        return (item.name != "" && item.name != null);
                    });
                    /* if(_modal_extra_product.length <= 0){
                        warningAlert('กรุณากรอกข้อมูล');
                        return false;
                    } */
                    var params = {
                        extras: _modal_extra_product,
                        cars_selected: cars_selected,
                        rental_id: this.rental_id,
                        pending_delete_extra_product_ids: this.pending_delete_extra_product_ids
                    };
                    console.log('params', params);
                    var _this = this;
                    axios.post("{{ route('admin.short-term-rental.summary.update-rental-extra') }}", params).then(response => {
                        if (response.data.success) {
                            console.log(response.data.data);
                            _this.summary = response.data.summary;
                            _this.cars = response.data.cars;
                            _this.pending_delete_extra_product_ids = [];
                            $('#extra-modal').modal('hide')
                        }
                    });
                },
                setTotalExtra(key) {
                    var _modal_extra_product = [...this.modal_extra_product];
                    var item = _modal_extra_product[key];
                    item.subtotal = this.calTotalText(item.amount, item.unit_price);
                    _modal_extra_product[key] = item;
                    this.modal_extra_product = _modal_extra_product;
                },


                // product_additional
                editProductAdditional: function(index, car, car_id){
                    console.log('edit', index, car, car_id);

                    // prepare modal for edit
                    $('input[type=checkbox][name=car_modal_id]').prop('checked', false);
                    $('input[type=checkbox][name=car_modal_id][value="'+ car_id +'"]').prop('checked', true);
                    $('input[type=checkbox][name=car_modal_id]').on('click', function(){
                        return false;
                    });

                    //this.modal_product_additional = [];
                    this.modal_product_additional = [...car.product_additionals];
                    this.modal_product_additional.forEach((product_additional, index) => {
                        product_additional.subtotal = this.calTotalText(product_additional.amount, product_additional.unit_price);
                    });
                    $('#rental-line-modal').modal('show');
                },
                saveProductAdditional() {
                    var cars_selected = [];
                    $('input[type=checkbox][name=car_modal_id]:checked').each(function(){
                        cars_selected.push($(this).val());
                    });
                    var params = {
                        product_additionals: this.modal_product_additional,
                        cars_selected: cars_selected,
                        rental_id: this.rental_id,
                    };
                    console.log('params', params);
                    var _this = this;
                    axios.post("{{ route('admin.short-term-rental.summary.update-rental-product-additional') }}", params).then(response => {
                        if (response.data.success) {
                            console.log(response.data.data);
                            _this.summary = response.data.summary;
                            _this.cars = response.data.cars;
                            $('#rental-line-modal').modal('hide')
                        }
                    });
                },
                setTotalProductAdditional: function(key) {
                    var _modal_product_additional = [...this.modal_product_additional];
                    var item = _modal_product_additional[key];
                    item.subtotal = this.calTotalText(item.amount, item.unit_price);
                    _modal_product_additional[key] = item;
                    this.modal_product_additional = _modal_product_additional;
                },
                calTotalText: function(amount, unit_price) {
                    return this.number_format(parseFloat(amount) * parseFloat(unit_price) * 1.07);
                },


                setSummary(summary){
                    this.summary = summary;
                },
                number_format(number) {
                    return number_format(number);
                },
            },
            props: ['title'],
        });

        function addExtraLine() {
            addRentalVue.addExtraLine();
        }

        function saveExtra() {
            addRentalVue.saveExtra();
        }

        function saveCar() {
            addRentalVue.saveCar();
        }

        function saveProductAdditional() {
            addRentalVue.saveProductAdditional();
        }

        function addExtraProduct() {
            addRentalVue.addExtraProduct();
        }

        function openExtraModal() {
            addRentalVue.addExtra();
        }
    </script>
@endpush
