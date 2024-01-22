@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('page_title_no', $d->worksheet_no)
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    @if ($d->status)
        @if (in_array($d->status, [InstallEquipmentStatusEnum::OVERDUE, InstallEquipmentStatusEnum::INSTALL_IN_PROCESS]))
        {!! badge_render(
            __('install_equipments.class_' . $d->status),
            __('install_equipments.status_' . $d->status) . ' (' . $d->day_amount . ') วัน',
            'text-' . __('install_equipments.class_' . $d->status),
        ) !!}
        @else
        {!! badge_render(
            __('install_equipments.class_' . $d->status),
            __('install_equipments.status_' . $d->status),
            'text-' . __('install_equipments.class_' . $d->status),
        ) !!}
        @endif
    @endif
@endsection
@push('styles')
    <style>
        .block-link-list {
            border-radius: 0.25rem;
            background: #F1F4F9 !important;
            border: 1px solid #CBD4E1 !important;
            border-radius: 6px;
        }
        .block-link-list .block {
            background: #F1F4F9 !important;
            border: 0px solid !important;
            margin-bottom: 0px !important;
        }
    </style>
@endpush
@section('content')
    <form id="save-form">
        {{-- @include('admin.install-equipments.sections.info') --}}
        @include('admin.install-equipments.sections.car-info')
        @include('admin.install-equipments.sections.accessory')
        <x-forms.hidden id="id" :value="$d->id" />

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.install-equipments.index',
                    'view' => ($mode == MODE_VIEW) ? true : null,
                    'manage_permisssion' => Actions::Manage . '_' . Resources::InstallEquipment
                    ]" 
                />
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => $store_uri,
])

@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')

@if ($mode == MODE_VIEW)
@include('admin.components.upload-image', [
    'id' => 'attachment',
    'max_files' => 10,
    'mock_files' => $install_equipment_files ?? [],
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'view_only' => true
]) 
@else
@include('admin.components.upload-image', [
    'id' => 'attachment',
    'max_files' => 10,
    'mock_files' => $install_equipment_files ?? [],
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
])
@endif

@include('admin.install-equipments.scripts.accessory-script')
@include('admin.install-equipments.scripts.accessory-select2')
@include('admin.install-equipments.scripts.update-datetime-script')


@include('admin.components.select2-ajax', [
    'id' => 'po_id',
    'url' => route('admin.util.select2-install-equipment.car-exist-purchase-orders'),
])

@include('admin.components.select2-ajax', [
    'id' => 'bom_id',
    'modal' => '#modal-bom',
    'url' => route('admin.util.select2-install-equipment.accessory-boms'),
])

