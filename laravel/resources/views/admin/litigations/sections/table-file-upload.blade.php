@section('block_options_doc')
    <div class="block-options-item">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <button class="btn btn-primary btn-custom-size btn-open-modal-upload" type="button">
                <i class="fa fa-plus-circle me-1"></i> {{ __('litigations.add_doc') }}
            </button>
        @endif
    </div>
@endsection

@include('admin.litigations.modals.upload-file-modal')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.additional_doc'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_doc',
    ])
    <div class="block-content">
        <div id="table-file-upload" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th style="width: 1px;">#</th>
                        <th style="width: 55%">{{ __('litigations.file_name') }}</th>
                        <th style="width: 35%">{{ __('litigations.file_date_size') }}</th>
                        <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="data_list.length > 0">
                        <tr v-for="(item, index) in data_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.name }}</td>
                            <td>@{{  item.date + '  /  ' + item.readable_size }}</td>
                            <td class="sticky-col text-center">
                                <a v-if="item.saved" v-bind:href="item.url" target="_blank"><i class="fa fa-file-arrow-down">&nbsp;&nbsp;&nbsp;</i></a><i
                                    class="far fa-circle-xmark" style="cursor: pointer"
                                    v-on:click="removeFile(index)"></i>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.tableFileUpload = new window.Vue({
            el: '#table-file-upload',
            data: {
                data_list: @if (isset($additional_files)) @json($additional_files) @else [] @endif,
                pending_delete_media_file_ids: [],
            },
            methods: {
                addFile: function(files, custom_file_name) {
                    __log(files);
                    files = files.map(file => this.formatFile(file, custom_file_name));
                    files.forEach(file => {
                        this.data_list.push(file)
                    });
                },
                formatFile: function(file, custom_file_name) {
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
                        readable_size: this.formatBytes(file.size),
                        mime_type: file.type,
                        raw_file: file,
                        saved: false,
                        formated: true,
                        date: this.formatDate(file.lastModified)
                    }
                },
                removeFile: function(index) {
                    const media_id = this.data_list[index].media_id;
                    if (media_id) {
                        this.pending_delete_media_file_ids.push(media_id);
                    }
                    this.data_list.splice(index, 1);
                },
                formatBytes: function(bytes,decimals) {
                    if(bytes == 0) return '0 Bytes';
                    var k = 1024,
                        dm = decimals || 2,
                        sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                        i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                },
                formatDate(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-US', options);
                    return formattedDate;
                },
            },
            props: ['title'],
        });

        $('.btn-open-modal-upload').click(function() {
            $('#modal-upload-file').modal('show')
        });

        $('#modal-upload-file').on('hidden.bs.modal', function(e) {
            index = window.myDropzone.findIndex(x => x.element.id ==="additional_files-area");
            window.myDropzone[index].removeAllFiles(true);
            $('#additional_file_name').val('')
        });

        $('.btn-save-file-modal').click(function() {
            index = window.myDropzone.findIndex(x => x.element.id ==="additional_files-area");
            const additional_file_name = $('#additional_file_name').val();
            const objDropzone = window.myDropzone[index]
            const files = objDropzone.getQueuedFiles();
            if (files.length === 0) {
                warningAlert('{{ __('กรุณาเลือกไฟล์') }}');
            } else if (!additional_file_name) {
                warningAlert('{{ __('กรุณากรอกชื่อเอกสาร') }}');
            } else {
                window.tableFileUpload.addFile(files, additional_file_name);
                $('#modal-upload-file').modal('hide');
            }
        });
    </script>
@endpush
