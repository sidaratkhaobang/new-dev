<x-modal :id="'attorney-select-car'" :title="'เลือกรถที่ต้องการขอหนังสือมอบอำนาจ'">
    @include('admin.ownership-transfers.section-attorney.select-car')
    <div id="attorney" v-cloak data-detail-uri="" data-title="">
        @include('admin.ownership-transfers.section-attorney.car-list')
    </div>
    <x-slot name="footer">
        <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                <button type="button" class="btn btn-primary btn-export-attorney-pdf"><i class="icon-menu-money"></i>
                    ขอหนังสือมอบอำนาจ (TLS)</button>
    </x-slot>
</x-modal>

@include('admin.ownership-transfers.scripts.attorney-script')
@push('scripts')
    <script>
        function checkCarAttorney() {
            var status = document.getElementById("status_attorney").value;
            var month_last_payment = document.getElementById("month_last_payment_attorney").value;
            var car_id = document.getElementById("car_id_attorney").value;
            var car_class = document.getElementById("car_class_attorney").value;
            var leasing = document.getElementById("leasing_attorney").value;
            if (!status) {
                return warningAlert("{{ __('registers.required_status') }}")
            }
            axios.get("{{ route('admin.ownership-transfers.check-car') }}", {
                params: {
                    status: status,
                    month_last_payment: month_last_payment,
                    car_id: car_id,
                    car_class: car_class,
                    leasing: leasing,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.ownership_transfer.length > 0) {
                        addAttorneyVue.addCar(response.data.ownership_transfer);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

     
        $(".btn-export-attorney-pdf").on("click", function() {
            var attorney_list = addAttorneyVue.attorney_list;
            var attorney_list_arr = [];

            attorney_list.forEach(attorney => {
                    attorney_list_arr.push(attorney.id);
            });

            var form = document.createElement('form');
            form.action = "{{ route('admin.ownership-transfers.export-pdf-attorney') }}";
            form.method = 'GET';

            attorney_list_arr.forEach(id => {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'attorney_list_arr[]'; 
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();

            $('#attorney-select-car-modal').modal('hide');
        });

        jQuery(function() {
            let monthExpireInput = document.querySelector("#month_last_payment_attorney");
            let defaultDate = "{{ $month_last_payment ?? '' }}";

            let flatpickrInstance = flatpickr(monthExpireInput, {
                plugins: [
                    new monthSelectPlugin({
                        dateFormat: "m/Y",
                        shorthand: true,
                        theme: "light",
                    })
                ],
                onReady: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.classList.add('flatpickr-monthYear');
                },
                onChange: function(selectedDates, dateStr) {

                },
                defaultDate: defaultDate,
            });
            if (this.value) {
                flatpickrInstance.setDate(this.value);
            }
        });
    </script>
@endpush
