@push('scripts')
    <script>
        let addDeliverGoodVue = new Vue({
            el: '#deliver-goods',
            data: {
                deliver_good_list: @if (isset($deliver_good_list)) @json($deliver_good_list) @else [] @endif,
                edit_index: null,
                mode: null,
                pending_delete_deliver_good_ids: [],
            },
            methods: {
                display: function() {
                    $("#deliver-goods").show();
                },
                addDeliverGood: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#delivery-good-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#deliver_good_brand_id").val(null).trigger('change');
                    $("#deliver_good_class_id").val(null).trigger('change');
                    $("#deliver_good_color_id").val(null).trigger('change');
                    $("#deliver_good_license_plate").val('');
                    $("#deliver_good_chassis_no").val('');
                    window.myDropzone[0].removeAllFiles(true);
                },
                setOption: function(_id, _text) {
                    var defaultOption = {
                        id: _id,
                        text: _text,
                    };
                    var tempOption = new Option(defaultOption.text, defaultOption.id, true, true);
                    return tempOption;
                },
                loadModalData: function(index){
                    var temp = null;
                    temp = this.deliver_good_list[index];
                    
                    temp_deliver_good_brand_option = this.setOption(temp.deliver_good_brand_id, temp.deliver_good_brand_text);
                    $("#deliver_good_brand_id").append(temp_deliver_good_brand_option).trigger('change');

                    temp_deliver_good_class_option = this.setOption(temp.deliver_good_class_id, temp.deliver_good_class_text);
                    $("#deliver_good_class_id").append(temp_deliver_good_class_option).trigger('change');

                    temp_deliver_good_color_option = this.setOption(temp.deliver_good_color_id, temp.deliver_good_color_text);
                    $("#deliver_good_color_id").append(temp_deliver_good_color_option).trigger('change');

                    $("#deliver_good_license_plate").val(temp.deliver_good_license_plate);
                    $("#deliver_good_chassis_no").val(temp.deliver_good_chassis_no);

                    // clear file myDropzone
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];

                    // load file license
                    var deliver_good_files = temp.deliver_good_files;
                    if(deliver_good_files.length > 0){
                        deliver_good_files.forEach( item => {
                            let mockFile = {...item};
                            window.myDropzone[0].emit( "addedfile", mockFile );
                            window.myDropzone[0].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[0].files.push( mockFile );
                        });
                    }
                },
                openModal: function(){
                    $("#modal-deliver-good").modal("show");
                },
                hideModal: function(){
                    $("#modal-deliver-good").modal("hide");
                },
                save: function() {
                    var _this = this;
                    if(_this.mode == 'edit') {
                        var index = _this.edit_index;
                        _this.saveEdit(index);
                    }else {
                        _this.saveAdd();
                    }
                },
                getDataFromModalAdd: function(){
                    var deliver_good_brand_id = document.getElementById("deliver_good_brand_id").value;
                    var deliver_good_brand_text = (deliver_good_brand_id) ? document.getElementById('deliver_good_brand_id').selectedOptions[0].text : '';
                    var deliver_good_class_id = document.getElementById("deliver_good_class_id").value;
                    var deliver_good_class_text = (deliver_good_class_id) ? document.getElementById('deliver_good_class_id').selectedOptions[0].text : '';
                    var deliver_good_color_id = document.getElementById("deliver_good_color_id").value;
                    var deliver_good_color_text = (deliver_good_color_id) ? document.getElementById('deliver_good_color_id').selectedOptions[0].text : '';
                    var deliver_good_license_plate = document.getElementById("deliver_good_license_plate").value;
                    var deliver_good_chassis_no = document.getElementById("deliver_good_chassis_no").value;
                    var deliver_good_raw_files = window.myDropzone[0].files;
                    var deliver_good_files = deliver_good_raw_files.map( item => this.formatFile(item) );
                    // var id = null;
                    return {
                        // id: id,
                        deliver_good_brand_id: deliver_good_brand_id,
                        deliver_good_brand_text: deliver_good_brand_text,
                        deliver_good_class_id: deliver_good_class_id,
                        deliver_good_class_text: deliver_good_class_text,
                        deliver_good_color_id: deliver_good_color_id,
                        deliver_good_color_text: deliver_good_color_text,
                        deliver_good_license_plate: deliver_good_license_plate,
                        deliver_good_chassis_no: deliver_good_chassis_no,
                        deliver_good_files: deliver_good_files,
                        pending_delete_deliver_good_files: [],
                    };
                },
                validateDataObject: function(deliver_good){
                    // if (deliver_good.name) {
                    //     return true;
                    // } else {
                    //     return false;
                    // }
                    return true;
                },
                saveAdd: function(){
                    var deliver_good = this.getDataFromModalAdd();
                    if (this.validateDataObject(deliver_good)) {
                        this.deliver_good_list.push(deliver_good);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {

                    var deliver_good_brand_id = document.getElementById("deliver_good_brand_id").value;
                    var deliver_good_brand_text = (deliver_good_brand_id) ? document.getElementById('deliver_good_brand_id').selectedOptions[0].text : '';
                    var deliver_good_class_id = document.getElementById("deliver_good_class_id").value;
                    var deliver_good_class_text = (deliver_good_class_id) ? document.getElementById('deliver_good_class_id').selectedOptions[0].text : '';
                    var deliver_good_color_id = document.getElementById("deliver_good_color_id").value;
                    var deliver_good_color_text = (deliver_good_color_id) ? document.getElementById('deliver_good_color_id').selectedOptions[0].text : '';
                    var deliver_good_license_plate = document.getElementById("deliver_good_license_plate").value;
                    var deliver_good_chassis_no = document.getElementById("deliver_good_chassis_no").value;
                    var deliver_good = this.deliver_good_list[index];
                    // load files in modal dropzone
                    var modal_deliver_good_files = window.myDropzone[0].files;
                    var deliver_good_files = modal_deliver_good_files.map( item => this.formatFile(item) );

                    // get all deleted files
                    var deleted_deliver_good_files = window.myDropzone[0].options.params.js_delete_files;
                    deleted_deliver_good_files  = deleted_deliver_good_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_deliver_good_media_ids = deleted_deliver_good_files.map((file) => {
                        return file.media_id;
                    });

                    deliver_good.deliver_good_brand_id = deliver_good_brand_id;
                    deliver_good.deliver_good_class_id = deliver_good_class_id;
                    deliver_good.deliver_good_color_id = deliver_good_color_id;
                    deliver_good.deliver_good_license_plate = deliver_good_license_plate;
                    deliver_good.deliver_good_chassis_no = deliver_good_chassis_no;
                    deliver_good.deliver_good_files = deliver_good_files;
                    deliver_good.pending_delete_deliver_good_files = deleted_deliver_good_media_ids;
                    if (this.validateDataObject(deliver_good)) {
                        addDeliverGoodVue.$set(this.deliver_good_list, index, deliver_good);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                remove: function(index) {
                    if (this.deliver_good_list[index].id) {
                        this.pending_delete_deliver_good_ids.push(this.deliver_good_list[index].id);
                    }
                    this.deliver_good_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.deliver_good_list.length;
                },
                formatFile: function(file) {
                    if(file.formated){
                        return file;
                    }
                    return {
                        media_id: null,
                        url: file.dataURL,
                        url_thumb: file.dataURL,
                        file_name: file.name,
                        name: file.name,
                        size: file.size,
                        raw_file: file,
                        saved: false, // check is save on server
                        formated: true
                    }
                },
                getFiles: function() {
                    return this.deliver_good_list.map(function(deliver_good, index){
                        return {
                            deliver_good: deliver_good,
                            deliver_good_files: deliver_good.deliver_good_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.deliver_good_list.map(function(deliver_good, index){
                        return {
                            deliver_good: deliver_good,
                            pending_delete_deliver_good_files: deliver_good.pending_delete_deliver_good_files,
                            index: index
                        }
                    });
                },
                getFilesPendingCount: function (files) {
                    return (files ? files.filter((file) => {return (!file.saved)}).length : '---');
                },
            },
            props: ['title'],
        });
        addDeliverGoodVue.display();
        window.addDeliverGoodVue = addDeliverGoodVue;

        function addDeliverGood(){
            addDeliverGoodVue.addDeliverGood();
        }

        function saveDeliverGood() {
            addDeliverGoodVue.save();
        }

    </script>
@endpush
