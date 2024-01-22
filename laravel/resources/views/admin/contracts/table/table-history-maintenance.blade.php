<div id="table-show-history-maintenance" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 40%">{{ __('วันที่ซ่อมบำรุง') }}</th>
            <th style="width: 60%">{{ __('รายละเอียดการซ่อมบำรุง') }}</th>
            </thead>
            <tbody v-if="data_list.length > 0">
            <tr v-for="(item, index) in data_list">
                <td></td>
                <td></td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="6">"
                    {{ __('lang.no_list') . __('ประวัติการซ่อมบำรุง') }} "</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
    <script>
        window.tableShowHistoryMaintenance = new window.Vue({
            el: '#table-show-history-maintenance',
            data: {
                data_list: [],
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
