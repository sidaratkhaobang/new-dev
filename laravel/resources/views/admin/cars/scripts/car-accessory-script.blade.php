@push('scripts')
    <script>
        let addCarAccessoryVue = new Vue({
            el: '#car-accessories',
            data: {
                car_accessory_list: @if (isset($car_accessory_list)) @json($car_accessory_list) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $('#car-accessories').show();
                },
                add: function() {
                    var _this = this;
                    var accessory_id = document.getElementById('accessory_field').value; 
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field').selectedOptions[0].text : '';
                    var amount = document.getElementById('amount_field').value; 
                    var remark = document.getElementById('remark_field').value; 

                    var car_accessory = {};
                    if (accessory_id && amount) {
                        car_accessory.accessory_id = accessory_id;
                        car_accessory.accessory_text = accessory_text;
                        car_accessory.amount = amount;
                        car_accessory.remark = remark;

                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            addCarAccessoryVue.$set(this.car_accessory_list, index, car_accessory);
                        } else {
                            _this.car_accessory_list.push(car_accessory);
                        }
                        _this.display();


                        $('#accessory_purchase_order_field').val('').change();
                        $('#accessory_field').val('').change();
                        $('#brand_field').val('').change();
                        $('#amount_field').val('');
                        $('#supplier_field').val('');
                        $('#remark_field').val('');
                        $('#car-accessory-modal').modal('hide');
                        this.edit_index = null;
                    }else{
                        warningAlert('{{ __("lang.required_field_inform") }}');
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.car_accessory_list[index];
                    $('#accessory_purchase_order_field').val(temp.accessory_purchase_order_id).change();
                    $('#accessory_field').val(temp.accessory_id).change();
                    $('#brand_field').val(temp.brand_id).change();
                    $('#supplier_field').val(temp.supplier);
                    $('#amount_field').val(temp.amount);
                    $('#remark_field').val(temp.remark);

                    var defaultAccessoryOption = {
                            id: temp.accessory_id,
                            text: temp.accessory_text,
                    };
                    var tempAccessoryOption = new Option(defaultAccessoryOption.text, defaultAccessoryOption.id, false, false);
                    $('#accessory_field').append(tempAccessoryOption).trigger('change');

                    var defaultBrandOption = {
                            id: temp.brand_id,
                            text: temp.brand_text,
                    };
                    var tempBrandOption = new Option(defaultBrandOption.text, defaultBrandOption.id, false, false);
                    $('#brand_field').append(tempBrandOption).trigger('change');

                    this.edit_index = index;
                    $('#car-accessory-modal').modal('show');
                    $('#car-accessory-modal-label').html('แก้ไขข้อมูลอุปกรณ์ภายในรถ');
                },
                remove: function(index) {
                    this.car_accessory_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                }
            },
            props: ['title'],
        });
        addCarAccessoryVue.display();

        function addCarAccessory() {
            addCarAccessoryVue.add();
        }

        function openCarAccessoryModal() {
            $('#accessory_purchase_order_field').val('').change();
            $('#accessory_field').val('').change();
            $('#brand_field').val('').change();
            $('#amount_field').val('');
            $('#supplier_field').val('');
            $('#remark_field').val('');
            $('#car-accessory-modal').modal('hide');
            addCarAccessoryVue.setIndex();
            $('#car-accessory-modal-label').html('เพิ่มข้อมูลอุปกรณ์ภายในรถ');
            $('#car-accessory-modal').modal('show');
        }
    </script>
@endpush

@include('admin.components.select2-ajax', [
'id' => 'accessory_field',
'modal' => '#car-accessory-modal',
'url' => route('admin.util.select2.accessories'),
])

@include('admin.components.select2-ajax', [
'id' => 'brand_field',
'modal' => '#car-accessory-modal',
'url' => route('admin.util.select2.accessory-versions'),
])