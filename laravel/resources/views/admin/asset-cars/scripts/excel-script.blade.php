@push('scripts')
    <script>
        let assetCarExcelVue = new Vue({
            el: '#asset-car-list',
            data: {
                asset_car_list: [],
                edit_index: null,
            },
            methods: {
                display: function() {
                    $("#asset-car-list").show();
                },
                add: async function() {
                    const url = "{{ route('admin.asset-cars.asset-excel-list') }}";
                    var temp_status = $('#temp_status').val();
                    var temp_lot_id = $('#temp_lot_id').val();
                    var temp_car_class_id = $('#temp_car_class_id').val();
                    var temp_car_id = $('#temp_car_id').val();
                    var excel_type = $('#excel_type').val();
                    const {
                        data
                    } = await axios.get(url, {
                        params: {
                            status: temp_status,
                            lot_id: temp_lot_id,
                            car_class_id: temp_car_class_id,
                            car_id: temp_car_id,
                            excel_type: excel_type,
                        }
                    });
                    var add_data = [...data];
                    if (add_data.length <= 0) {
                        return warningAlert("{{ __('lang.not_found') }}");
                    }
                    this.asset_car_list.push(...add_data);
                },
                clearSearch: function() {
                    $('#temp_status').val(null).trigger('change');
                    $('#temp_lot_id').val(null).trigger('change');
                    $('#temp_car_class_id').val(null).trigger('change');
                    $('#temp_car_id').val(null).trigger('change');
                },
                clearData: function() {
                    this.asset_car_list = [];
                },
                getAssetCarId: function() {
                    let asset_car_ids = [];
                    asset_car_ids = this.asset_car_list.map(obj => obj.id);
                    return asset_car_ids;
                },
                remove: function(index) {
                    this.asset_car_list.splice(index, 1);
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                export: function() {
                    var date = new Date();
                    var year = date.getFullYear();
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var day = ('0' + date.getDate()).slice(-2);
                    var current_date = day + '_' + month + '_' + year;
                    var cost_center = '{{ \App\Enums\AssetCarTypeEnum::COST_CENTER }}';
                    var asset_master_car = '{{ \App\Enums\AssetCarTypeEnum::ASSET_MASTER_CAR }}';
                    var asset_master_sub_car = '{{ \App\Enums\AssetCarTypeEnum::ASSET_MASTER_SUB_CAR }}';
                    var post_value_car = '{{ \App\Enums\AssetCarTypeEnum::POST_VALUE_CAR }}';
                    var post_value_sub_car = '{{ \App\Enums\AssetCarTypeEnum::POST_VALUE_SUB_CAR }}';
                    var excel_type = document.getElementById('excel_type').value;
                    var asset_car_list = this.asset_car_list;
                    var ids = asset_car_list.map(obj => obj.id);
                    if (ids.length <= 0) {
                        return warningAlert("{{ __('lang.not_found') }}");
                    }
                    $.ajax({
                        xhrFields: {
                            responseType: 'blob'
                        },
                        type: 'POST',
                        url: '{{ route('admin.asset-cars.export') }}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            asset_car_ids: ids,
                            excel_type: excel_type,
                        },
                        success: function(result, status, xhr) {
                            if (excel_type === cost_center) {
                                var fileName = 'CostCenter_' + current_date + '.xlsx';
                            }
                            if (excel_type === asset_master_car) {
                                var fileName = 'AssetMasterCar_' + current_date + '.xlsx';
                            }
                            if (excel_type === asset_master_sub_car) {
                                var fileName = 'AssetMasterSubCar_' + current_date + '.xlsx';
                            }
                            if (excel_type === post_value_car) {
                                var fileName = 'PostValueCar_' + current_date + '.xlsx';
                            }
                            if (excel_type === post_value_sub_car) {
                                var fileName = 'PostValueSubCar_' + current_date + '.xlsx';
                            }
                            var blob = new Blob([result], {
                                type: 'text/csv;charset=utf-8'
                            });
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = fileName;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            $('#asset-car-excel-modal').modal('hide');
                        },
                        error: function(result, status, xhr) {
                            warningAlert("{{ __('lang.not_found') }}")
                        }
                    });
                }
            },
            props: ['title'],
        });
        assetCarExcelVue.display();

        function addAssetCarList() {
            assetCarExcelVue.add();
        }

        function clearFilter() {
            assetCarExcelVue.clearSearch();
        }

        function exportAssetCarList() {
            assetCarExcelVue.export();
        }
    </script>
@endpush
