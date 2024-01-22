@push('custom_styles')
    <style>
        .block-header-custom {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@endpush
<div class="block-header-custom mb-2 mt-2">
    <h4><i class="fa fa-file-lines"></i> {{ __('รายการเอกสารที่เกี่ยวข้อง') }}</h4>
    <button class="btn btn-primary btn-custom-size btn-open-modal-upload" type="button">
        <i class="fa fa-plus-circle"></i> {{ __('เพิ่ม') }}
    </button>
</div>
<div id="table-file-upload" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
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
                    <a v-if="item.saved" target="_blank" v-bind:href="item.url"><i class="fa fa-file-arrow-down">&nbsp;&nbsp;&nbsp;</i></a><i class="far fa-trash-alt" style="cursor: pointer" v-on:click="removeFile(index)"></i>
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

@push('scripts')
    <script>
        window.tableFileUpload = new window.Vue({
            el: '#table-file-upload',
            data: {
                data_list: [],
                pending_delete_media_file_ids: [],
            },
            methods: {
                addDataList: function (data_list) {
                    this.data_list = data_list
                },
                clearDataList: function () {
                    this.data_list = []
                },
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
    </script>
@endpush
