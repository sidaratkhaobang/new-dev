@push('scripts')
    <script>
        let addNoticeVue = new Vue({
            el: '#notice-list',
            data: {
                notice_list: @if (isset($notice_list)) @json($notice_list) @else [] @endif,
                pending_delete_notice_ids: [],
                edit_index: null,
            },
            methods: {
                display: function() {
                    $("#notice-list").show();
                },
                add: function() {
                    var _this = this;
                    var delivery_date = document.getElementById("temp_delivery_date").value;
                    var rp_no = document.getElementById("temp_rp_no").value;
                    var receive_date = document.getElementById("temp_receive_date").value;
                    var recipient_name = document.getElementById("temp_recipient_name").value;

                    var notice = {};
                    if (delivery_date && rp_no && receive_date && recipient_name) {
                        notice.delivery_date = delivery_date;
                        notice.rp_no = rp_no;
                        notice.receive_date = receive_date;
                        notice.recipient_name = recipient_name;

                        if (_this.edit_index != null) {
                            index = _this.edit_index;
                            temp = this.notice_list[index];
                            notice.id = temp.id;
                            addNoticeVue.$set(this.notice_list, index, notice);
                        } else {
                            _this.notice_list.push(notice);
                        }
                        _this.display();

                        $("#temp_delivery_date").val(null);
                        $("#temp_rp_no").val(null);
                        $("#temp_receive_date").val(null);
                        $("#temp_recipient_name").val(null);
                        $("#modal-notice").modal("hide");
                        this.edit_index = null;
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                edit: function(index) {
                    var temp = null;
                    var _this = this;
                    temp = this.notice_list[index];
                    $("#temp_delivery_date").val(temp.delivery_date);
                    $("#temp_rp_no").val(temp.rp_no);
                    $("#temp_receive_date").val(temp.receive_date);
                    $("#temp_recipient_name").val(temp.recipient_name);
                    this.edit_index = index;
                    $("#modal-notice").modal("show");
                    $("#notice-modal-label").html('แก้ไขหนังสือทวงถาม');
                },
                remove: function(index) {
                    if (this.notice_list[index] && this.notice_list[index].id) {
                        this.pending_delete_notice_ids.push(this.notice_list[index].id);
                    }
                    this.notice_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                numberWithCommas: function(x) {
                    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
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
                // editable: function(item) {
                //     can_edit = true;
                //     if (this.status != '{{ LitigationStatusEnum::PENDING }}') {
                //         can_edit = false;
                //     }
                //     if (!item.id) {
                //         can_edit = true;
                //     }
                //     return can_edit;
                // },
            },
            props: ['title'],
        });
        addNoticeVue.display();

        function addNotice() {
            addNoticeVue.add();
        }

        function hideNoticeModal() {
            $("#modal-notice").modal("hide");
        }

        function openNoticeModal() {
            addNoticeVue.setIndex();
            $("#notice-modal-label").html('เพิ่มหนังสือทวงถาม');
            $("#temp_delivery_date").val(null);
            $("#temp_rp_no").val(null);
            $("#temp_receive_date").val(null);
            $("#temp_recipient_name").val(null);
            $("#modal-notice").modal("show");
        }
    </script>
@endpush
