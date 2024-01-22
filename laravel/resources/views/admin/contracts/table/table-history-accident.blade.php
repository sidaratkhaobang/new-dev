<div id="table-show-history-accident" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 40%">{{ __('วันที่เกิดอุบัติเหตุ') }}</th>
            <th style="width: 60%">{{ __('รายละเอียดอุบัติเหตุ') }}</th>
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
                    {{ __('lang.no_list') . __('ประวัติอุบัติเหตุ') }} "</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
    <script>
        window.tableShowHistoryAccident = new window.Vue({
            el: '#table-show-history-accident',
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
