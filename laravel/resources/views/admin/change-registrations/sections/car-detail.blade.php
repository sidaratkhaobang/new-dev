<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.car_detail'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="car_id" :list="$car_list" :value="$d->car_id"
                                       :label="__('cars.license_plate')"
                                       :optionals="['required' => true]"/>
                <x-forms.hidden id="car_id_hidden" :value="$d->car_id"/>
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="car_class" :value="$d?->car?->carClass?->full_name"
                                        :label="__('tax_renewals.car_class')"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="engine_no" :value="$d?->car?->engine_no" :label="__('cars.engine_no')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="chassis_no" :value="$d?->car?->chassis_no" :label="__('cars.chassis_no')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="cc" :value="$d?->car?->engine_size" :label="__('car_classes.engine_size')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_status" :value="null" :label="__('sign_yellow_tickets.car_status')"/>
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="color_registered" :value="$d?->Registered?->color_registered"
                                        :label="__('registers.color_registered')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="registered_sign" :value="$d?->Registered?->registered_sign"
                                        :label="__('registers.license_plate_registered')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_characteristic_transport" :value="null"
                                        :label="__('registers.car_characteristic_transport')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_category" :value="null" :label="__('registers.car_category')"/>
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="leasing" :value="null" :label="__('registers.leasing')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="copy_pattern" :value="$d?->Registered?->registered_sign"
                                        :label="__('change_registrations.copy_pattern')"/>
            </div>
        </div>
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

        {{--function updateCarData(carId) {--}}
        {{--    axios.get("{{ route('admin.request-change-registrations.default-data-car') }}", {--}}
        {{--        params: {--}}
        {{--            car_id: carId,--}}
        {{--        }--}}
        {{--    }).then(response => {--}}
        {{--        if (response.data.success) {--}}
        {{--            if (response.data.data) {--}}
        {{--                $("#engine_no").val(response.data.data.engine_no);--}}
        {{--                $("#chassis_no").val(response.data.data.chassis_no);--}}
        {{--                $("#car_class").val(response.data.data.car_class);--}}
        {{--                $("#branch").val(response.data.data.branch);--}}
        {{--                $("#engine_no").val(response.data.data.engine_no);--}}
        {{--                $("#chassis_no").val(response.data.data.chassis_no);--}}
        {{--                $("#car_status").val(response.data.data.status);--}}
        {{--                $("#cc").val(response.data.data.cc);--}}
        {{--                $("#color_registered").val(response.data.data.color_registered);--}}
        {{--                $("#registered_sign").val(response.data.data.registered_sign);--}}
        {{--                $("#car_characteristic_transport").val(response.data.data.car_characteristic_transport);--}}
        {{--                $("#car_category").val(response.data.data.car_category);--}}
        {{--                $("#leasing").val(response.data.data.leasing);--}}
        {{--            } else {--}}
        {{--                $("#engine_no").val();--}}
        {{--                $("#chassis_no").val();--}}
        {{--                $("#car_class").val();--}}
        {{--                $("#branch").val();--}}
        {{--                $("#engine_no").val();--}}
        {{--                $("#chassis_no").val();--}}
        {{--                $("#car_status").val();--}}
        {{--                $("#cc").val();--}}
        {{--                $("#color_registered").val();--}}
        {{--                $("#registered_sign").val();--}}
        {{--                $("#car_characteristic_transport").val();--}}
        {{--                $("#car_category").val();--}}
        {{--                $("#leasing").val();--}}
        {{--            }--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        {{--$("#car_id").on('change', function () {--}}
        {{--    var carId = $(this).val();--}}
        {{--    $('#car_id_hidden').val(carId)--}}
        {{--    updateCarData(carId);--}}
        {{--    if (carId) {--}}
        {{--        $('#openModal').show();--}}
        {{--    } else {--}}
        {{--        $('#openModal').hide();--}}
        {{--    }--}}
        {{--});--}}

        {{--$(document).ready(function () {--}}
        {{--    var carId = $("#car_id").val();--}}
        {{--    if (carId) {--}}
        {{--        $('#car_id_hidden').val(carId)--}}
        {{--        updateCarData(carId);--}}
        {{--    }--}}
        {{--    if (carId) {--}}
        {{--        $('#openModal').show();--}}
        {{--    } else {--}}
        {{--        $('#openModal').hide();--}}
        {{--    }--}}
        {{--});--}}

    </script>
@endpush
