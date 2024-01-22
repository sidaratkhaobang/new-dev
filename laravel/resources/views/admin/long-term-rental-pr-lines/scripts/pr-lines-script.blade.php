@push('scripts')
    <script>
    let addPrLineVue = new Vue({
            el: '#lt-pr-line',
            data: {
                lt_pr_line_list: @if (isset($lt_pr_line_list)) @json($lt_pr_line_list) @else [] @endif,
                lt_rental_line_list: @if (isset($lt_rental_line_list)) @json($lt_rental_line_list) @else [] @endif,
                edit_index: null,
                mode: null,
                pending_delete_lt_pr_line_ids: [],
                view_mode: @if (isset($view_mode)) true @else false @endif,
                rule_amount: @json($lt_rental_line_list),
                rule_month: [],
            },
            methods: {
                display: function() {
                    $("#lt-pr-line").show();
                },
                addPRLine: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                addAllPRLine: function() {
                    this.addPendingIds();
                    var lines = JSON.parse(JSON.stringify(this.lt_rental_line_list));
                    this.lt_pr_line_list = lines;
                },
                edit: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#pr-line-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#temp_lt_line").val(null).change();
                    $("#temp_lt_line_amount").val(null).change();
                    $("#temp_lt_month").val(null).change();
                    $("#temp_remark").val('');
                    window.myDropzone[1].removeAllFiles(true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.lt_pr_line_list[index];
                    $("#temp_lt_line").val(temp.lt_line).trigger("change");
                    var defaultLtLineOption = {
                        id: temp.lt_line,
                        text: temp.lt_line_text,
                    };
                    var tempLtLineOption = new Option(defaultLtLineOption.text, defaultLtLineOption.id, true, true);
                    $("#temp_lt_line").append(tempLtLineOption).trigger('change');
                    $("#temp_lt_line_amount").val(temp.amount);

                    $("#temp_lt_month").val(temp.lt_month).trigger("change");
                    var defaultLtMonthOption = {
                        id: temp.month,
                        text: temp.month_text,
                    };
                    var tempLtMonthOption = new Option(defaultLtMonthOption.text, defaultLtMonthOption.id, true, true);
                    $("#temp_lt_month").append(tempLtMonthOption).trigger('change');
                    $("#temp_remark").val(temp.remark);
                    // clear file myDropzone
                    window.myDropzone[1].removeAllFiles(true);
                    window.myDropzone[1].options.params.js_delete_files = [];

                    // load file license
                    var approved_rental_files = temp.approved_rental_files;
                    if(approved_rental_files.length > 0){
                        approved_rental_files.forEach( item => {
                            console.log(item);
                            let mockFile = {...item};
                            window.myDropzone[1].emit( "addedfile", mockFile );
                            window.myDropzone[1].emit( "thumbnail", mockFile, item.url_thumb );
                            window.myDropzone[1].files.push( mockFile );

                        });
                    }
                },
                openModal: function() {
                    $("#modal-pr-line").modal("show");
                },
                hideModal: function() {
                    $("#modal-pr-line").modal("hide");
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
                getDataFromModalAdd: function() {
                    var lt_line = $("#temp_lt_line").val();
                    var lt_line_text = $("#temp_lt_line option:selected").text();
                    var amount = $("#temp_lt_line_amount").val();
                    var month = $("#temp_lt_month").val();
                    var month_text = $("#temp_lt_month option:selected").text();
                    var remark = $("#temp_remark").val();
                    var approved_rental_files = window.myDropzone[1].files;
                    var approved_rental_files = approved_rental_files.map( item => this.formatFile(item) );
                    // var id = null;

                    return {
                        // id: id,
                        lt_line: lt_line,
                        lt_line_text: lt_line_text,
                        amount: amount,
                        month: month,
                        month_text: month_text,
                        remark: remark,
                        approved_rental_files: approved_rental_files,
                        pending_delete_approved_rental_files: [],
                    };
                },
                validateDataObject: function(lt_pr_line) {
                    // ToDO
                    return true;
                },
                checkAmountRule: function(lt_pr_line, edit_index = null) {
                    var _this = this;
                    const sumsByLine = _this.lt_pr_line_list.filter((item, index) => {
                        if (edit_index != null && edit_index == index) {
                            return false;
                        }
                        return true;
                    })
                    .reduce((result, item) => {
                        if (!result[item.lt_line]) {
                            result[item.lt_line] = parseInt(0);
                        }
                        result[item.lt_line] += parseInt(item.amount);
                        return result;
                    }, {});

                    if (!sumsByLine[lt_pr_line.lt_line]) {
                        sumsByLine[lt_pr_line.lt_line] = parseInt(0);
                    }
                    sumsByLine[lt_pr_line.lt_line] += parseInt(lt_pr_line.amount);
                    const item = this.lt_rental_line_list.find(obj => obj.lt_line === lt_pr_line.lt_line);
                    if (item.amount < sumsByLine[lt_pr_line.lt_line]) {
                        return false;
                    }
                    return true;
                },
                checkMonthRule: function(lt_pr_line, edit_index = null) {
                    var _this = this;
                    const monthsByLine = _this.lt_pr_line_list.filter((item, index) => {
                        if (edit_index != null && edit_index == index) {
                            return false;
                        }
                        return true;
                    })
                    .reduce((result, item) => {
                        if (!result[item.lt_line]) {
                            result[item.lt_line] = [];
                        }
                        result[item.lt_line].push(item.month);
                        return result;
                    }, {});
                    if (!monthsByLine[lt_pr_line.lt_line]) {
                        monthsByLine[lt_pr_line.lt_line] = [];
                    }
                    monthsByLine[lt_pr_line.lt_line].push(lt_pr_line.month);
                    if (monthsByLine[lt_pr_line.lt_line].indexOf(lt_pr_line.month) 
                        !== monthsByLine[lt_pr_line.lt_line].lastIndexOf(lt_pr_line.month)) {
                        return false;
                    } 
                    return true;
                },
                saveAdd: function() {
                    var lt_pr_line = this.getDataFromModalAdd();
                    if (!this.validateDataObject(lt_pr_line)) {
                        return warningAlert("{{ __('lang.required_field_inform') }}");
                    }

                    if (!this.checkAmountRule(lt_pr_line)) {
                        return warningAlert("{{ __('long_term_rentals.invalid_amount') }}");
                    }

                    if (!this.checkMonthRule(lt_pr_line)) {
                        return warningAlert("{{ __('long_term_rentals.invalid_month') }}");
                    }

                    this.lt_pr_line_list.push(lt_pr_line);
                    this.edit_index = null;
                    this.display();
                    this.hideModal();
                },
                saveEdit: function(index) {
                    console.log('saveEdit');
                    console.log('saveEdit_ index' , index);
                    var lt_line = $("#temp_lt_line").val();
                    var lt_line_text = $("#temp_lt_line option:selected").text();
                    var amount = $("#temp_lt_line_amount").val();
                    var month = $("#temp_lt_month").val();
                    var month_text = $("#temp_lt_month option:selected").text();
                    var remark = $("#temp_remark").val();
                    var lt_pr_line = this.lt_pr_line_list[index];
                    console.log('edit' , lt_pr_line);

                    // load files in modal dropzone
                    var modal_approved_rental_files = window.myDropzone[1].files;
                    var approved_rental_files = modal_approved_rental_files.map( item => this.formatFile(item) );

                    // get all deleted files
                    var deleted_approved_rental_files = window.myDropzone[1].options.params.js_delete_files;
                    deleted_approved_rental_files  = deleted_approved_rental_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_approved_rental_media_ids = deleted_approved_rental_files.map((file) => {
                        return file.media_id;
                    });

                    lt_pr_line.lt_line = lt_line;
                    lt_pr_line.lt_line_text = lt_line_text;
                    lt_pr_line.amount = amount;
                    lt_pr_line.month = month;
                    lt_pr_line.month_text = month_text;
                    lt_pr_line.remark = remark;
                    lt_pr_line.approved_rental_files = approved_rental_files;
                    lt_pr_line.pending_delete_approved_rental_files = deleted_approved_rental_media_ids;

                    if (this.validateDataObject(lt_pr_line)) {
                        if (!this.checkAmountRule(lt_pr_line, index)) {
                            return warningAlert("{{ __('long_term_rentals.invalid_amount') }}");
                        }
                        addPrLineVue.$set(this.lt_pr_line_list, index, lt_pr_line);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    }else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                remove: function(index) {
                    if (this.lt_pr_line_list[index].id) {
                        this.pending_delete_lt_pr_line_ids.push(this.lt_pr_line_list[index].id);
                    }
                    this.lt_pr_line_list.splice(index, 1);
                },
                addPendingIds: function() {  
                    var pending_delete_ids = [];
                    this.lt_pr_line_list.forEach(function(item) {
                        if (item.id) {
                            pending_delete_ids.push(item.id);   
                        }
                    });
                    this.pending_delete_lt_pr_line_ids = pending_delete_ids;
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                   return this.lt_pr_line_list.length;
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
                    return this.lt_pr_line_list.map(function(lt_pr_line, index){
                        return {
                            lt_pr_line: lt_pr_line,
                            lt_pr_line_approved_rental_files: lt_pr_line.approved_rental_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.lt_pr_line_list.map(function(lt_pr_line, index){
                        return {
                            lt_pr_line: lt_pr_line,
                            pending_delete_approved_rental_files: lt_pr_line.pending_delete_approved_rental_files,
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
        addPrLineVue.display();
        window.addPrLineVue = addPrLineVue;

        function openModalAddPRLine(id) {
            $('#modal-pr-line').modal('show');
        }

        function addLongTermRentalPRLine() {
            addPrLineVue.addPRLine();
        }

        function addAllLongTermRentalPRLine() {
            addPrLineVue.addAllPRLine();
        }

        function saveLongTermRentalPRLine() {
            addPrLineVue.save();
        }

        $("#temp_lt_line").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            dropdownParent: $("#modal-pr-line"),
            ajax: {
                delay: 250,
                url: function (params) {
                    return "{{ route('admin.util.select2-rental.lt-rental-line-car-classes') }}";
                },
                type: 'GET',
                data: function (params) {
                    long_term_rental_id = $('#id').val();
                    return {
                        long_term_rental_id: long_term_rental_id,
                        s: params.term
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            }
        });

        $("#temp_lt_month").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            dropdownParent: $("#modal-pr-line"),
            ajax: {
                delay: 250,
                url: function (params) {
                    return "{{ route('admin.util.select2-rental.lt-rental-months') }}";
                },
                type: 'GET',
                data: function (params) {
                    long_term_rental_id = $('#id').val();
                    return {
                        long_term_rental_id: long_term_rental_id,
                        s: params.term
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            }
        });

        $('#temp_lt_line').on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-rental.lt-rental-line-car-amount') }}", {
                params: {
                    id: data.id
                }
            }).then(response => {
                if (response.data) {
                    maxlength = parseInt(response.data);
                    $("#temp_lt_line_amount").val(maxlength);
                    $("#temp_lt_line_amount").attr('max', maxlength); 
                }
            });
        });

        var view_mode = @if (isset($view_mode)) true @else false @endif;
        if (view_mode) {
            $("#approve_status").prop('disabled', true);
            $("#require_date").prop('disabled', true);
        }

    </script>
@endpush