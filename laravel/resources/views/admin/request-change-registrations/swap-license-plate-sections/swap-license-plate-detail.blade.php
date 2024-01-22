<div class="row mb-4">
    {{-- <div class="row mb-4"> --}}
    <div class="col-sm-3">
        <x-forms.date-input id="receive_case_date_swap" :value="$d->receive_case_date" :label="__('change_registrations.receive_case_date')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.radio-inline id="is_car_alternate_tls" :value="$d->is_car_alternate_tls" :list="$yes_no_list" :label="__('change_registrations.is_car_alternate_tls')"
            :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="car_owner_type" :list="$request_type_contact_list" :value="$d->car_owner_type" :label="__('change_registrations.car_owner_type')"
            :optionals="['required' => true]" />
    </div>
    {{-- </div> --}}
</div>

<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_swap" :value="$d->car_swap" :label="__('change_registrations.car_swap')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="car_class_swap" :list="[]" :value="$d->car_class" :label="__('change_registrations.car_class')"
            :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $car_class_name]"  />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('change_registrations.engine_no')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('change_registrations.chassis_no')" :optionals="['required' => true]" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'register_files'" :label="__('change_registrations.register_files')" :list="$register_files" />
        @else
            <x-forms.upload-image :id="'register_files'" :label="__('change_registrations.register_files')" />
        @endif
    </div>
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'power_attorney_files'" :label="__('change_registrations.power_attorney_files')" :list="$power_attorney_files" />
        @else
            <x-forms.upload-image :id="'power_attorney_files'" :label="__('change_registrations.power_attorney_files')" />
        @endif
    </div>
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'letter_consent_files'" :label="__('change_registrations.letter_consent_files')" :list="$letter_consent_files" />
        @else
            <x-forms.upload-image :id="'letter_consent_files'" :label="__('change_registrations.letter_consent_files')" />
        @endif
    </div>
    <div class="col-sm-3">
        @if (isset($view))
            <x-forms.view-image :id="'citizen_files'" :label="__('change_registrations.citizen_files')" :list="$citizen_files" />
        @else
            <x-forms.upload-image :id="'citizen_files'" :label="__('change_registrations.citizen_files')" />
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