@push('scripts')
    <script>
        $('#created_at').prop('disabled', true);
        $('#created_by').prop('disabled', true);
        $('#expected_end_date').prop('disabled', true);
        // $('#start_date').prop('disabled', true);
        // $('#install_day_amount').prop('disabled', true);

        const mode = @if (isset($mode)) @json($mode) @else null @endif;
        const ie_status = @if (isset($d->status)) @json($d->status) @else null @endif;
        const MODE_UPDATE = '{{ MODE_UPDATE }}';
        const MODE_VIEW = '{{ MODE_VIEW }}';
        if ([MODE_UPDATE, MODE_VIEW].includes(mode)) {
            $('#po_id').prop('disabled', true);
            $('#car_code').prop('disabled', true);
            $('#license_plate').prop('disabled', true);
            $('#engine_no').prop('disabled', true);
            $('#chassis_no').prop('disabled', true);
            // $('#remark').prop('disabled', true);
            if (ie_status === '{{ InstallEquipmentStatusEnum::INSTALL_COMPLETE }}') {
               $('#temp_status').prop('disabled', true);
               $('#end_date').prop('disabled', true);
            }
            if ([
                '{{ InstallEquipmentStatusEnum::INSTALL_COMPLETE }}', 
                '{{ InstallEquipmentStatusEnum::INSTALL_IN_PROCESS }}',
                '{{ InstallEquipmentStatusEnum::OVERDUE }}',
                '{{ InstallEquipmentStatusEnum::DUE }}',
                '{{ InstallEquipmentStatusEnum::INSTALL_COMPLETE }}',
                '{{ InstallEquipmentStatusEnum::INSPECT_IN_PROCESS }}',
                '{{ InstallEquipmentStatusEnum::COMPLETE }}',
            ].includes(ie_status)) {
                $('#start_date').prop('disabled', true);
                $('#install_day_amount').prop('disabled', true);
                $('#remark').prop('disabled', true);
            }
        }
        if ([MODE_VIEW].includes(mode)) {
            $('#start_date').prop('disabled', true);
            $('#install_day_amount').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#temp_status').prop('disabled', true);
        }

        async function getCarDetail(car_id) {
            try {
                const response = await axios.get("{{ route('admin.install-equipments.car-detail') }}", {
                    params: { car_id: car_id }
                });
                return response.data;
            } catch (error) {
                return null;
            }
        }

        function assignOptions(car, except_id)
        {
            if (except_id != 'car_code') {
                option_text = car.code ?? "{{ __('install_equipments.not_register') }}";
                var tempCodeOption = new Option(option_text, car.id, true, true);
                $("#car_code").append(tempCodeOption).trigger('change');
            }

            if (except_id != 'license_plate') {
                option_text = car.license_plate ?? "{{ __('install_equipments.not_register') }}";
                var tempLicensePlateOption = new Option(option_text, car.id, true, true);
                $("#license_plate").append(tempLicensePlateOption).trigger('change');
            }

            if (except_id != 'chassis_no') {
                option_text = car.chassis_no ?? "{{ __('install_equipments.not_register') }}";
                var tempChasisOption = new Option(option_text, car.id, true, true);
                $("#chassis_no").append(tempChasisOption).trigger('change');
            }

            if (except_id != 'engine_no') {
                option_text = car.engine_no ?? "{{ __('install_equipments.not_register') }}";
                var tempEngineOption = new Option(option_text, car.id, true, true);
                $("#engine_no").append(tempEngineOption).trigger('change');
            }
        }

        function clearOptions()
        {
            $("#car_code").val(null).trigger('change');
            $("#license_plate").val(null).trigger('change');
            $("#chassis_no").val(null).trigger('change');
            $("#engine_no").val(null).trigger('change');
        }

        // var car_select2_arr = ['car_code', 'license_plate', 'engine_no', 'chassis_no'];
        // $("#car_id").forEach(element => {
            $("#license_plate").select2({
                placeholder: "{{ __('lang.select_option') }}",
                allowClear: true,
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return "{{ route('admin.util.select2-install-equipment.cars-by-po') }}";
                    },
                    type: 'GET',
                    data: function (params) {
                        let po_id = $("#po_id").val();
                        return {
                            po_id: po_id,
                            s: params.term,
                            // column: element,
                        }
                    },
                    processResults: function (data) {
                        // data = data.map(item => {
                        //     const container = {};
                        //     container.id = item.id;
                        //     container.text = item.license_plate;
                        //     if (element === 'car_code') {
                        //         container.text = item.code;
                        //     }
                        //     if (element === 'license_plate') {
                        //         container.text = item.license_plate;
                        //     }
                        //     if (element === 'engine_no') {
                        //         container.text = item.engine_no;
                        //     }
                        //     if (element === 'chassis_no') {
                        //         container.text = item.chassis_no;
                        //     }
                        //     return container;
                        // });
                        
                        return {
                            results: data
                        };
                    },
                }
            });
        // });

        $("#license_plate").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.install-equipments.accessory-car-list') }}", {
                params: {
                    car_id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    var accessory_list = response.data.data;
                    addInstallEquipmentVue.addAccessorylist(accessory_list);
                }
            });
        });

        $("#po_id").on('select2:select', function(e) {
            clearOptions();
        });

        $("#btn-save-form-install-equipment").on("click", function() {
            let storeUri = "{{ route('admin.install-equipments.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }
            if (window.addInstallEquipmentVue) {
                let delete_install_equipment_ids = window.addInstallEquipmentVue.pending_install_equipment_ids;
                if (delete_install_equipment_ids && (delete_install_equipment_ids.length > 0)) {
                    delete_install_equipment_ids.forEach(function(delete_install_equipment_id) {
                        formData.append('delete_install_equipment_ids[]', delete_install_equipment_id);
                    });
                }
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
