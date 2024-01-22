<div id="table-change-car-user" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 30%">{{ __('ทะเบียน') }}</th>
            <th style="width: 30%">{{ __('ชื่อผู้ใช้') }}</th>
            <th style="width: 30%">{{ __('เบอร์โทร') }}</th>
            </thead>
            <tbody v-if="data_list.length > 0">
            <tr v-for="(item, index) in data_list">
                <td>@{{ item.car_license_plate }}</td>
                <td>@{{ item.car_user }}</td>
                <td>@{{ item.car_phone }}</td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="4">
                    "{{ __('lang.no_list') }} "
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        window.tableChangeCarUser = new window.Vue({
            el: '#table-change-car-user' ,
            data: {
                data_list: [
                    // {
                    //     car_id : 'xxx',
                    //     car_license_plate : 'ABc 456',
                    //     car_user : 'EiEi',
                    //     car_phone : '0987654352',
                    // },
                ] ,
            } ,
            methods: {
                addDataList: function (data_list) {
                    this.data_list = data_list
                },
                addData: function (data) {
                    this.data_list.push(data)
                },
                clearDataList: function () {
                    this.data_list = []
                },
            } ,
            props: ['title'] ,
        });
    </script>
@endpush
