@push('scripts')
    <script>
        let incomeExcelVue = new Vue({
            el: '#income-list',
            data: {
                income_list: [],
                edit_index: null,
                countOfPage: 5,
                currPage: 1,
            },
            computed: {
                // filteredRows: function() {
                //     return this.car_list_temp;
                // },
                pageStart: function() {
                    return (this.currPage - 1) * this.countOfPage;
                },
                totalPage: function() {
                    return Math.ceil(this.income_list.length / this.countOfPage);
                },
            },
            methods: {
                display: function() {
                    $("#income-list").show();
                },
                add: async function() {
                    const url = "{{ route('admin.income-accounts.income-list') }}";
                    var temp_doc_type_id = $('#temp_doc_type_id').val();
                    var temp_status = $('#temp_status').val();
                    var temp_from_date = $('#temp_from_date').val();
                    var temp_to_date = $('#temp_to_date').val();

                    const {
                        data
                    } = await axios.get(url, {
                        params: {
                            doc_type_id: temp_doc_type_id,
                            status: temp_status,
                            from_date: temp_from_date,
                            to_date: temp_to_date,
                        }
                    });
                    var add_data = [...data];
                    __log(add_data);
                    if (add_data.length <= 0) {
                        return warningAlert("{{ __('lang.not_found') }}");
                    }
                    _list = this.income_list;
                    _list.push(...add_data);
                    const unique_data = _list.reduce((unique, item) => {
                        const is_item_exist = unique.some(i => i.id === item.id);
                        if (!is_item_exist) {
                            unique.push(item);
                        }
                        return unique;
                    }, []);
                    this.income_list = unique_data;
                },
                clearSearch: function() {
                    $('#temp_doc_type_id').val(null).trigger('change');
                    $('#temp_status').val(null).trigger('change');
                    $('#temp_from_date').val(null);
                    $('#temp_to_date').val(null);
                },
                remove: function(index) {
                    this.income_list.splice(index, 1);
                    if (this.income_list.slice(this.pageStart, this.pageStart + this.countOfPage).length <= 0) {
                        this.setPage(this.currPage - 1)
                    }
                },
                setIndex: function() {
                    this.edit_index = null;
                },
                setPage: function(idx) {
                    if (idx <= 0 || idx > this.totalPage) {
                        return;
                    }
                    this.currPage = idx;
                },
                export: function() {
                    var from_date = document.getElementById('temp_from_date').value;
                    var to_date = document.getElementById('temp_to_date').value;
                    var income_list = this.income_list;
                    var ids = income_list.map(obj => obj.id);
                    if (ids.length <= 0) {
                        return warningAlert("{{ __('lang.not_found') }}");
                    }
                    $.ajax({
                        xhrFields: {
                            responseType: 'blob'
                        },
                        type: 'POST',
                        url: "{{ route('admin.income-accounts.export') }}",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            sap_interface_ids: ids,
                        },
                        success: function(result, status, xhr) {
                            var fileName = 'file.xlsx';
                            if (from_date || to_date) {
                                fileName = from_date + '-' + to_date + '.xlsx';
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
                        },
                        error: function(result, status, xhr) {
                            warningAlert("{{ __('lang.not_found') }}")
                        }
                    });
                }
            },
            props: ['title'],
        });
        incomeExcelVue.display();

        function addIncomeList() {
            incomeExcelVue.add();
        }

        function clearFilter() {
            incomeExcelVue.clearSearch();
        }

        function exportIncomeList() {
            incomeExcelVue.export();
        }
    </script>
@endpush
