@push('scripts')
    <script>
        let addCarSlotVue = new Vue({
            el: '#car-slots',
            data: {
                car_slot_list: @if (isset($car_slot_list))
                    @json($car_slot_list)
                @else
                    []
                @endif ,
                edit_index: null,
                sum_total_slot: @if (isset($sum_total_slot))
                    @json($sum_total_slot)
                @else
                    0
                @endif ,
                sum_available_car_slot_count: @if (isset($sum_available_car_slot_count))
                    @json($sum_available_car_slot_count)
                @else
                    0
                @endif ,
                sum_unavailable_car_slot_count: @if (isset($sum_unavailable_car_slot_count))
                    @json($sum_unavailable_car_slot_count)
                @else
                    0
                @endif ,
                car_group_list: @if (isset($car_group_list))
                    @json($car_group_list)
                @else
                    []
                @endif ,
                zone_type_list: @if (isset($zone_type_list))
                    @json($zone_type_list)
                @else
                    []
                @endif ,
                deleted_area_list: [],
                delete_car_park_list: [],
                add_car_park_list: [],
                car_list: []
            },
            mounted() {
                this.genArrayCar();
                this.setTotalSlot();
            },
            watch: {
                car_slot_list: function(new_cal_slot_list, old_cal_slot_list) {
                    var _this = this;
                    _this.sum_total_slot = 0;
                    _this.sum_available_car_slot_count = 0;
                    _this.sum_unavailable_car_slot_count = 0;
                    new_cal_slot_list.forEach(function(item) {
                        _this.sum_available_car_slot_count += item.available_car_slot_count;
                        _this.sum_unavailable_car_slot_count += item.unavailable_car_slot_count;
                        _this.sum_total_slot += item.total_slot;
                    });
                    this.setTotalSlot();
                },
            },
            methods: {
                display: function() {
                    $("#car-slots").show();
                },
                add: function() {
                    var _this = this;
                    var start_slot_no = parseInt($("#start_slot_no_field").val());
                    var end_slot_no = parseInt($("#end_slot_no_field").val());
                    var car_groups = $("#car_group_id").val();
                    var zone_type = $("#zone_type_id").val();
                    var car_group_data = $('#car_group_id').select2('data');
                    var car_group_text = [];
                    car_group_data.forEach(function(item) {
                        car_group_text.push(item.text);
                    });
                    var area_size = $('input[name="area_size"]:checked').val();
                    var area_size_text = $("input[name='area_size']:checked").data('name');
                    var zone_type = $("#zone_type_id").val();
                    var zone_type_name = (zone_type) ? document.getElementById('zone_type_id').selectedOptions[0].text : '';

                    validated_result = this.validateCarNumber(start_slot_no, end_slot_no);
                    if (validated_result.status === false) {
                        return warningAlert(validated_result.message);
                    }

                    var car_slot_exist = false;
                    if (_this.edit_index != null) {
                        car_slot_exist = this.checkifEditCarSlotExisted(start_slot_no, end_slot_no, _this
                            .edit_index);
                        console.log(car_slot_exist);
                    } else {
                        car_slot_exist = this.checkifCarSlotExisted(start_slot_no, end_slot_no);
                        console.log(car_slot_exist);
                    }

                    if (car_slot_exist) {
                        return warningAlert("{{ __('parking_lots.slot_duplicate') }}");
                    }

                    var total_slot = parseInt(end_slot_no) - parseInt(start_slot_no) + 1;
                    var car_slot = {};
                    if (start_slot_no && end_slot_no && (car_groups.length > 0) && area_size && zone_type) {
                        car_slot.start_number = start_slot_no;
                        car_slot.end_number = end_slot_no;
                        car_slot.car_groups = car_groups;
                        car_slot.zone_type = zone_type;
                        car_slot.zone_type_name = zone_type_name;
                        car_slot.car_group_text = car_group_text.join(", ");
                        car_slot.area_size = area_size;
                        car_slot.area_size_text = area_size_text;
                        car_slot.total_slot = total_slot;
                        car_slot.available_car_slot_count = total_slot;
                        car_slot.unavailable_car_slot_count = 0;

                        if (_this.edit_index != null) {
                            index = _this.edit_index;
                            temp = this.car_slot_list[index];
                            car_slot.id = temp.id;
                            car_slot.status = temp.status;
                            this.compare(temp, car_slot);
                            addCarSlotVue.$set(this.car_slot_list, index, car_slot);
                        } else {
                            _this.car_slot_list.push(car_slot);
                        }
                        this.genArrayCar();
                        _this.display();

                        $("#start_slot_no_field").val('');
                        $("#end_slot_no_field").val('');
                        $("#car_group_id").val(null).change();
                        $("#zone_type_id").val(null).change();
                        $("#area_size").prop('checked', false);
                        $("#total_unavailable_slot").val('');
                        $("#total_available_slot").val('');
                        $("#modal-car-slot").modal("hide");
                        this.edit_index = null;
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.car_slot_list[index];
                    if (temp.unavailable_car_slot_count && temp.unavailable_car_slot_count > 0) {
                        return warningAlert("{{ __('parking_lots.edit_warning') }}");
                    }

                    $("#start_slot_no_field").val(temp.start_number);
                    $("#end_slot_no_field").val(temp.end_number);
                    $("#car_group_id").val(null).change();
                    $("#zone_type_id").val(null).change();
                    var defaultZoneTypeOption = {
                            id: temp.zone_type,
                            text: temp.zone_type_name,
                    };
                    var tempCarClassOption = new Option(defaultZoneTypeOption.text, defaultZoneTypeOption.id, true, true);
                    $("#zone_type_id").append(tempCarClassOption).trigger('change');
                    // var zone_types = $("#zone_type_id").val();
                    // $("#zone_type_id").val(temp.zone_types).change();
                    

                    temp.car_groups.forEach(car_group_id => {
                        car_group_data = this.car_group_list.find(o => o.id == car_group_id.trim());
                        var option = new Option(car_group_data.name, car_group_data.id, true, true);
                        $("#car_group_id").append(option).trigger('change');
                    })

                    $('input[name="area_size"][value="' + temp.area_size + '"]').prop('checked', true);
                    $("#total_slot").val(temp.total_slot);

                    this.edit_index = index;
                    $("#modal-car-slot").modal("show");
                    $("#car-slot-modal-label").html('แก้ไขข้อมูล');
                    this.setTotalSlot();
                },
                remove: function(index) {
                    temp = this.car_slot_list[index];
                    if (temp.unavailable_car_slot_count && temp.unavailable_car_slot_count > 0) {
                        return warningAlert("{{ __('parking_lots.car_park_area_delete_fail') }}");
                    }
                    this.deleted_area_list.push(temp.id);
                    this.car_slot_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                genArrayCar: function() {
                    this.car_list = [];
                    this.car_slot_list.forEach(item => {
                        var start = item.start_number;
                        var end = item.end_number;
                        for (let index = start; index <= end; index++) {
                            this.car_list.push(index);
                        }
                    });
                },
                setTotalSlot: function() {
                    $("#total_slot").val(this.sum_total_slot);
                },
                compare: function(old_obj, new_obj) {
                    old_start_number = old_obj.start_number;
                    new_start_number = new_obj.start_number;
                    old_end_number = old_obj.end_number;
                    new_end_number = new_obj.end_number;

                    if (old_start_number > new_start_number) {
                        for (let i = new_start_number; i < old_start_number; i++) {
                            this.add_car_park_list.push({
                                area_id: old_obj.id,
                                number: i
                            });
                        }
                    }
                    if (old_start_number < new_start_number) {
                        for (let i = old_start_number; i < new_start_number; i++) {
                            this.delete_car_park_list.push(i);
                        }
                    }
                    if (old_end_number > new_end_number) {
                        for (let i = new_end_number + 1; i <= old_end_number; i++) {
                            this.delete_car_park_list.push(i);
                        }
                    }
                    if (old_end_number < new_end_number) {
                        for (let i = old_end_number + 1; i <= new_end_number; i++) {
                            this.add_car_park_list.push({
                                area_id: old_obj.id,
                                number: i
                            });
                        }
                    }
                },
                validateCarNumber: function(start, end) {
                    var status = true;
                    var message = '';

                    if (start <= 0 || end <= 0) {
                        status = false;
                        message = 'เลขที่ช่องจอดต้องมากกว่า 0';
                    } else if (start > end) {
                        status = false;
                        message = 'เลขที่ช่องจอดเริ่มต้นต้องน้อยกว่าเลขที่ช่องจอดสิ้นสุด';
                    } else {}

                    return {
                        'status': status,
                        'message': message
                    };
                },
                checkifCarSlotExisted: function(start, end) {
                    var waiting_car_slot_list = [];
                    for (let index = start; index < end; index++) {
                        waiting_car_slot_list.push(index);
                    }
                    const found = this.car_list.some(i => waiting_car_slot_list.includes(i));
                    return found;
                },
                checkifEditCarSlotExisted: function(start, end, index_object) {
                    const all_car_list = [...this.car_slot_list];
                    all_car_list.splice(index_object, 1);

                    new_car_slot_list = [];
                    all_car_list.forEach(item => {
                        var start = item.start_number;
                        var end = item.end_number;
                        for (let index = start; index <= end; index++) {
                            new_car_slot_list.push(index);
                        }
                    });

                    var waiting_car_slot_list = [];
                    for (let index = start; index <= end; index++) {
                        waiting_car_slot_list.push(index);
                    }

                    const found = new_car_slot_list.some(i => waiting_car_slot_list.includes(i));
                    return found;
                },
                gotoShiftCarRoute: function(id) {
                    route = '{{ route('admin.parking-lots.show-shift-cars', ['car_park_area_id' => 'item']) }}'
                    var url = new URL(route.replace('item', id));
                    return url.href;
                },
            },
            props: ['title'],
        });
        addCarSlotVue.display();

        function addCarSlot() {
            addCarSlotVue.add();
        }

        function openCarSlotModal() {
            $("#start_slot_no_field").val('');
            $("#end_slot_no_field").val('');
            $("#car_group_id").val(null).change();
            $("#zone_type_id").val(null).change();
            $("#area_size").prop('checked', false);
            $("#total_unavailable_slot").val('');
            $("#total_available_slot").val('');
            addCarSlotVue.setIndex();
            $("#car-slot-modal-label").html('เพิ่มข้อมูล');
            $("#modal-car-slot").modal("show");
        }

        $("#total_slot").prop('disabled', true);
    </script>
@endpush
