@push('scripts')
    <script>
        let addReturnGoodVue = new Vue({
            el: '#return-goods',
            data: {
                return_good_list: @if (isset($return_good_list)) @json($return_good_list) @else [] @endif,
                edit_index: null,
                mode: null,
                pending_delete_return_good_ids: [],
            },
            methods: {
                display: function() {
                    $("#return-goods").show();
                },
                addReturnGood: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#return-good-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#return_good_brand_id").val(null).trigger('change');
                    $("#return_good_class_id").val(null).trigger('change');
                    $("#return_good_color_id").val(null).trigger('change');
                    $("#return_good_license_plate").val('');
                    $("#return_good_chassis_no").val('');
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
                    temp = this.return_good_list[index];
                    
                    temp_return_good_brand_option = this.setOption(temp.return_good_brand_id, temp.return_good_brand_text);
                    $("#return_good_brand_id").append(temp_return_good_brand_option).trigger('change');

                    temp_return_good_class_option = this.setOption(temp.return_good_class_id, temp.return_good_class_text);
                    $("#return_good_class_id").append(temp_return_good_class_option).trigger('change');

                    temp_return_good_color_option = this.setOption(temp.return_good_color_id, temp.return_good_color_text);
                    $("#return_good_color_id").append(temp_return_good_color_option).trigger('change');

                    $("#return_good_license_plate").val(temp.return_good_license_plate);
                    $("#return_good_chassis_no").val(temp.return_good_chassis_no);

                    // clear file myDropzone
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];

                    // load file license
                    var return_good_files = temp.return_good_files;
                    if(return_good_files.length > 0){
                        return_good_files.forEach( item => {
                            let mockFile = {...item};
                            window.myDropzone[0].emit( "addedfile", mockFile );
                            window.myDropzone[0].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[0].files.push( mockFile );
                        });
                    }
                },
                openModal: function(){
                    $("#modal-return-good").modal("show");
                },
                hideModal: function(){
                    $("#modal-return-good").modal("hide");
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
                    var return_good_brand_id = document.getElementById("return_good_brand_id").value;
                    var return_good_brand_text = (return_good_brand_id) ? document.getElementById('return_good_brand_id').selectedOptions[0].text : '';
                    var return_good_class_id = document.getElementById("return_good_class_id").value;
                    var return_good_class_text = (return_good_class_id) ? document.getElementById('return_good_class_id').selectedOptions[0].text : '';
                    var return_good_color_id = document.getElementById("return_good_color_id").value;
                    var return_good_color_text = (return_good_color_id) ? document.getElementById('return_good_color_id').selectedOptions[0].text : '';
                    var return_good_license_plate = document.getElementById("return_good_license_plate").value;
                    var return_good_chassis_no = document.getElementById("return_good_chassis_no").value;
                    var return_good_raw_files = window.myDropzone[0].files;
                    var return_good_files = return_good_raw_files.map( item => this.formatFile(item) );
                    // var id = null;
                    return {
                        // id: id,
                        return_good_brand_id: return_good_brand_id,
                        return_good_brand_text: return_good_brand_text,
                        return_good_class_id: return_good_class_id,
                        return_good_class_text: return_good_class_text,
                        return_good_color_id: return_good_color_id,
                        return_good_color_text: return_good_color_text,
                        return_good_license_plate: return_good_license_plate,
                        return_good_chassis_no: return_good_chassis_no,
                        return_good_files: return_good_files,
                        pending_delete_return_good_files: [],
                    };
                },
                validateDataObject: function(return_good){
                    // if (return_good.name) {
                    //     return true;
                    // } else {
                    //     return false;
                    // }
                    return true;
                },
                saveAdd: function(){
                    var return_good = this.getDataFromModalAdd();
                    if (this.validateDataObject(return_good)) {
                        this.return_good_list.push(return_good);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {

                    var return_good_brand_id = document.getElementById("return_good_brand_id").value;
                    var return_good_brand_text = (return_good_brand_id) ? document.getElementById('return_good_brand_id').selectedOptions[0].text : '';
                    var return_good_class_id = document.getElementById("return_good_class_id").value;
                    var return_good_class_text = (return_good_class_id) ? document.getElementById('return_good_class_id').selectedOptions[0].text : '';
                    var return_good_color_id = document.getElementById("return_good_color_id").value;
                    var return_good_color_text = (return_good_color_id) ? document.getElementById('return_good_color_id').selectedOptions[0].text : '';
                    var return_good_license_plate = document.getElementById("return_good_license_plate").value;
                    var return_good_chassis_no = document.getElementById("return_good_chassis_no").value;
                    var return_good = this.return_good_list[index];
                    // load files in modal dropzone
                    var modal_return_good_files = window.myDropzone[0].files;
                    var return_good_files = modal_return_good_files.map( item => this.formatFile(item) );

                    // get all deleted files
                    var deleted_return_good_files = window.myDropzone[0].options.params.js_delete_files;
                    deleted_return_good_files  = deleted_return_good_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_return_good_media_ids = deleted_return_good_files.map((file) => {
                        return file.media_id;
                    });

                    return_good.return_good_brand_id = return_good_brand_id;
                    return_good.return_good_class_id = return_good_class_id;
                    return_good.return_good_color_id = return_good_color_id;
                    return_good.return_good_license_plate = return_good_license_plate;
                    return_good.return_good_chassis_no = return_good_chassis_no;
                    return_good.return_good_files = return_good_files;
                    return_good.pending_delete_return_good_files = deleted_return_good_media_ids;
                    if (this.validateDataObject(return_good)) {
                        addReturnGoodVue.$set(this.return_good_list, index, return_good);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                remove: function(index) {
                    if (this.return_good_list[index].id) {
                        this.pending_delete_return_good_ids.push(this.return_good_list[index].id);
                    }
                    this.return_good_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.return_good_list.length;
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
                    return this.return_good_list.map(function(return_good, index){
                        return {
                            return_good: return_good,
                            return_good_files: return_good.return_good_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.return_good_list.map(function(return_good, index){
                        return {
                            return_good: return_good,
                            pending_delete_return_good_files: return_good.pending_delete_return_good_files,
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
        addReturnGoodVue.display();
        window.addReturnGoodVue = addReturnGoodVue;

        function addReturnGood(){
            addReturnGoodVue.addReturnGood();
        }

        function saveReturnGood() {
            addReturnGoodVue.save();
        }

    </script>
@endpush
