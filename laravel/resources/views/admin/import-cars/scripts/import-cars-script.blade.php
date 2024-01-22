@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script>

        let addImportCarVue = new Vue({
            el: '#import-cars',
            data: {
                accessory_pr: @if (isset($accessory_pr)) @json($accessory_pr) @else [] @endif,
                accessory_list: [],
                //dataset: [{key:"",engine_no:"123",engine_no2:"123"},{key:"",engine_no:"321",engine_no2:"123"},{key:"",engine_no:"546",engine_no2:"123"},{key:"",engine_no:"678",engine_no2:"123"},{key:"",engine_no:"234",engine_no2:"123"},{key:"",engine_no:"789",engine_no2:"123"}],
                dataset: @json($object),
                car_park_transfer: @if (isset($car_park_transfer)) @json($car_park_transfer) @else [] @endif,
                inspection_job: @if (isset($inspection_job)) @json($inspection_job) @else [] @endif,
                edit_index: null
            },
            methods: {

                display: function(id,index) {
                    temp2 = this.dataset[id][index];
                    $('#engine_no').val(temp2.engine_no);
                    $('#chassis_no').val(temp2.chassis_no);
                    $('#installation_completed_date').val(temp2.installation_completed_date);
                    $('#delivery_date').val(temp2.delivery_date);
                    $('#remark_line').val(temp2.remark_line);
                    $('#delivery_place').val(temp2.delivery_place);
                    if(temp2.verification_date != null || ''){
                        $('#verification_date').html('วันที่ Dealer บันทึกข้อมูล : ' + temp2.verification_date);   // วันที่ dealer ยืนยัน
                    }else{
                        $('#verification_date').html('วันที่ Dealer บันทึกข้อมูล : -');
                    }
                    if(temp2.reject_reason != null || ''){
                        $('#reason').html('*เหตุผลที่ต้องแก้ไข : ' + temp2.reject_reason);
                        $('#reason').css("color","#e04f1a");
                    }else{
                        $('#reason').html('');
                    }
                    $('#engine_no').prop('disabled', true);
                    $('#chassis_no').prop('disabled', true);
                    $('#remark_line').prop('disabled', true);
                    $('#installation_completed_date').prop('disabled', true);
                    $('#installation_completed_date').css("background-color", "#e9ecef");
                    var filter_accessory = this.filterAccessory(id);
                    this.accessory_list = filter_accessory;

                    var car_worksheet_no = this.filterCarParkTransfer(temp2.id)
                    $('#car_entry').val(car_worksheet_no);
                    var job_worksheet_no = this.filterInspectionJob(temp2.id);
                    $('#car_inspection').val(job_worksheet_no);

                    $("#saveDetail").hide();
                    $("#saveRemark").hide();
                    $("#modal-edit-purchase").modal("show");

                },
                test: function(vue_arr){
                    this.dataset = vue_arr;
                },
                filterAccessory: function(id){
                    var clone_accessory = [...this.accessory_pr];
                    return clone_accessory.filter(e => e.po_line_id == id);
                },
                add: function() {
                    var _this = this;

                    var engine_no = document.getElementById("engine_no").value;
                    console.log(engine_no);
                    var chassis_no = document.getElementById("chassis_no").value;
                    var installation_completed_date = document.getElementById("installation_completed_date").value;
                    var status = '{{\App\Enums\ImportCarLineStatusEnum::PENDING}}';
                    var status_delivery = '{{\App\Enums\ImportCarLineStatusEnum::PENDING}}';

                    var ob_import = {};
                    ob_import.status = status;
                    ob_import.engine_no = engine_no;
                    ob_import.chassis_no = chassis_no;
                    ob_import.status_delivery = status_delivery;
                    ob_import.installation_completed_date = installation_completed_date;

                    id = _this.edit_id;
                    index = _this.edit_index;
                    addImportCarVue.$set(this.dataset[id],index, ob_import);
                    $("#engine_no").val('');
                    $("#chassis_no").val('');
                    $("#installation_completed_date").val('');
                    $("#modal-edit-purchase").modal("hide");
                    this.edit_index = null;
                    this.edit_id = null;

                },
                confirmStatus: function(id,index) {
                    temp = this.dataset[id][index];
                    $('#engine_no').val(temp.engine_no);
                    $('#chassis_no').val(temp.chassis_no);
                    $('#installation_completed_date').val(temp.installation_completed_date);
                    var loggedIn = "{{{ (Auth::user()) ? Auth::user() : '' }}}";


                    if(loggedIn){
                        var status_draft = '{{\App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA}}';
                    }else{
                        var status_draft = '{{\App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA}}';
                    }


                    var _this = this;
                    var status = '{{\App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW}}';
                    var status_delivery = '{{\App\Enums\ImportCarLineStatusEnum::PENDING}}';
                    var engine_no = document.getElementById("engine_no").value;
                    var chassis_no = document.getElementById("chassis_no").value;
                    var installation_completed_date = document.getElementById("installation_completed_date").value;
                    var no_id = '-'+id+'-'+index;

                    var ob_import = {};
                        ob_import.status = status;
                        ob_import.status_draft = status_draft;
                        ob_import.status_delivery = status_delivery;
                        ob_import.engine_no = engine_no;
                        ob_import.chassis_no = chassis_no;
                        ob_import.installation_completed_date = installation_completed_date;
                            addImportCarVue.$set(this.dataset[id],index, ob_import);
                            $('#engine_no'+no_id).prop('disabled', true);
                            $('#chassis_no'+no_id).prop('disabled', true);
                            $('#installation_completed_date'+no_id).prop('disabled', true);
                            $('#installation_completed_date'+no_id).css("background-color", "#e9ecef");
                },
                rejectStatus: function() {
                    var _this = this;
                    var engine_no = document.getElementById("engine_no").value;
                    var chassis_no = document.getElementById("chassis_no").value;
                    var reject_reason = document.getElementById("reject_reason").value;
                    var installation_completed_date = document.getElementById("installation_completed_date").value;
                    var status = '{{\App\Enums\ImportCarLineStatusEnum::REJECT_DATA}}';
                    var status_delivery = '{{\App\Enums\ImportCarLineStatusEnum::PENDING}}';

                    var ob_import = {};
                        ob_import.status = status;
                        ob_import.status_delivery = status_delivery;
                        ob_import.engine_no = engine_no;
                        ob_import.chassis_no = chassis_no;
                        ob_import.reject_reason = reject_reason;
                        ob_import.installation_completed_date = installation_completed_date;

                            id = _this.edit_id;
                            index = _this.edit_index;
                            console.log(id);
                            console.log(index);
                            console.log(reject_reason);
                        if(reject_reason){
                            addImportCarVue.$set(this.dataset[id],index, ob_import);
                            $("#engine_no").val('');
                            $("#chassis_no").val('');
                            $("#reject_reason").val('');
                            $("#installation_completed_date").val('');
                            $("#modal-reject-display").modal("hide");
                        }else{
                            errorAlert('ระบุเหตุผลที่ต้องแก้ไขข้อมูล');
                        }
                        // this.edit_index = null;
                        // this.edit_id = null;

                },
                rejectDisplay: function(id,index) {
                    temp = this.dataset[id][index];
                    $('#engine_no').val(temp.engine_no);
                    $('#chassis_no').val(temp.chassis_no);
                    $('#installation_completed_date').val(temp.installation_completed_date);
                    $('#delivery_date').val(temp.delivery_date);
                    $('#delivery_place').val(temp.delivery_place);
                    this.edit_index = index;
                    this.edit_id = id;

                    $("#modal-reject-display").modal("show");

                },

                edit: function(id,index) {
                    temp = this.dataset[id][index];
                    $('#engine_no').val(temp.engine_no);
                    $('#chassis_no').val(temp.chassis_no);
                    $('#installation_completed_date').val(temp.installation_completed_date);
                    $('#delivery_date').val(temp.delivery_date);
                    $('#delivery_place').val(temp.delivery_place);
                    $('#engine_no').prop('disabled', false);
                    $('#chassis_no').prop('disabled', false);
                    $('#installation_completed_date').prop('disabled', false);
                    $('#installation_completed_date').css("background-color", "");
                    $("#saveDetail").show();
                    $("#saveRemark").hide();

                    this.edit_index = index;
                    this.edit_id = id;
                    var filter_accessory = this.filterAccessory(id);
                    this.accessory_list = filter_accessory;
                    $("#modal-edit-purchase").modal("show");
                    // $("#car-color-modal-label").html('แก้ไขข้อมูล');
                },
                editRemark: function(id,index) {
                    temp = this.dataset[id][index];
                    $('#engine_no').val(temp.engine_no).prop('disabled', true);
                    $('#chassis_no').val(temp.chassis_no).prop('disabled', true);
                    $('#installation_completed_date').val(temp.installation_completed_date).prop('disabled', true);
                    $('#delivery_date').val(temp.delivery_date).prop('disabled', true);
                    $('#delivery_place').val(temp.delivery_place).prop('disabled', true);
                    $('#remark_line').val(temp.remark_line).prop('disabled', false);
                    $("#saveDetail").hide();
                    $("#saveRemark").show();

                    this.edit_index = index;
                    this.edit_id = id;
                    var filter_accessory = this.filterAccessory(id);
                    this.accessory_list = filter_accessory;

                    var car_worksheet_no = this.filterCarParkTransfer(temp.id)
                    $('#car_entry').val(car_worksheet_no);
                    var job_worksheet_no = this.filterInspectionJob(temp.id);
                    $('#car_inspection').val(job_worksheet_no);

                    $("#modal-edit-purchase").modal("show");
                },
                addRemark: function() {
                    var _this = this;
                    id = _this.edit_id;
                    index = _this.edit_index;
                    temp = this.dataset[id][index];
                    var remark_line = document.getElementById("remark_line").value;

                    var ob_import = {};
                    ob_import.status = temp.status;
                    ob_import.status_delivery = temp.status_delivery;
                    ob_import.engine_no = temp.engine_no;
                    ob_import.chassis_no = temp.chassis_no;
                    ob_import.installation_completed_date = temp.installation_completed_date;
                    ob_import.delivery_place = temp.delivery_place;
                    ob_import.delivery_date = temp.delivery_date;
                    ob_import.remark_line = remark_line;
                    ob_import.id = temp.id;

                    addImportCarVue.$set(this.dataset[id],index, ob_import);
                    $("#remark_line").val('');
                    $("#modal-edit-purchase").modal("hide");
                    this.edit_index = null;
                    this.edit_id = null;
                },
                filterCarParkTransfer: function(id) {
                    var car_worksheet_no = [];
                    var car_park_transfers = this.car_park_transfer.filter(obj => obj.car_id == id);
                    car_park_transfers.forEach((e) => {
                        car_worksheet_no.push(e.worksheet_no);
                    });
                    return car_worksheet_no;
                },
                filterInspectionJob: function(id) {
                    var job_worksheet_no = [];
                    var inspection_jobs = this.inspection_job.filter(obj => obj.car_id == id);
                    inspection_jobs.forEach((e) => {
                        job_worksheet_no.push(e.worksheet_no);
                    });
                    return job_worksheet_no;
                },
                remove: function(index) {
                    this.class_color_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                redirectPage: function(item_id, text) {
                    if (text == 'car_park_transfer') {
                        route = "{{ route('admin.car-park-transfers.show', ['car_park_transfer' => 'car_park_transfer_id']) }}";
                        route = route.replace('car_park_transfer_id', item_id);
                    }else if (text == 'inspection_job') {
                        route = "{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => 'inspection_job_step_id']) }}";
                        route = route.replace('inspection_job_step_id', item_id);
                    }
                    var url = new URL(route);
                    return url.href;
                },
            },
            props: ['title'],
        });
        // addImportCarVue.display();

        function addDetail() {
            addImportCarVue.add();
        }

        function addRemark() {
            addImportCarVue.addRemark();
        }

        function confirmStatus() {
            addImportCarVue.confirmStatus();
        }

        function rejectStatus() {
            addImportCarVue.rejectStatus();
        }

        function editPurchaseModal() {
            $("#modal-edit-purchase").modal("show");
        }

    </script>
@endpush
