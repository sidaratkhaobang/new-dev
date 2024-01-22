@push('custom_styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/dropzone.css')  }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        Vue.component('drop-zone-vue', {
            template: `<div class="form-group row">
                                    <div :id="id" class="dropzone custom-file-image">
                                        <div class="test-previews"
                                             :id="id+'-previews'"></div>
                                        <div class="file-select dropzone-area" :id="id+'-area'" :data-id="index" :data-type="filetype">
                                            <i class="fas fa-plus"></i>
                                        </div>
                                    </div>
                                </div>`,
            props: {
                id: '',
                name: '',
                index: '',
                filetype: '',
                options: Object,
                value: null,
                file: [],
                vue_name: '',
                array_name: '',
                max_file: 1,
                max_file_size: 50000,
            },
            mounted() {

                new Dropzone(`#${this.id}-area`, {
                    paramName: "file",
                    url: "#",
                    previewsContainer: `#${this.id}-previews`,
                    previewTemplate:
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
                    </div>`,
                    addRemoveLinks: false,
                    autoProcessQueue: false,
                    uploadMultiple: true,
                    parallelUploads: 1,
                    maxFiles: this.max_file,
                    maxFileSize: this.max_file_size,
                    showRemove: true,
                    showUpload: true,
                    showCancel: false,
                    showClose: false,
                    showBrowse: false,
                    showPreview: false,
                    acceptedFiles: "{{ isset($accepted_files) ? $accepted_files : '.jpeg,.jpg,.png,.svg' }}",
                    dictInvalidFileType: "ไฟล์ไม่ถูกต้อง",
                    params: {
                        elm_id: `${this.id}`,
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
                    init: function () {
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
                        this.on("success", function (file, response, formData) {
                            $('#save-form')[0].reset();
                            $('.dropzone-previews').empty();
                        });

                        this.on("addedfile", function (file) {
                            this.options.params.quota_count -= 1;
                            this.setAddArea();

                            if (this.options.params.view_only) {
                                this.viewOnly();
                            }

                            let ext = file.name.split('.').pop();
                            if (ext === "pdf") {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/pdf.png')}}');
                            } else if (ext.indexOf("doc") !== -1) {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/doc.png')}}');
                            } else if (ext.indexOf("docx") !== -1) {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/docx.png')}}');
                            } else if (ext.indexOf("xls") !== -1) {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/xls.png')}}');
                            } else if (ext.indexOf("xlsx") !== -1) {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/xls.png')}}');
                            } else if (ext.indexOf("csv") !== -1) {
                                $(file.previewElement).find(".dz-image img").attr('src', '{{asset('images/icons/dropzone/csv.png')}}');
                            }
                        });
                        this.on("sendingmultiple", function () {
                        });

                        this.on("successmultiple", function (files, response) {
                        });

                        this.on("errormultiple", function (files, response) {
                            files.forEach((file) => {
                                this.removeFile(file);
                            });
                            this.setAddArea();
                        });
                        this.on("error", function (files, response) {
                            mySwal.fire({
                                title: "{{ __('lang.store_error_title') }}",
                                text: response,
                                icon: 'error',
                                confirmButtonText: "{{ __('lang.ok') }}",
                            }).then(value => {
                                if (value) {
                                }
                            });
                        });

                        this.on('removedfile', function (file) {
                            if (file.media_id) {
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

                        this.on('resetFiles', function () {
                            this.files.forEach((file) => {
                                console.log(file);
                                this.options.params.no_delete_ids.push(file.media_id);
                            });

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
                        this.options.params.mock_files.forEach((item, index) => {
                            let mockFile = {
                                name: item.file_name,
                                size: item.size,
                                media_id: item.media_id
                            };
                            this.displayExistingFile(mockFile, item.url_thumb);

                            @if (isset($show_url))

                            var preview_link = $(
                                    `.dz-${this.id}-preview-content > .dz-content > [data-dz-name]`
                            ).eq(index);
                            preview_link.attr('href', item.url);
                            preview_link.attr('target', '_blank');

                            @endif

                        });

                        this.on('maxfilesreached', function (file) {
                            this.setAddArea();
                        });
                    },
                });
                if (this.file) {
                    this.test(this.file, this.id)
                }
            },
            methods: {
                test(file, id, type) {
                    this.$nextTick(() => {
                        var myDropzone = Dropzone.forElement(`#${this.id}-area`);
                        myDropzone.emit("resetFiles");
                        if (file != undefined) {
                            if (file.length > 0) {
                                myDropzone.emit("addedfile", file[0]);
                                if (type == null) {
                                    myDropzone.emit("thumbnail", file[0], file[0].url);
                                } else if (type == 'modal') {
                                    myDropzone.emit("thumbnail", file[0], file[0].dataURL);
                                }
                                myDropzone.files.push(file[0]);
                            }
                        }
                    });
                }
            }
        })

    </script>
@endpush
