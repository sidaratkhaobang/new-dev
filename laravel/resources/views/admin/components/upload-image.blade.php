@push('scripts')
    <script>
        __log($)
        Dropzone.autoDiscover = false;
        $(function() {
            var dropzoneElm = new Dropzone("div#{{ $id }}-area", {
                paramName: "file",
                url: "#",
                previewsContainer: "div.{{ $id }}-previews",
                previewTemplate: @if (!isset($preview_files))
                    `
                    <div class="dz-preview dz-file-preview dz-preview-content dz-{{ $id }}-preview-content">
                    <div class="dz-content">
                        <a data-dz-name></a>
                        <button type="button" class="dz-remove btn btn-danger btn-sm btn-dropzone" data-dz-remove >
                        <i class="fa fa-times"></i>
                        </button>
                    </div>

                        <div class="dz-hash" data-dz-hash ></div>
                    </div>
                    `
                @else
                    `<div class="dz-preview dz-file-preview">
                        <div class="dz-image">
                        <img data-dz-thumbnail />
                        </div>
                        <div class="dz-details">
                        <div class="dz-filename"><span data-dz-name></span></div>
                        <div class="dz-size" data-dz-size></div>
                        </div>
                        <div class="dz-success-mark"><span>✔</span></div>
                        <div class="dz-error-mark"><span>✘</span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <button type="button" class="dz-remove btn btn-danger btn-sm" data-dz-remove >
                        <i class="fa fa-times"></i>
                        </span>
                        <div class="dz-hash" data-dz-hash ></div>
                    </div>`
                @endif ,
                addRemoveLinks: false,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 1,
                maxFiles: parseInt("{{ isset($max_files) ? $max_files : 1 }}", 10),
                maxFileSize: parseInt("{{ isset($max_file_size) ? $max_file_size : 50000 }}", 10),
                showRemove: true,
                showUpload: true,
                showCancel: false,
                showClose: false,
                showBrowse: false,
                showPreview: false,
                acceptedFiles: "{{ isset($accepted_files) ? $accepted_files : '.jpeg,.jpg,.png,.svg' }}",
                dictInvalidFileType: "ไฟล์ไม่ถูกต้อง",
                params: {
                    elm_id: "{{ $id }}",
                    mock_files: @if (isset($mock_files))
                        @json($mock_files)
                    @else
                        []
                    @endif ,
                    pending_delete_ids: [],
                    pending_add_ids: [],
                    js_delete_files: [],
                    no_delete_ids: [],
                    quota_count: 0,
                    view_only: @if (isset($view_only)) true
                    @else 
                        @if (is_view()) true @else false @endif
                    @endif,
                },
                init: function() {
                    // init quota
                    this.options.params.quota_count = this.options.maxFiles;
                    // check limit quota
                    this.isLimit = () => {
                        return ((this.options.params.quota_count <= 0) ? true : false);
                    }

                    this.setAddArea = () => {
                        let id = this.element.getAttribute('id');
                        if (this.isLimit()) {
                            $("#" + id).hide();
                        } else {
                            $("#" + id).show();
                        }
                    }

                    this.viewOnly = () => {
                        let id = this.element.getAttribute('id');
                        $("#" + id).hide();
                        $('.dz-remove').hide();
                    }

                    if (this.options.params.view_only) {
                        this.viewOnly();
                    }
                    this.on("success", function(file, response, formData) {
                        //reset the form
                        $('#save-form')[0].reset();
                        //reset dropzone
                        $('.dropzone-previews').empty();
                    });

                    this.on("addedfile", function(file) {
                        this.options.params.quota_count -= 1;
                        this.setAddArea();

                        if (this.options.params.view_only) {
                            this.viewOnly();
                        }

                        let ext = file.name.split('.').pop();

                        if (ext === "pdf") {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/pdf.png')}}');
                        }
                        else if (ext.indexOf("doc") !== -1) {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/doc.png')}}');
                        }
                        else if (ext.indexOf("docx") !== -1) {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/docx.png')}}');
                        }
                        else if (ext.indexOf("xls") !== -1) {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/xls.png')}}');
                        }
                        else if (ext.indexOf("xlsx") !== -1) {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/xls.png')}}');
                        }
                        else if (ext.indexOf("csv") !== -1) {
                            $(file.previewElement).find(".dz-image img").attr('src' , '{{asset('images/icons/dropzone/csv.png')}}');
                        }
                    });

                    // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
                    // of the sending event because uploadMultiple is set to true.
                    this.on("sendingmultiple", function() {});

                    this.on("successmultiple", function(files, response) {
                        // Gets triggered when the files have successfully been sent.
                        // Redirect user or notify of success.
                    });

                    this.on("errormultiple", function(files, response) {
                        // Gets triggered when there was an error sending the files.
                        // Maybe show form again, and notify user of error

                        //console.log(files, response);
                        files.forEach((file) => {
                            this.removeFile(file);
                        });
                        this.setAddArea();

                        /* mySwal.fire({
                          title: "{{ __('lang.store_error_title') }}",
                          text: response.message,
                          icon: 'error',
                          confirmButtonText: "{{ __('lang.ok') }}",
                        }).then(value => {
                          if (value) {
                            //
                          }
                        }); */
                    });
                    this.on("error", function(files, response) {
                        // Gets triggered when there was an error sending the files.
                        // Maybe show form again, and notify user of error
                        mySwal.fire({
                            title: "{{ __('lang.store_error_title') }}",
                            text: response,
                            icon: 'error',
                            confirmButtonText: "{{ __('lang.ok') }}",
                        }).then(value => {
                            if (value) {
                                //
                            }
                        });
                    });

                    this.on('removedfile', function(file) {
                        console.log(file);
                        if (file.media_id) {
                            // console.log(file);
                            this.options.params.pending_delete_ids.push(file.media_id);
                        }
                        if (file.pending_del_media_id) {
                            for (var i = 0; i < this.options.params.pending_add_ids
                                .length; i++) {
                                // console.log(this.options.params.pending_add_ids[i], file.pending_del_media_id)
                                if (this.options.params.pending_add_ids[i] === file
                                    .pending_del_media_id) {
                                    this.options.params.pending_add_ids.splice(i, 1);
                                    i--;
                                }
                            }
                        }
                        this.options.params.quota_count += 1;
                        this.setAddArea();
                    });

                    this.on('resetFiles', function() {

                        // var myDropzone = Dropzone.forElement('#product_files');
                        // console.log(this.options.params.pending_delete_ids);

                        this.files.forEach((file) => {
                            console.log(file);
                            this.options.params.no_delete_ids.push(file.media_id);
                        });


                        // this.options.params.pending_delete_ids.push(file.media_id);
                        // Loop through the list of files and remove each one

                        // var index = this.options.params.pending_delete_ids.indexOf(item);
                        // if (index !== -1) {
                        //     array.splice(index, 1);
                        // }

                        // for (var i = 0; i < this.options.params.pending_delete_ids
                        //         .length; i++) {
                        //         // console.log(this.options.params.pending_add_ids[i], file.pending_del_media_id)
                        //         if (this.options.params.pending_add_ids[i] === file
                        //             .pending_del_media_id) {
                        //             this.options.params.pending_add_ids.splice(i, 1);
                        //             i--;
                        //         }
                        //     }
                        while (this.files.length) {
                            this.removeFile(this.files[0]);
                        }
                    });

                    this.addPending = (id) => {
                        this.options.params.pending_add_ids.push(id);
                    }

                    this.removeDisplay = () => {
                        this.options.params.mock_files = [];
                    }

                    // init exists mockfile
                    this.options.params.mock_files.forEach((item, index) => {
                        let mockFile = {
                            name: item.file_name,
                            size: item.size,
                            media_id: item.media_id
                        };
                        this.displayExistingFile(mockFile, item.url_thumb);
                        var preview_link = $(
                            '.dz-{{ $id }}-preview-content > .dz-content > [data-dz-name]'
                        ).eq(index);
                        preview_link.attr('href', item.url);
                        preview_link.attr('target', '_blank');
                    });

                    this.on('maxfilesreached', function(file) {
                        //console.log("maxfilesreached");
                        this.setAddArea();
                    });
                },
            });
            if (!window.myDropzone) {
                window.myDropzone = [];
            }
            window.myDropzone.push(dropzoneElm);
        });
    </script>
@endpush
