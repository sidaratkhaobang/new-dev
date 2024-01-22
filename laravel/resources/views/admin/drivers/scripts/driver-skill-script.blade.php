@push('scripts')
    <script>
        let addDriverSkillVue = new Vue({
            el: '#driver-skill',
            data: {
                driver_skill_list: @if (isset($driver_skill_list)) @json($driver_skill_list) @else [] @endif,
                edit_index: null,
                mode: null,
                pending_delete_driver_skill_ids: [],
            },
            methods: {
                display: function() {
                    $("#driver-skill").show();
                },
                addDriverSkill: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editDriverSkill: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#driver-skill-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#driving_skill_field").val('').change();
                    window.myDropzone[1].removeAllFiles(true);
                },
                loadModalData: function(index){
                    var temp = null;
                    temp = this.driver_skill_list[index];
                    $("#driving_skill_field").val(temp.driving_skill_id).change();
                    var defaultDeiverSkillOption = {
                            id: temp.driving_skill_id,
                            text: temp.driving_skill_text,
                    };
                    var tempDriverSkillOption = new Option(defaultDeiverSkillOption.text, defaultDeiverSkillOption.id, false, false);
                    $("#driving_skill_field").append(tempDriverSkillOption).trigger('change');
                    // clear file myDropzone
                    window.myDropzone[1].removeAllFiles(true);
                    window.myDropzone[1].options.params.js_delete_files = [];

                    // load file skill
                    var skill_files = temp.skill_files;
                    if(skill_files.length > 0){
                        skill_files.forEach( item => {
                            let mockFile = {...item};
                            window.myDropzone[1].emit( "addedfile", mockFile );
                            window.myDropzone[1].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[1].files.push( mockFile );
                        });
                    }
                },
                openModal: function(){
                    $("#modal-driver-skill").modal("show");
                },
                hideModal: function(){
                    $("#modal-driver-skill").modal("hide");
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
                    var driving_skill_id = document.getElementById("driving_skill_field").value;
                    var driving_skill_text = (driving_skill_id) ? document.getElementById('driving_skill_field').selectedOptions[0].text : '';
                    console.log(window.myDropzone[1]);
                    var skill_raw_files = window.myDropzone[1].files;
                    var skill_files = skill_raw_files.map( item => this.formatFile(item) );
                    var id = null;
                    return {
                        id: id,
                        driving_skill_id: driving_skill_id,
                        driving_skill_text: driving_skill_text,
                        skill_files: skill_files,
                        pending_delete_skill_files: [],
                    };
                },
                validateDataObject: function(driver_skill){
                    if (driver_skill.driving_skill_id) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(){
                    var driver_skill = this.getDataFromModalAdd();
                    if (this.validateDataObject(driver_skill)) {
                        this.driver_skill_list.push(driver_skill);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var driving_skill_id = document.getElementById("driving_skill_field").value;
                    var driving_skill_text = (driving_skill_id) ? document.getElementById('driving_skill_field').selectedOptions[0].text : '';
                    var driver_skill = this.driver_skill_list[index];
                    // load files in modal dropzone
                    var modal_skill_files = window.myDropzone[1].files;
                    var skill_files = modal_skill_files.map( item => this.formatFile(item) );

                    // get all deleted files
                    var deleted_skill_files = window.myDropzone[1].options.params.js_delete_files;
                    deleted_skill_files  = deleted_skill_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_skill_media_ids = deleted_skill_files.map((file) => {
                        return file.media_id;
                    });

                    driver_skill['driving_skill_id'] = driving_skill_id;
                    driver_skill['driving_skill_text'] = driving_skill_text;
                    driver_skill['skill_files'] = skill_files;
                    driver_skill['pending_delete_skill_files'] = deleted_skill_media_ids;
                    if (this.validateDataObject(driver_skill)) {
                        addDriverSkillVue.$set(this.driver_skill_list, index, driver_skill);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                removeDriverSkill: function(index) {
                    if (this.driver_skill_list[index].id) {
                        this.pending_delete_driver_skill_ids.push(this.driver_skill_list[index].id);
                    }
                    this.driver_skill_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.driver_skill_list.length;
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
                    return this.driver_skill_list.map(function(driver, index){
                        return {
                            driver: driver,
                            driver_skill_files: driver.skill_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.driver_skill_list.map(function(driver, index){
                        return {
                            driver: driver,
                            pending_delete_skill_files: driver.pending_delete_skill_files,
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
        addDriverSkillVue.display();
        window.addDriverSkillVue = addDriverSkillVue;

        function addDriverSkill(){
            addDriverSkillVue.addDriverSkill();
        }

        function saveDriverSkill() {
            addDriverSkillVue.save();
        }

    </script>
@endpush
