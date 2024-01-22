@section('block_options_1')
<div class="block-options-item">
    @if(Route::is('*.edit') || Route::is('*.create'))
        <button class="btn btn-primary btn-custom-size btn-open-modal-upload" type="button">
            <i class="fa fa-plus-circle me-1"></i> {{ __('เพิ่ม') }}
        </button>
    @endif
</div>
@endsection

<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('รายการเอกสารที่เกี่ยวข้อง'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div id="table-file-upload" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                    <th style="width: 45%">{{ __('check_credit.form.section_table.file_name') }}</th>
                    <th style="width: 45%">{{ __('check_credit.form.section_table.extension_name') }}</th>
                    <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="data_list.length > 0">
                    <tr v-for="(item, index) in data_list">
                        <td>@{{ item.name }}</td>
                        <td>@{{ item.mime_type }}</td>
                        <td class="sticky-col text-center">
                            <a v-if="item.saved" v-bind:href="item.url"><i class="fa fa-file-arrow-down">&nbsp;&nbsp;&nbsp;</i></a><i class="far fa-circle-xmark" style="cursor: pointer" v-on:click="removeFile(index)"></i>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr class="table-empty">
                        <td class="text-center" colspan="6">"
                            {{ __('lang.no_list') . __('เอกสารที่เกี่ยวข้อง') }} "</td>
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
                data_list: @if(isset($contract_file)) @json($contract_file) @else [] @endif,
                pending_delete_media_file_ids: [],
            },
            methods: {
                addFile: function (files, custom_file_name) {
                    files = files.map(file => this.formatFile(file, custom_file_name));
                    files.forEach(file => {
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
