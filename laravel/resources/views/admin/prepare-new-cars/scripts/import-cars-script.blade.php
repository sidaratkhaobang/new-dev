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
                edit_index: null
            },
            methods: {
                
                display: function(id,index) {
                    temp2 = this.dataset[id][index];
                    $('#engine_no').val(temp2.engine_no);
                    $('#chassis_no').val(temp2.chassis_no);
                    $('#installation_completed_date').val(temp2.installation_completed_date);    
                    $('#delivery_date').val(temp2.delivery_date);   
                    $('#delivery_place').val(temp2.delivery_place);     
                    $('#engine_no').prop('disabled', true);
                    $('#chassis_no').prop('disabled', true);
                    $('#installation_completed_date').prop('disabled', true);
                    $('#installation_completed_date').css("background-color", "#e9ecef");
                    var filter_accessory = this.filterAccessory(id);
                    this.accessory_list = filter_accessory;
                    $("#saveDetail").hide();
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
                    var status = 1;

                    var ob_import = {};
                    // if (engine_no && chassis_no) {
                        ob_import.status = status;
                        ob_import.engine_no = engine_no;
                        ob_import.chassis_no = chassis_no;
                        ob_import.installation_completed_date = installation_completed_date;
                        
                            id = _this.edit_id;
                            index = _this.edit_index;
                            // console.log(id);
                            // console.log(index);
                            console.log(ob_import.engine_no);
                        console.log(ob_import.chassis_no);
                            addImportCarVue.$set(this.dataset[id],index, ob_import);
                        $("#engine_no").val('');
                        $("#chassis_no").val('');
                        $("#installation_completed_date").val('');
                        $("#modal-edit-purchase").modal("hide");
                        this.edit_index = null;
                        this.edit_id = null;
                },
                confirmStatus: function(id,index) {
 

                    // temp = this.dataset[id][index]
                    // console.log(document.getElementById("engine_no").value);
                    temp = this.dataset[id][index];
                    
                    // console.log(index);
                    $('#engine_no').val(temp.engine_no);
                    $('#chassis_no').val(temp.chassis_no);
                    $('#installation_completed_date').val(temp.installation_completed_date);
                    // $('#status_car_line').val(temp.status);
                   
                    

                    var _this = this;
                    var status = 2;
                    var status_draft = 3;
                    var status_delivery = 1;
                    var engine_no = document.getElementById("engine_no").value;
                    var chassis_no = document.getElementById("chassis_no").value;
                    // var status_real = document.getElementById("status_car_line").value;
                    // console.log(engine_no);
                    var installation_completed_date = document.getElementById("installation_completed_date").value;
                    // console.log(status_real);
                    // var date_input = document.querySelectorAll('.delivery_date')[index];
                    // console.log(date_input);
                    // flatpickr(date_input);
                    var engine_no_id = '-'+id+'-'+index;

                    var ob_import = {};
                    // if (engine_no && chassis_no) {
                        ob_import.status = status;
                        ob_import.status_draft = status_draft;
                        ob_import.status_delivery = status_delivery;
                        ob_import.engine_no = engine_no;
                        ob_import.chassis_no = chassis_no;
                        ob_import.installation_completed_date = installation_completed_date;
                            addImportCarVue.$set(this.dataset[id],index, ob_import);
                            $('#engine_no'+engine_no_id).prop('disabled', true);
                            // $('#delivery_date'+engine_no_id).prop('disabled', true);
                            // $('#delivery_date'+engine_no_id).css("background-color", "#e9ecef");
                            // this.$forceUpdate();
                            $('#chassis_no').prop('disabled', true);
                            // js-flatpickr-enabled active
                     
                            // this.renderComponent = true;
                },
                // close: function(){
                //         $("#engine_no").val('');
                //         $("#chassis_no").val('');
                //         $("#installation_completed_date").val('');    
                //         $("#modal-edit-purchase").modal("hide");
                rejectStatus: function() {
                    // temp = this.dataset[id][index]
                    // console.log(document.getElementById("engine_no").value);
                    
                    var _this = this;
                    var engine_no = document.getElementById("engine_no").value;
                    
                    var chassis_no = document.getElementById("chassis_no").value;
                    var reject_reason = document.getElementById("reject_reason").value;
                    
                    var installation_completed_date = document.getElementById("installation_completed_date").value;
                    var status = 4;
                    var status_delivery = 1;

                    var ob_import = {};
                    // if (engine_no && chassis_no) {
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
                        
                        
                // },
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

                    this.edit_index = index;
                    this.edit_id = id;
                    var filter_accessory = this.filterAccessory(id);
                    this.accessory_list = filter_accessory;
                    $("#modal-edit-purchase").modal("show");
                    // $("#car-color-modal-label").html('แก้ไขข้อมูล');
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
        // addImportCarVue.display();

        function addDetail() {
            addImportCarVue.add();
        }

        function confirmStatus() {
            addImportCarVue.confirmStatus();
        }

        function rejectStatus() {
            addImportCarVue.rejectStatus();
        }

       

        // function hideCarColorModal() {
        //     $("#modal-car-class-color").modal("hide");
        // }
        // function openCarColorModal() {
        //     addCarClassColorVue.setIndex();
        //     $("#car-color-modal-label").html('เพิ่มข้อมูล');
        //     $("#standard_price_field").val('');
        //     $("#color_price_field").val('');
        //     $("#total_price_field").val('');
        //     $("#remark_field").val('');
        //     $("#color_field").val('').change();
        //     $("#modal-car-class-color").modal("show");
        // }

        // function backButton(){
        //     addImportCarVue.close();
        // }

        function editPurchaseModal() {
            $("#modal-edit-purchase").modal("show");
        }
        
        function openShareDealerModal() {
            $("#modal-import-cars").modal("show");
        }

        // $(document).ready(function(){
        //     $("#engine_no").keyup(function(){
        //         // $("#engine_no").val();
        //         var value = $( this ).val();
        //         $("#engine_no").text(value);
        //     });
        // });
   

        
    </script>
@endpush
