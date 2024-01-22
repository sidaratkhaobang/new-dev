@push('scripts')
    <script>
        window.tableFileUpload = new window.Vue({
            el: '#table-file-upload',
            data: {
                data_list: @if(isset($check_credit_file)) @json($check_credit_file) @else [] @endif,
                pending_delete_media_file_ids: [],
            },
            methods: {
                addFile: function (files, custom_file_name) {
                    files = files.map(file => this.formatFile(file, custom_file_name));
                    files.forEach(file => {
                        console.log('===insert===')
                        this.data_list.push(file)
                    });
                },
                formatFile: function (file, custom_file_name) {
                    if (file.formated) {
                        return file;
                    }
                    return {
                        media_id: null,
                        url: file.dataURL,
                        url_thumb: file.dataURL,
                        file_name: file.name,
                        name: custom_file_name,
                        size: file.size,
                        mime_type: file.type,
                        raw_file: file,
                        saved: false,
                        formated: true,
                    }
                },
                removeFile: function (index) {
                    const media_id = this.data_list[index].media_id;
                    console.log(media_id)
                    if (media_id) {
                        this.pending_delete_media_file_ids.push(media_id);
                    }
                    this.data_list.splice(index, 1);
                },
            },
            props: ['title'],
        });

        $('.btn-open-modal-upload').click(function () {
            $('#modal-upload-file').modal('show')
        });

        $('#modal-upload-file').on('hidden.bs.modal', function (e) {
            window.myDropzone[0].removeAllFiles(true);
            $('#custom_file_name').val('')
        });

        $('.btn-save-form-modal').click(function () {
            const custom_file_name = $('#custom_file_name').val();
            const objDropzone = window.myDropzone[0]
            const files = objDropzone.getQueuedFiles();
            if (files.length === 0) {
                warningAlert('{{__('กรุณาเลือกไฟล์')}}');
            } else if (!custom_file_name) {
                warningAlert('{{__('กรุณากรอกชื่อเอกสาร')}}');
            } else {
                window.tableFileUpload.addFile(files, custom_file_name);
                $('#modal-upload-file').modal('hide');
            }
        });
    </script>
@endpush
