@push('scripts')
    <script>
        let addRentalBillVue = new Vue({
            el: '#rental-bill',
            data: {
                rental_bill_list: @if (isset($list)) @json($list) @else [] @endif,
                edit_index: null,
                extracost: '{{ OrderLineTypeEnum::EXTRA }}',
                tax_invoice_list: [] ,
            },
            methods: {
                display: function() {
                    $('#rental-bill').show();
                },
                add: function() {
                    var _this = this;
                    _this.display();
                    this.clearInput()
                    $('#rental-bill-modal').modal('hide');
                    this.edit_index = null;
                    window.location.reload();
                },

                setIndex: function() {
                    this.edit_index = null;
                },
                setLastIndex: function() {
                    return this.rental_bill_list.length;
                },
                clearInput: function() {
                    // const text = '{{ __('short_term_rentals.bill_' . RentalBillTypeEnum::OTHER) }}' + ' ที่ ' +this.setLastIndex();
                    $('#rental_line_name').val('');
                    $('#rental_line_description').val('');
                    $('#rental_line_amount').val('');
                    $('#rental_line_subtotal').val('');
                },
                getNumberWithCommas(x) {
                    console.log(x)
                    return numberWithCommas(x);
                },
                getTotalOfEachRentalLine: function(subttotal, amount) {
                    total_price = parseFloat(parseFloat(subttotal) * amount).toFixed(2);
                    return total_price;
                },
            },
            props: ['title'],
        });
        addRentalBillVue.display();

        function addRentalBill() {
            var rental_line = addRentalVue.getRentalLine();
            var summary = addRentalVue.getSummary();
            var bill_total = summary.subtotal;
            var bill_vat = parseFloat(parseFloat(bill_total) * 7 / 107).toFixed(2);
            var rental_id = document.getElementById('rental_id').value;
            var is_customer_address = document.getElementById('is_customer_address').value;
            var customer_billing_address_id = document.getElementById('customer_billing_address_id').value;
            var customer_billing_count = addTaxInvoiceVue.setLastIndex();
            if ((customer_billing_address_id.length <= 0) && (is_customer_address === '{{ BOOL_FALSE }}')) {
                if(customer_billing_count <= 0){
                    return warningAlert('กรุณาเพิ่มข้อมูล Billing Address');
                }else{
                    return warningAlert('กรุณาเลือกข้อมูลลูกค้า');
                }
            }
            if(rental_line.length < 1){
                return warningAlert("{{__('lang.required_field_inform')}}");
            }
            if (rental_line) {
                var data = {
                    rental_id: rental_id,
                    rental_line: rental_line,
                    bill_total: bill_total,
                    bill_vat: bill_vat,
                    is_customer_address: is_customer_address,
                    customer_billing_address_id: customer_billing_address_id
                };
                var updateUri = "{{ route('admin.short-term-rental.alter.store-rental-bill') }}";
                axios.post(updateUri, data).then(response => {
                    if (response.data.success) {
                        addRentalBillVue.add();
                    } else {
                        mySwal.fire({
                            title: "{{ __('lang.store_error_title') }}",
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: "{{ __('lang.ok') }}",
                        }).then(value => {
                            if (value) {
                                //
                            }
                        });
                    }
                });
            }else {
                warningAlert('{{ __('lang.required_field_inform') }}');
            }
            // addRentalBillVue.add();
        }

        function openRentalBillModal() {
            addRentalBillVue.clearInput();
            addRentalBillVue.setIndex();
            $('#rental-bill-modal-label').html('เพิ่มข้อมูล');
            $('#rental-bill-modal').modal('show');
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }

    </script>
@endpush
