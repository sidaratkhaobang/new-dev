@push('scripts')
    <script>
        let addStatusVue = new Vue({
            el: '#status-list',
            data: {
                status: @if (isset($d->status)) @json($d->status) @else null @endif,
                status_list: @if (isset($status_list)) @json($status_list) @else [] @endif,
                location_case: @if (isset($d->location_case)) @json($d->location_case) @else null @endif,
                police_follow_status_list: @if (isset($police_follow_status_list)) @json($police_follow_status_list) @else [] @endif,
                court_follow_status_list: @if (isset($court_follow_status_list)) @json($court_follow_status_list) @else [] @endif,
                pending_delete_status_ids: [],
                edit_index: null
            },
            methods: {
                display: function() {
                    $("#status-list").show();
                },
                add: function() {
                    var _this = this;
                    var date = document.getElementById("temp_date").value;
                    var status = document.getElementById("temp_status").value; 
                    var status_text = (status) ? document.getElementById('temp_status').selectedOptions[0].text : '';
                    var appointment_date = document.getElementById("temp_appointment_date").value; 
                    var description = document.getElementById("temp_description").value; 
                    var data = {};

                    if (status) {
                        data.date = date;
                        data.status = status;
                        data.status_text = status_text;
                        data.appointment_date = appointment_date;
                        data.description = description;

                        if(_this.edit_index != null) {
                            index = _this.edit_index;
                            temp = this.status_list[index];
                            data.id = temp.id;
                            addStatusVue.$set(this.status_list, index, data);
                        } else {
                            _this.status_list.push(data);
                        }
                        _this.display();

                        $("#temp_date").val(null);
                        $("#temp_status").val(null).trigger('change');
                        $("#temp_appointment_date").val(null);
                        $("#temp_description").val(null);
                        $("#modal-status").modal("hide");
                        this.edit_index = null;
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.status_list[index];
                    this.setFollowStatus();
                    $("#temp_date").val(temp.date);
                    $("#temp_status").val(temp.status).trigger('change'); 
                    $("#temp_appointment_date").val(temp.appointment_date);
                    $("#temp_description").val(temp.description);
                    this.edit_index = index;
                    $("#modal-status").modal("show");
                    $("#status-modal-label").html('แก้ไขข้อมูลการติดตาม');
                },
                remove: function(index) {
                    if (this.status_list[index] && this.status_list[index].id) {
                        this.pending_delete_status_ids.push(this.status_list[index].id);
                    }
                    this.status_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                setFollowStatus: function(_all = false) {
                    if (this.location_case === '{{ LitigationLocationEnum::POLICE_STATION }}') {
                        var options = this.police_follow_status_list;
                        $("#temp_status").select2({
                            data: options
                        })
                    }

                    if (this.location_case === '{{ LitigationLocationEnum::COURT }}') {
                        var options = this.court_follow_status_list;
                        $("#temp_status").select2({
                            data: options
                        })
                    }
                },
                editable: function(item) {
                    can_edit = true;
                    if (this.status != '{{ LitigationStatusEnum::PENDING }}') {
                        can_edit = false;
                    }
                    if (!item.id) {
                        can_edit = true;
                    }
                    return can_edit;
                },
                formatDate(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-US', options);
                    return formattedDate;
                },
            },
            props: ['title'],
        });
        addStatusVue.display();

        function addStatus() {
            addStatusVue.add();
        }

        function hideSatusModal() {
            $("#modal-status").modal("hide");
        }
        function openStatusModal() {
            addStatusVue.setIndex();
            addStatusVue.setFollowStatus();
            $("#status-modal-label").html('เพิ่มข้อมูลการติดตาม');
            $("#temp_date").val(null);
            $("#temp_status").val(null).trigger('change');
            $("#temp_appointment_date").val(null);
            $("#temp_description").val(null);
            $("#modal-status").modal("show");

        }
    </script>
@endpush
