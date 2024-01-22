@push('scripts')
    <script>
        let addCarClassColorVue = new Vue({
            el: '#car-class-colors',
            data: {
                class_color_list: @if (isset($car_class_color_list)) @json($car_class_color_list) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#car-class-colors").show();
                },
                add: function() {
                    var _this = this;
                    var standard_price = document.getElementById("standard_price_field").value;
                    var color_price = document.getElementById("color_price_field").value; 
                    var color_id = document.getElementById("color_field").value; 
                    var color_text = (color_id) ? document.getElementById('color_field').selectedOptions[0].text : '';
                    var total_price = document.getElementById("total_price_field").value; 
                    var remark = document.getElementById("remark_field").value; 

                    var class_color = {};
                    if (standard_price && color_price && color_id) {
                        class_color.standard_price = standard_price;
                        class_color.color_price = color_price;
                        class_color.car_color_id = color_id;
                        class_color.color_text = color_text;
                        class_color.total_price = total_price;
                        class_color.remark = remark;
                        
                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            addCarClassColorVue.$set(this.class_color_list, index, class_color);
                        } else {
                            _this.class_color_list.push(class_color);
                        }
                        _this.display();

                        $("#standard_price_field").val('');
                        $("#color_price_field").val('');
                        $("#total_price_field").val('');
                        $("#remark_field").val('');
                        $("#color_field").val('').change();
                        $("#modal-car-class-color").modal("hide");
                        this.edit_index = null;
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.class_color_list[index];
                    $('#class_car_color_index').val(index);
                    $("#standard_price_field").val(temp.standard_price);
                    $("#color_price_field").val(temp.color_price);
                    $("#total_price_field").val(temp.total_price);
                    $("#remark_field").val(temp.remark);
                    $("#color_field").val(temp.car_color_id).change(); 
                    var defaultColorOption = {
                            id: temp.car_color_id,
                            text: temp.color_text,
                    };
                    var tempColorOption = new Option(defaultColorOption.text, defaultColorOption.id, false, false);
                    $("#color_field").append(tempColorOption).trigger('change');

                    this.edit_index = index;
                    $("#modal-car-class-color").modal("show");
                    $("#car-color-modal-label").html('แก้ไขข้อมูล');
                },
                remove: function(index) {
                    this.class_color_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                }
            },
            props: ['title'],
        });
        addCarClassColorVue.display();

        function addCarColor() {
            addCarClassColorVue.add();
        }

        function hideCarColorModal() {
            $("#modal-car-class-color").modal("hide");
        }
        function openCarColorModal() {
            addCarClassColorVue.setIndex();
            $("#car-color-modal-label").html('เพิ่มข้อมูล');
            $("#standard_price_field").val('');
            $("#color_price_field").val('');
            $("#total_price_field").val('');
            $("#remark_field").val('');
            $("#color_field").val('').change();
            $("#modal-car-class-color").modal("show");
        }

        $('#total_price_field').prop('disabled', true);

        $("#standard_price_field").on("input", function() {
            var a = (isNaN(parseFloat($(this).val()).toFixed(2))) ? 0 : parseFloat($(this).val()).toFixed(2),
                b = (isNaN(parseFloat($('#color_price_field').val()).toFixed(2))) ? 0 : parseFloat($('#color_price_field').val()).toFixed(2);
            total_price = (parseFloat(parseFloat(a) + parseFloat(b)).toFixed(2));
            $('#total_price_field').val(total_price);
        });

        $("#color_price_field").on("input", function() {
            var a = (isNaN(parseFloat($(this).val()).toFixed(2))) ? 0 : parseFloat($(this).val()).toFixed(2),
                b = (isNaN(parseFloat($('#standard_price_field').val()).toFixed(2))) ? 0 : parseFloat($('#standard_price_field').val()).toFixed(2);
            total_price = (parseFloat(parseFloat(a) + parseFloat(b)).toFixed(2));
            $('#total_price_field').val(total_price);
        });
    </script>
@endpush
