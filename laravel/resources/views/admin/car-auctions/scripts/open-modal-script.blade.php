@push('scripts')
    <script>
        function openCancelCMIVMIMultiModal() {
            var enum_pending_sale = '{{ \App\Enums\CarAuctionStatusEnum::PENDING_SALE }}';
            var check_list = @json($list);
            var arr_check = [];
            checkCancelCMIVMIVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.status == enum_pending_sale && item.close_cmi_vmi_date == null) {
                            checkCancelCMIVMIVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-cancel-cmi-vmi-multi').modal('show');
        }

        function openKeyMultiModal() {
            var enum_pending_sale = '{{ \App\Enums\CarAuctionStatusEnum::PENDING_SALE }}';
            var check_list = @json($list);
            var arr_check = [];
            checkPickUpKeyVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.status == enum_pending_sale && item.pick_up_date == null) {
                            checkPickUpKeyVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-pick-up-key-multi').modal('show');
        }

        function openSendAuctionMultiModal() {
            var enum_ready_auction = '{{ \App\Enums\CarAuctionStatusEnum::READY_AUCTION }}';
            var enum_change_auction = '{{ \App\Enums\CarAuctionStatusEnum::CHANGE_AUCTION }}';
            const enum_status = [enum_ready_auction, enum_change_auction];
            var check_list = @json($list);
            var arr_check = [];
            sendAuctionVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (enum_status.includes(item.status)) {
                            $("#auction_place").val(item.auction_id).change();
                            var defaultAuctionOption = {
                                id: item.auction_id,
                                text: item.auction_name,
                            };
                            var tempAuctionOption = new Option(defaultAuctionOption.text, defaultAuctionOption
                                .id, false, false);
                            $("#auction_place").append(tempAuctionOption).trigger('change');
                            if (item.status == enum_change_auction) {
                                $("#change_status").val(item.status);
                                $('#auction_place').prop('disabled', true);
                            }
                            sendAuctionVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-send-auction').modal('show');
        }

        function openBookMultiModal() {
            var check_list = @json($list);
            var arr_check = [];
            checkBookVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.book_date == null) {
                            checkBookVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-book-multi').modal('show');
        }

        $(".btn-change-auction-modal").on("click", function() {
            var id = $(this).attr('data-id');
            $('#car_auction_id').val(id);
            var check_list = @json($list);
            var auction_old = null;
            $('#auction_old').val('');
            $('#auction_old_id').val('');
            $('#status_old').val('');
            if (check_list.data.length > 0) {
                var car_auction = check_list.data.filter(obj => obj.id == id);
                car_auction.forEach((e) => {
                    $('#auction_old').val(e.auction_name);
                    $('#auction_old_id').val(e.auction_id);
                    $('#status_old').val(e.status);
                });
            }
            $('#modal-change-auction').modal('show');
        });

        function download() {
            var check_list = @json($list);
            var arr_check = [];
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        arr_check.push(item.id);
                    }
                });
            }
            var export_url = "{{ route('admin.car-auctions.download-excel') }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: export_url,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ids: arr_check,
                },
                success: function(result, status, xhr) {
                    let today = new Date().toISOString().slice(0, 10);
                    var fileName = 'สรุปการขาย.xlsx';
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
                    errorAlert("{{ __('lang.not_found') }}");
                }
            });
        }

        function printSaleSummary() {
            var enum_sold_out = '{{ \App\Enums\CarAuctionStatusEnum::SOLD_OUT }}';
            var check_list = @json($list);
            var arr_check = [];
            if (check_list.data.length > 0) {
                check_list.data.forEach(function(item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.status == enum_sold_out) {
                            arr_check.push(item.id);
                        }
                    }
                });
            }
            var export_url = "{{ route('admin.car-auctions.sale-summary-pdf') }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: export_url,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ids: arr_check,
                },
                success: function(result, status, xhr) {
                    console.log('success');
                    let today = new Date().toISOString().slice(0, 10);
                    var fileName = 'เอกสารสรุปการขาย.pdf';
                    var blob = new Blob([result], {
                        type: 'application/pdf'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(result, status, xhr) {
                    console.log('error');
                    errorAlert("{{ __('lang.not_found') }}");
                }
            });
        }
    </script>
@endpush
