@push('scripts')
    <script>
        let addCarAccessoryVue = new Vue({
            el: '#class-car-accessories',
            data: {
                class_accessory_list: @if (isset($class_accessory_list)) @json($class_accessory_list) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#class-car-accessories").show();
                },
                add: function() {
                    var _this = this;
                    var accessory_id = document.getElementById("accessory_field").value; 
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field').selectedOptions[0].text : '';
                    /* var accessory_version_id = document.getElementById("accessory_version_field").value; 
                    var accessory_version_text = (accessory_version_id) ? document.getElementById('accessory_version_field').selectedOptions[0].text : ''; */
                    var accessory_remark = document.getElementById("accessory_remark_field").value; 

                    var class_accessory = {};
                    if (accessory_id) {
                        class_accessory.accessory_id = accessory_id;
                        class_accessory.accessory_text = accessory_text;
                        /* class_accessory.accessory_version_id = accessory_version_id;
                        class_accessory.accessory_version_text = accessory_version_text; */
                        class_accessory.remark = accessory_remark;

                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            addCarAccessoryVue.$set(this.class_accessory_list, index, class_accessory);
                        } else {
                            _this.class_accessory_list.push(class_accessory);
                        }
                        _this.display();


                        $("#accessory_field").val('').change();
                        //$("#accessory_version_field").val('').change();
                        $("#accessory_remark_field").val('');
                        $("#modal-class-car-accessory").modal("hide");
                        this.edit_index = null;
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.class_accessory_list[index];
                    $("#accessory_field").val(temp.accessory_id).change();
                    //$("#accessory_version_field").val(temp.accessory_version_id).change();
                    $("#accessory_remark_field").val(temp.remark);

                    var defaultAccessoryOption = {
                            id: temp.accessory_id,
                            text: temp.accessory_text,
                    };
                    var tempAccessoryOption = new Option(defaultAccessoryOption.text, defaultAccessoryOption.id, false, false);
                    $("#accessory_field").append(tempAccessoryOption).trigger('change');

                    var defaultAccessoryVersionOption = {
                            id: temp.accessory_version_id,
                            text: temp.accessory_version_text,
                    };
                    /* var tempAccessoryVersionOption = new Option(defaultAccessoryVersionOption.text, defaultAccessoryVersionOption.id, false, false);
                    $("#accessory_version_field").append(tempAccessoryVersionOption).trigger('change'); */

                    this.edit_index = index;
                    $("#modal-class-car-accessory").modal("show");
                    $("#car-accessory-modal-label").html('แก้ไขข้อมูล');
                },
                remove: function(index) {
                    this.class_accessory_list.splice(index, 1);
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

        function editGuideLicense() {
            addCarAccessoryVue.saveEdit();
        }

        function openCarAccessoryModal() {
            $("#accessory_field").val('').change();
            $("#accessory_version_field").val('').change();
            $("#accessory_remark_field").val('');
            addCarAccessoryVue.setIndex();
            $("#car-accessory-modal-label").html('เพิ่มข้อมูล');
            $("#modal-class-car-accessory").modal("show");
        }
    </script>
@endpush
