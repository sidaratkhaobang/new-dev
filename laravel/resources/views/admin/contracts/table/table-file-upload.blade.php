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
</div>
<div id="table-show-file-upload" v-cloak data-detail-uri="" data-title="">
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
                    <a v-if="item.saved" target="_blank" v-bind:href="item.url"><i class="fa fa-file-arrow-down">&nbsp;&nbsp;&nbsp;</i></a>
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
        window.tableShowFileUpload = new window.Vue({
            el: '#table-show-file-upload',
            data: {
                data_list: [],
                pending_delete_media_file_ids: [],
            },
            methods: {
                addDataList: function (data) {
                    this.data_list = data
                },
                clearDataList: function () {
                    this.data_list = []
                },
            },
            props: ['title'],
        });
    </script>
@endpush
