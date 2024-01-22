@push('scripts')
    <script>
        async function getProductDetail(id) {
            return response = await axios.get("{{ route('admin.util.select2.product-detail') }}", {
                params: {
                    product_id: id
                }
            }).then(response => {
                if (response.data.success) {
                    return response.data.data;
                }
            });
        }
        
        jQuery(function() {
            let pickup_date_flatpickr_init = flatpickr("#pickup_date", {
                minDate: "today",
            });

            let return_date_flatpickr = flatpickr("#return_date", {
                minDate: "today",
                onClose: async function(selectedDates, dateStr, instance) {
                    product_id = $('#product_id').val();
                    if (product_id) {
                        product = await getProductDetail(product_id);
                        fix_days = parseInt(product.fix_days);
                        fix_return_time = product.fix_return_time;
                        if (pickup_date_flatpickr.selectedDates.length > 0) {
                            new_return_date = { ...pickup_date_flatpickr.selectedDates }; 
                            new_return_date = new Date(new_return_date[0]);
                            if (fix_days > 0) {
                                parsedDate = new_return_date.setDate(new_return_date.getDate() + fix_days);
                            } else {
                                parsedDate = selectedDates;
                            }
                            let return_date = new Date(parsedDate);
                            if (fix_return_time) {
                                const [hours, minutes] = fix_return_time.split(':');
                                return_date.setHours(hours, minutes);
                            }
                            instance.setDate(return_date, false, "Y-m-d H:i");
                        }
                    }
                }
            });
            
            let pickup_date_flatpickr = flatpickr("#pickup_date", {
                minDate: "today",
                onClose: async function(selectedDates, dateStr, instance) {
                    product_id = $('#product_id').val();
                    if (product_id) {
                        product = await getProductDetail(product_id);
                        fix_days = parseInt(product.fix_days);
                        fix_return_time = product.fix_return_time;
                        const return_date_input = return_date_flatpickr;
                        if (fix_days > 0) {
                            let cloneDates = Object.assign({}, selectedDates);
                            cloneDates = { ...selectedDates }; 
                            cloneDates = new Date(cloneDates[0]);
                            parsedDate = cloneDates.setDate(cloneDates.getDate() + fix_days);
                            let return_date = new Date(parsedDate);
                            if (fix_return_time) {
                                const [hours, minutes] = fix_return_time.split(':');
                                return_date.setHours(hours, minutes);
                            }
                            return_date_input.setDate(return_date, false, "Y-m-d H:i");
                        } 
                    }
                }
            });

            $('#product_id').on('select2:select', async function(e) {
                var data = e.params.data;
                product = await getProductDetail(data.id);
                fix_days = parseInt(product.fix_days);
                fix_return_time = product.fix_return_time;
                is_fixed = (fix_days && fix_return_time) ? true : false;
                if (pickup_date_flatpickr.selectedDates.length > 0 && fix_days > 0 ) {
                    new_return_date = { ...pickup_date_flatpickr.selectedDates }; 
                    new_return_date = new Date(new_return_date[0]);
                    parsedDate = new_return_date.setDate(new_return_date.getDate() + fix_days);
                    let return_date = new Date(parsedDate);
                    if (fix_return_time) {
                        const [hours, minutes] = fix_return_time.split(':');
                        return_date.setHours(hours, minutes);
                    }
                    return_date_flatpickr.setDate(return_date, false, "Y-m-d H:i");
                }
            });
        });
    </script>
@endpush