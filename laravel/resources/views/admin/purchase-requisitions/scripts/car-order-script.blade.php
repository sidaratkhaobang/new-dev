@push('scripts')
    <script>
        jQuery(function() { Dashmix.helpers(['dm-table-tools-checkable']); });
        var selected_dealers = addPurchaseOrderDealerVue.$data.purchase_order_dealer_list;
        var selected_creditor_id = '{{ $d->creditor_id }}';
        var purchase_order_lines = @json($purchase_order_lines);
        
        // set disable field
        $('#purchase_order_no').prop('disabled', true);
        $('#po_request_date').prop('disabled', true);
        $('#requester_name').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#purchase_requisition_no').prop('disabled', true);
        $('#request_date').prop('disabled', true);
        $('#purchase_requisition_date').prop('disabled', true);
        $('#delivery_date').prop('disabled', true);
        $('#rental_type').prop('disabled', true);
        $('#purchase_requisition_remark').prop('disabled', true);
        $('#reviewer_name').prop('disabled', true);
        $('#reviewer_department').prop('disabled', true);
        $('#review_at').prop('disabled', true);
        $('#reason').prop('disabled', true);

        if (selected_creditor_id) {
            $('#ordered_creditor_id').val(selected_creditor_id).trigger('change');
            $('#check-all').prop('disabled', false);
            $('.form-check-input-each').prop('disabled', false);
        } else {
            $('#check-all').prop('disabled', true);
            $('.form-check-input-each').prop('disabled', true);
        }

        $('#ordered_creditor_id').on('select2:select', function(e) {
            $('#check-all').prop('disabled', false);
            $('.form-check-input-each').prop('disabled', false);
        });

        $('#ordered_creditor_id').on('change', function() {
            $('#check-all').prop('disabled', true);
            $('.form-check-input-each').prop('disabled', true);
            $('#check-all').prop('checked', false);
            $('.form-check-input-each').prop('checked', false);
            $('.summary-vat-total').text('-');
            $('.summary-price-total').text('-');
            $('#summary-car-amount').text('-');

            purchase_requisition_car_list.forEach(function(car) {
                $('#'+ car.id + '_amount').prop('disabled', true);
                $('#'+ car.id + '_amount').val('');
                $('#vat_total_'+ car.id).html('-'); 
                $('#price_total_'+ car.id).html('-'); 
            });
        });

        // disabled price input
        purchase_requisition_car_list.forEach(function(car) {
            $('#'+ car.id + '_amount').prop('disabled', true);
            $('#row_' + car.id).on('click', function(){
                var disabled = $(this).is(':checked') ? false :  true;
                if (disabled) { 
                    $('#'+ car.id + '_amount').val('');
                    $('#vat_total_'+ car.id).html('-'); 
                    $('#price_total_'+ car.id).html('-'); 
                }
                $('#'+ car.id + '_amount').prop('disabled', disabled);
            });
        });

        if (purchase_order_lines.length > 0) {
            purchase_order_lines.forEach(function(car_price) {
                $('#row_'+ car_price.item_id).prop('checked', true);
                $('#'+ car_price.item_id + '_amount').prop('disabled', false);
                $('#'+ car_price.item_id + '_amount').val(car_price.amount);
                $('#vat_total_'+ car_price.item_id).html(numberWithCommas(car_price.vat)); 
                $('#price_total_'+ car_price.item_id).html(numberWithCommas(car_price.total)); 
                $('#selected_cars\\[' + car_price.item_id + '\\]\\[vat\\]').val(car_price.vat);
                $('#selected_cars\\[' + car_price.item_id + '\\]\\[price\\]').val(car_price.total);
            });
            sumVatPrice();
            sumCarAmount();
            checkAllcheck()
        }

        // toogle input check all
        $('.form-check-input-each').change(function(){
            sumVatPrice();
            sumCarAmount();
            checkAllcheck()
        });
        $('#check-all').change(function(){
            if (this.checked) {
                $('.form-check-input-each').prop('checked', true);
                purchase_requisition_car_list.forEach(function(car) {
                    $('#'+ car.id + '_amount').prop('disabled', false);
                });
            } else {
                $('.form-check-input-each').prop('checked', false);
                purchase_requisition_car_list.forEach(function(car) {
                    $('#'+ car.id + '_amount').prop('disabled', true);
                    $('#'+ car.id + '_amount').val('');
                    $('#vat_total_'+ car.id).html('-'); 
                    $('#price_total_'+ car.id).html('-'); 
                });
            }
            sumVatPrice();
            sumCarAmount();
        });             
        
        // iput each car amount change 
        $('.input-number-car-amount').on('input', function() {
            var required_car = $(this).val();
            var car_id = $(this).attr('data-id');
            var selected_creditor_id = $('#ordered_creditor_id').val();
            var max_car = purchase_requisition_car_list.find(o => o.id == car_id).amount;
            if (required_car % 1 !== 0) {
                required_car = parseInt( isNaN(parseInt(required_car))? 0 : required_car);
                $('#'+ car_id + '_amount').val(required_car);
            }
            if (required_car > max_car) {
                $('#'+ car_id + '_amount').val(max_car);
                required_car = max_car;
            }

            var selected_dealer_data = selected_dealers.find(o => o.creditor_id == selected_creditor_id);
            var price_list = selected_dealer_data.dealer_price_list.find(o => o.car_id == car_id);
            var vat_total =  parseFloat(price_list.vat * required_car).toFixed(2);
            var price_total = parseFloat(price_list.car_price * required_car).toFixed(2);

            sumCarAmount();
            $('#vat_total_' + car_id).html(numberWithCommas(vat_total));
            $('#selected_cars\\[' + car_id + '\\]\\[vat\\]').val(vat_total);
            $('#price_total_' + car_id).html(numberWithCommas(price_total));
            $('#selected_cars\\[' + car_id + '\\]\\[price\\]').val(price_total);
        });

        // summary price
        $('.input-number-car-amount').on('input change', function() {
            console.log('input change');
            sumVatPrice();
        });

        function sumVatPrice() {
            list = ['vat', 'price'];
            list.forEach(function(item) {
                var item_sum = 0;
                $('.' + item + '-total').each(function(){
                    value = parseFloat($(this).text().replace(/,/g, ''));
                    item_sum += (isNaN(value)? 0 : value);
                });
                sum = numberWithCommas(parseFloat(item_sum).toFixed(2));
                $('.summary-'+ item +'-total').text(sum);
                $('#summary_'+ item +'_total').val(parseFloat(item_sum).toFixed(2));
            });
        }

        function sumCarAmount() {
            var car_amount_sum = 0;
            $('.input-number-car-amount').each(function(){
                car_amount = parseInt(+$(this).val());
                car_amount_sum +=  parseInt(isNaN(car_amount)? 0 : car_amount);
            });
            $('#summary-car-amount').text(parseInt(car_amount_sum));
        }

        function checkAllcheck() {
            checked = ($('.form-check-input-each:checked').length == $('.form-check-input-each').length)? true : false;
            $('#check-all').prop('checked', checked);
        }

        $(".btn-update-status").on("click", function() {
            let storeUri = "{{ route('admin.purchase-orders.store') }}";
            var formData = appendFormData();
            formData.append('status_updated', true);
            saveForm(storeUri, formData);
        });

        $(".btn-purchase-order-update-status").on("click", function() {
            let storeUri = "{{ route('admin.purchase-orders.update-status') }}";
            var formData = appendFormData();
            formData.append('status_updated', true);
            saveForm(storeUri, formData);
        });

        $(".btn-purchase-order-update-status").on("click", function(){
            var data = {
                purchase_order_status: $(this).attr('id'),
                purchase_order_id: document.getElementById("id").value,
            };
            updatePurchaseOrderStatus(data);
        });

        function appendFormData() 
        {
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.addPurchaseOrderDealerVue) {
                let data = window.addPurchaseOrderDealerVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.dealer_files && item.dealer_files.length > 0) {
                            item.dealer_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('dealer_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addPurchaseOrderDealerVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_dealer_files && item.pending_delete_dealer_files.length >
                            0) {
                            item.pending_delete_dealer_files.forEach(function(id) {
                                formData.append('pending_delete_dealer_files[]', id);
                            });
                        }
                    });
                }
                
                //delete dealer row
                let delete_dealer_ids = window.addPurchaseOrderDealerVue.pending_delete_dealer_ids;
                if (delete_dealer_ids && (delete_dealer_ids.length > 0)) {
                    delete_dealer_ids.forEach(function(delete_driver_id) {
                        formData.append('delete_dealer_ids[]', delete_driver_id);
                    });
                }
            }
            return formData;
        }
    </script>
@endpush 