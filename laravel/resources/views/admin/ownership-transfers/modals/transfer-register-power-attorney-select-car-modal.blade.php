<x-modal :id="'transfer-register-power-attorney-select-car'" :title="'เลือกรถที่ต้องการขอหนังสือชุดโอน/เล่มทะเบียน/มอบอำนาจ'">
    @include('admin.ownership-transfers.section-transfer-register-power-attorney.select-car')
    <div id="transfer-register-power-attorney" v-cloak data-detail-uri="" data-title="">
        @include('admin.ownership-transfers.section-transfer-register-power-attorney.car-list')
    </div>
    <x-slot name="footer">
        <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                <button type="button" class="btn btn-primary btn-export-transfer-pdf"><i class="icon-menu-money"></i>
                    ขอชุดโอน/เล่มทะเบียน/มอบอำนาจ</button>
    </x-slot>
</x-modal>

@include('admin.ownership-transfers.scripts.transfer-register-power-attorney-script')
@push('scripts')
    <script>
        function checkCarTransfer() {
            var status = document.getElementById("status_transfer").value;
            var month_last_payment = document.getElementById("month_last_payment_transfer").value;
            var car_id = document.getElementById("car_id_transfer").value;
            var car_class = document.getElementById("car_class_transfer").value;
            var leasing = document.getElementById("leasing_transfer").value;
            if (!status) {
                return warningAlert("{{ __('registers.required_status') }}")
            }
            if (!leasing) {
                return warningAlert("{{ __('registers.required_leasing') }}")
            }
            axios.get("{{ route('admin.ownership-transfers.check-car-transfer') }}", {
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
                        addTransferVue.addCar(response.data.ownership_transfer);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

     
        $(".btn-export-transfer-pdf").on("click", function() {
            var transfer_list = addTransferVue.transfer_list;
            var transfer_list_arr = [];

            transfer_list.forEach(transfer => {
                transfer_list_arr.push(transfer.id);
            });

            var form = document.createElement('form');
            form.action = "{{ route('admin.ownership-transfers.export-pdf-transfer') }}";
            form.method = 'GET';

            transfer_list_arr.forEach(id => {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'transfer_list_arr[]'; 
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();

            $('#transfer-register-power-attorney-select-car-modal').modal('hide');
        });

        jQuery(function() {
            let monthExpireInput = document.querySelector("#month_last_payment_transfer");
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
