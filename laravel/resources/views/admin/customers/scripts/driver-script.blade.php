@push('scripts')
    <script>
        let addCustomerDriverVue = new Vue({
            el: '#customer-driver',
            data: {
                customer_driver_list: @if (isset($customer_driver_list)) @json($customer_driver_list) @else [] @endif,
                edit_index: null,
                mode: null,
                pending_delete_driver_ids: [],
            },
            methods: {
                display: function() {
                    $("#customer-driver").show();
                },
                addDriver: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editDriver: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#driver-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#full_name_field").val('');
                    $("#tel_driver_field").val('');
                    $("#citizen_field").val('');
                    $("#email_driver_field").val('');
                    $("#driving_license").val('');
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[1].removeAllFiles(true);
                },
                loadModalData: function(index){
                    var temp = null;
                    temp = this.customer_driver_list[index];
                    $("#full_name_field").val(temp.name);
                    $("#tel_driver_field").val(temp.tel);
                    $("#citizen_field").val(temp.citizen_id);
                    $("#email_driver_field").val(temp.email);
                    // clear file myDropzone
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];
                    window.myDropzone[1].removeAllFiles(true);
                    window.myDropzone[1].options.params.js_delete_files = [];

                    // load file license
                    var license_files = temp.license_files;
                    if(license_files.length > 0){
                        license_files.forEach( item => {
                            let mockFile = {...item};
                            window.myDropzone[0].emit( "addedfile", mockFile );
                            window.myDropzone[0].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[0].files.push( mockFile );
                        });
                    }

                    // load file citizen
                    var citizen_files = temp.citizen_files;
                    if(citizen_files.length > 0){
                        citizen_files.forEach( item => {
                            let mockFile = {...item};
                            window.myDropzone[1].emit( "addedfile", mockFile );
                            window.myDropzone[1].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[1].files.push( mockFile );
                        });
                    }
                },
                openModal: function(){
                    $("#modal-driver").modal("show");
                },
                hideModal: function(){
                    $("#modal-driver").modal("hide");
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
                    var name = document.getElementById("full_name_field").value;
                    var tel = document.getElementById("tel_driver_field").value;
                    var citizen_id = document.getElementById("citizen_field").value;
                    var email = document.getElementById("email_driver_field").value;
                    var license_raw_files = window.myDropzone[0].files;
                    var citizen_raw_files = window.myDropzone[1].files;
                    var license_files = license_raw_files.map( item => this.formatFile(item) );
                    var citizen_files = citizen_raw_files.map( item => this.formatFile(item) );
                    var id = null;
                    return {
                        id: id,
                        name: name,
                        tel: tel,
                        citizen_id: citizen_id,
                        email: email,
                        license_files: license_files,
                        citizen_files: citizen_files,
                        pending_delete_license_files: [],
                        pending_delete_citizen_files: [],
                    };
                },
                validateDataObject: function(driver){
                    if (driver.name) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(){
                    var driver = this.getDataFromModalAdd();
                    if (this.validateDataObject(driver)) {
                        this.customer_driver_list.push(driver);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                    console.log(this.customer_driver_list);
                },
                saveEdit: function(index) {
                    var name = document.getElementById("full_name_field").value;
                    var tel = document.getElementById("tel_driver_field").value;
                    var citizen_id = document.getElementById("citizen_field").value;
                    var email = document.getElementById("email_driver_field").value;
                    var driver = this.customer_driver_list[index];
                    // load files in modal dropzone
                    var modal_license_files = window.myDropzone[0].files;
                    var license_files = modal_license_files.map( item => this.formatFile(item) );
                    var modal_citizen_files = window.myDropzone[1].files;
                    var citizen_files = modal_citizen_files.map( item => this.formatFile(item) );

                    // get all deleted files
                    var deleted_license_files = window.myDropzone[0].options.params.js_delete_files;
                    deleted_license_files  = deleted_license_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_license_media_ids = deleted_license_files.map((file) => {
                        return file.media_id;
                    });
                    var deleted_citizen_files = window.myDropzone[1].options.params.js_delete_files;
                    deleted_citizen_files  = deleted_citizen_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_citizen_media_ids = deleted_citizen_files.map((file) => {
                        return file.media_id;
                    });

                    driver['name'] = name;
                    driver['tel'] = tel;
                    driver['citizen_id'] = citizen_id;
                    driver['email'] = email;
                    driver['license_files'] = license_files;
                    driver['citizen_files'] = citizen_files;
                    driver['pending_delete_license_files'] = deleted_license_media_ids;
                    driver['pending_delete_citizen_files'] = deleted_citizen_media_ids;
                    if (this.validateDataObject(driver)) {
                        addCustomerDriverVue.$set(this.customer_driver_list, index, driver);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                removeDriver: function(index) {
                    if (this.customer_driver_list[index].id) {
                        this.pending_delete_driver_ids.push(this.customer_driver_list[index].id);
                    }
                    this.customer_driver_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.customer_driver_list.length;
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
                    return this.customer_driver_list.map(function(driver, index){
                        return {
                            driver: driver,
                            driver_license_files: driver.license_files,
                            driver_citizen_files: driver.citizen_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.customer_driver_list.map(function(driver, index){
                        return {
                            driver: driver,
                            pending_delete_license_files: driver.pending_delete_license_files,
                            pending_delete_citizen_files: driver.pending_delete_citizen_files,
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
        addCustomerDriverVue.display();
        window.addCustomerDriverVue = addCustomerDriverVue;

        function addDriver(){
            addCustomerDriverVue.addDriver();
        }

        function saveDriver() {
            addCustomerDriverVue.save();
        }

    </script>
@endpush
