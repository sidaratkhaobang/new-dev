<div class="row mb-4">
    {{-- <div class="row mb-4"> --}}
        <div class="col-sm-3">
            <x-forms.date-input id="receive_case_date" :value="$d->receive_case_date" :label="__('change_registrations.receive_case_date')" :optionals="['required' => true]" />
        </div>
        <div class="col-sm-6">
            <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('registers.remark')" />
        </div>
    {{-- </div> --}}
</div>
<div class="row mb-4">
    <div class="col-sm-4">
        <x-forms.radio-inline id="is_tax_sign" :value="$d->is_tax_sign" :list="$yes_no_list" :label="__('change_registrations.is_tax_sign')" :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
    </div>
    <div class="col-sm-4 amount_tax_sign"
        @if (strcmp($d->is_tax_sign, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
        <x-forms.input-new-line id="amount_tax_sign" :value="$d->amount_tax_sign" :label="__('change_registrations.amount_tax_sign')" :optionals="['type' => 'number']" />
    </div>
</div>

<div class="row mb-4">
    <div class="col-sm-4">
        <x-forms.radio-inline id="is_license_plate" :value="$d->is_license_plate" :list="$yes_no_list" :label="__('change_registrations.is_license_plate')" :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
    </div>
    <div class="col-sm-4 amount_license_plate"
        @if (strcmp($d->is_license_plate, STATUS_ACTIVE) == 0) style="display: block;" @else style="display: none;" @endif>
        <x-forms.input-new-line id="amount_license_plate" :value="$d->amount_license_plate" :label="__('change_registrations.amount_license_plate')" :optionals="['type' => 'number']" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'optional_files'" :label="__('change_registrations.optional_files')" :list="$optional_files" />
        @else
            <x-forms.upload-image :id="'optional_files'" :label="__('change_registrations.optional_files')" />
        @endif
    </div>

</div>


@push('scripts')
    <script>
        $('#engine_no').prop('disabled', true);
        $('#car_class').prop('disabled', true);
        $('#chassis_no').prop('disabled', true);
        $('#cc').prop('disabled', true);
        $('#car_status').prop('disabled', true);
        $('#color_registered').prop('disabled', true);
        $('#registered_sign').prop('disabled', true);
        $('#car_characteristic_transport').prop('disabled', true);
        $('#car_category').prop('disabled', true);
        $('#leasing').prop('disabled', true);
        $('#copy_pattern').prop('disabled', true);

        function updateCarData(carId) {
            axios.get("{{ route('admin.util.select2-change-registration.default-data-car') }}", {
                params: {
                    car_id: carId,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        $("#engine_no").val(response.data.data.engine_no);
                        $("#chassis_no").val(response.data.data.chassis_no);
                        $("#car_class").val(response.data.data.car_class);
                        $("#branch").val(response.data.data.branch);
                        $("#engine_no").val(response.data.data.engine_no);
                        $("#chassis_no").val(response.data.data.chassis_no);
                        $("#car_status").val(response.data.data.status);
                        $("#cc").val(response.data.data.cc);
                        $("#color_registered").val(response.data.data.color_registered);
                        $("#registered_sign").val(response.data.data.registered_sign);
                        $("#car_characteristic_transport").val(response.data.data.car_characteristic_transport);
                        $("#car_category").val(response.data.data.car_category);
                        $("#leasing").val(response.data.data.leasing);
                    } else {
                        $("#engine_no").val();
                        $("#chassis_no").val();
                        $("#car_class").val();
                        $("#branch").val();
                        $("#engine_no").val();
                        $("#chassis_no").val();
                        $("#car_status").val();
                        $("#cc").val();
                        $("#color_registered").val();
                        $("#registered_sign").val();
                        $("#car_characteristic_transport").val();
                        $("#car_category").val();
                        $("#leasing").val();
                    }
                }
            });
        }


        // $("#car_id").on('select2:select', function(e) {
        //     var data = e.params.data;
        //     $('#car_id_hidden').val(data.id)
        //     updateCarData(data.id);

        // });

        $("#car_id").on('change', function() {
            var carId = $(this).val();
            $('#car_id_hidden').val(carId)
            updateCarData(carId);
            if (carId) {
                $('#openModal').show();
            } else {
                $('#openModal').hide();
            }
        });

        $(document).ready(function() {
            var carId = $("#car_id").val();
            if (carId) {
                $('#car_id_hidden').val(carId)
                updateCarData(carId);
            }
            if (carId) {
                $('#openModal').show();
            } else {
                $('#openModal').hide();
            }
        });


            function handleTaxSignVisibility() {
                if ($('input[name="is_tax_sign"]:checked').val() === '{{ STATUS_ACTIVE }}') {
                    $('.amount_tax_sign').show();
                } else {
                    $('.amount_tax_sign').hide();
                }
            }

            handleTaxSignVisibility();

            $('input[name="is_tax_sign"]').on("click", function() {
                handleTaxSignVisibility();
            });
            //
            function handleLicensePlateVisibility() {
                if ($('input[name="is_license_plate"]:checked').val() === '{{ STATUS_ACTIVE }}') {
                    $('.amount_license_plate').show();
                } else {
                    $('.amount_license_plate').hide();
                }
            }

            handleLicensePlateVisibility();

            $('input[name="is_license_plate"]').on("click", function() {
                handleLicensePlateVisibility();
            });


    </script>
@endpush
