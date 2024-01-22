@push('scripts')
    <script>
        let addBranchLocationVue = new Vue({
            el: '#branch-locations',
            data: {
                branch_location_list: @if (isset($branch_location_list)) @json($branch_location_list) @else [] @endif,
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#branch-locations").show();
                },
                add: function() {
                    var _this = this;
                    var location_group_id = document.getElementById('location_group_field').value;
                    var location_group_text = (location_group_id) ? document.getElementById('location_group_field').selectedOptions[0].text : '';
                    var location_id = document.getElementById('location_field').value; 
                    var location_text = (location_id) ? document.getElementById('location_field').selectedOptions[0].text : '';
                    var origin_field = $('input[name="origin_field"]:checked').val();
                    var stopover_field = $('input[name="stopover_field"]:checked').val();
                    var destination_field = $('input[name="destination_field"]:checked').val();

                    var branch_location = {};
                    const location_exist = this.branch_location_list.some(function(el) { return el.location_id === location_id;});
                    var temp_data = this.branch_location_list[_this.edit_index];
                    var temp_location = temp_data ? temp_data.location_id : '';
                    if (location_exist && temp_location != location_id ) {
                        return warningAlert("{{ __('branches.location_exist') }}");
                    }
                    if (location_id) {
                        branch_location.location_group_id = location_group_id;
                        branch_location.location_group_text = location_group_text;
                        branch_location.location_id = location_id;
                        branch_location.location_text = location_text;
                        branch_location.can_origin = origin_field;
                        branch_location.can_stopover = stopover_field;
                        branch_location.can_destination = destination_field;
                        
                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            addBranchLocationVue.$set(this.branch_location_list, index, branch_location);
                        } else {
                            _this.branch_location_list.push(branch_location);
                        }
                        _this.display();

                        $('#location_group_field').val('').change();
                        $('#location_field').val('').change();
                        $('input[name="origin_field"][value="0"]').prop('checked', true);
                        $('input[name="stopover_field"][value="0"]').prop('checked', true);
                        $('input[name="destination_field"][value="0"]').prop('checked', true);
                        $("#modal-branch-location").modal("hide");
                        this.edit_index = null;
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.branch_location_list[index];

                    if (temp.location_group_id) {
                        $("#location_group_field").val(temp.location_group_id).change(); 
                        var defaultLocationGroupOption = {
                                id: temp.location_group_id,
                                text: temp.location_group_text,
                        };
                        var tempLocationGroupOption = new Option(defaultLocationGroupOption.text, defaultLocationGroupOption.id, true, true);
                        $("#location_group_field").append(tempLocationGroupOption).trigger('change');

                    }

                    $("#location_field").val(temp.location_id).change(); 
                    var defaultLocationOption = {
                            id: temp.location_id,
                            text: temp.location_text,
                    };
                    var tempLocationOption = new Option(defaultLocationOption.text, defaultLocationOption.id, true, true);
                    $("#location_field").append(tempLocationOption).trigger('change');

                    $('input[name="origin_field"][value="'+ temp.can_origin +'"]').prop('checked', true);
                    $('input[name="stopover_field"][value="'+ temp.can_stopover +'"]').prop('checked', true);
                    $('input[name="destination_field"][value="'+ temp.can_destination +'"]').prop('checked', true);
                    
                    this.edit_index = index;
                    $("#modal-branch-location").modal("show");
                    $("#branch-location-modal-label").html('แก้ไขข้อมูล');
                },
                remove: function(index) {
                    this.branch_location_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                getYesNoText: function($value) {
                    return ($value == true) ? 'ใช่' : 'ไม่ใช่';
                },
            },
            props: ['title'],
        });
        addBranchLocationVue.display();

        function addBranchLocation() {
            addBranchLocationVue.add();
        }

        function openBranchLocationModal() {
            addBranchLocationVue.setIndex();
            $('#location_group_field').val('').change();
            $('#location_field').val('').change();
            $('input[name="origin_field"][value="0"]').prop('checked', true);
            $('input[name="stopover_field"][value="0"]').prop('checked', true);
            $('input[name="destination_field"][value="0"]').prop('checked', true);
            $("#branch-location-modal-label").html('เพิ่มข้อมูล');
            $("#modal-branch-location").modal("show");
        }
    </script>
@endpush
