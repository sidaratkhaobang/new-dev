@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.select-option :value="$lot_no_search" id="lot_no_search" :list="null" :label="__('registers.lot_no')"
                        :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => $lot_no_search_text,
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$car_class_search" id="car_class_search" :list="null" :label="__('registers.car_class')"
                        :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => $car_class_search_text,
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$license_plate_search" id="license_plate_search" :list="null" :label="__('registers.engine_chassis_no')"
                        :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => $license_plate_search_text,
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status_search" :value="$status_search" :list="$status_register_list" :label="__('registers.status')" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::Register)
                <button type="button" class="btn btn-primary" onclick="openModalAvanceSelectCar()"><i
                        class="icon-menu-money"></i>
                    {{ __('registers.save_avance') }}</button>
                <button type="button" class="btn btn-primary" onclick="openModalFaceSheetSelectCar()"><i
                        class="icon-printer"></i>
                    {{ __('registers.face_sheet') }}</button>
                <div class="btn-group" style="position: sticky;left: 100%; min-width:295px;">
                    <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                        style="width: 220px;"><i class="icon-edit"></i>{{ __('registers.edit_multiple') }}</button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                        style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">ดาวน์โหลด
                                Templete
                            </button>
                        </li>
                        <li>
                            <label for="upload" class="dropdown-item btn-request file"
                                style="cursor: pointer;">อัปโหลดไฟล์</label>
                            <input id="upload" type="file" name="file[]"
                                style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                        </li>
                    </ul>
                </div>
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th>@sortablelink('lot_no', __('registers.lot_no'))</th>
                <th>@sortablelink('car.car_class', __('registers.car_class'))</th>
                <th>@sortablelink('car.chassis_no', __('registers.chassis_no'))</th>
                <th>@sortablelink('car.engine_no', __('registers.engine_no'))</th>
                <th>@sortablelink('car.waiting_document', __('registers.waiting_document'))</th>
                <th>@sortablelink('status', __('registers.status'))</th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->lot_no }}</td>
                    <td>{{ $d->car->carClass->full_name ?? null }}</td>
                    <td>{{ $d->car->engine_no ?? null }}</td>
                    <td>{{ $d->car->chassis_no ?? null }}</td>
                    <td>
                        <ul>
                            @if (!$d->document_date)
                                <li>ชุดจดทะเบียน</li>
                            @endif
                            @if (!$d->receive_registered_dress_date)
                                <li>ชุดแจ้ง</li>
                            @endif
                            @if (!$d->receive_cmi)
                                <li>พรบ.</li>
                            @endif
                            @if ($d->document_date && $d->receive_registered_dress_date && $d->receive_cmi)
                                -
                            @endif
                        </ul>
                    </td>
                    <td> {!! badge_render(
                        __('registers.status_' . $d->status . '_class'),
                        __('registers.status_' . $d->status . '_text'),
                        null,
                    ) !!}</td>
                    @if (in_array($d->status, [RegisterStatusEnum::PREPARE_REGISTER]))
                        <td class="sticky-col text-center">
                            <x-tables.dropdown :id="'dropdown-table'" :routes="[
                                'view_route' => route('admin.registers.show', [
                                    'register' => $d,
                                ]),
                                'edit_route' => route('admin.registers.edit', [
                                    'register' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::Register,
                                'manage_permission' => Actions::Manage . '_' . Resources::Register,
                            ]">
                            </x-tables.dropdown>
                        </td>
                    @elseif(in_array($d->status, [RegisterStatusEnum::REGISTERING]))
                        <td class="sticky-col text-center">
                            <x-tables.dropdown :id="'dropdown-table'" :routes="[
                                'view_route' => route('admin.registers.show-registered', [
                                    'register' => $d,
                                ]),
                                'edit_route' => route('admin.registers.edit-registered', [
                                    'register' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::Register,
                                'manage_permission' => Actions::Manage . '_' . Resources::Register,
                            ]">
                            </x-tables.dropdown>
                        </td>
                    @elseif(in_array($d->status, [RegisterStatusEnum::REGISTERED]))
                        <td class="sticky-col text-center">
                            <x-tables.dropdown :id="'dropdown-table'" :routes="[
                                'view_route' => route('admin.registers.show-registered', [
                                    'register' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::Register,
                                'manage_permission' => Actions::Manage . '_' . Resources::Register,
                            ]">
                            </x-tables.dropdown> 
                        </td>
                    @endif
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
    @include('admin.registers.modals.face-sheet-select-car-modal')
    @include('admin.registers.modals.face-sheet-modal')

    @include('admin.registers.modals.avance-select-car-modal')
    <div id="avance-selected" v-cloak data-detail-uri="" data-title="">
        @include('admin.registers.modals.avance-modal')
    </div>

    @include('admin.registers.modals.template-select-car-modal')
    @include('admin.registers.modals.template-import-car-modal')

@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'lot_no_search',
    'url' => route('admin.util.select2-register.lot-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_search',
    'url' => route('admin.util.select2-register.car-class-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'license_plate_search',
    'url' => route('admin.util.select2-register.license-plate-list'),
])

@include('admin.components.select2-ajax', [
    'id' => 'facesheet_status',
    'url' => route('admin.util.select2-register.get-status-facesheet'),
    'modal' => '#face-sheet-modal',
])

@php
    $modals = [
        'face-sheet-select-car-modal' => ['leasing', 'lot_no', 'car_class', 'car_id', 'status'],
        'avance-select-car-modal' => ['leasing_avance', 'lot_no_avance', 'car_class_avance', 'car_id_avance', 'status_avance'],
        'template-select-car-modal' => ['leasing_template', 'lot_no_template', 'car_class_template', 'car_id_template', 'status_template'],
    ];

    $url_mapping = [
        'leasing' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_avance' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_template' => 'admin.util.select2-finance.creditor-leasing-list',
        'lot_no' => 'admin.util.select2-register.lot-list',
        'lot_no_avance' => 'admin.util.select2-register.lot-list',
        'lot_no_template' => 'admin.util.select2-register.lot-list',
        'car_class' => 'admin.util.select2-register.car-class-list',
        'car_class_avance' => 'admin.util.select2-register.car-class-list',
        'car_class_template' => 'admin.util.select2-register.car-class-list',
        'car_id' => 'admin.util.select2-register.license-plate-list',
        'car_id_avance' => 'admin.util.select2-register.license-plate-list',
        'car_id_template' => 'admin.util.select2-register.license-plate-list',
        'status' => 'admin.util.select2-register.get-status',
        'status_avance' => 'admin.util.select2-register.get-status',
        'status_template' => 'admin.util.select2-register.get-status',
    ];
@endphp

@foreach ($modals as $modal => $ids)
    @foreach ($ids as $id)
        @include('admin.components.select2-ajax', [
            'id' => $id,
            'url' => route($url_mapping[$id]),
            'modal' => '#' . $modal,
        ])
    @endforeach
@endforeach



@push('scripts')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script>
        function openModalFaceSheetSelectCar() {
            $('#status').val('').change();
            $('#lot_no').val('').change();
            $('#leasing').val('').change();
            $('#car_id').val('').change();
            $('#car_class').val('').change();
            addFaceSheetVue.face_sheet_list = [];
            $('#face-sheet-select-car-modal').modal('show');
        }

        function openModalFaceSheet() {
            $('#facesheet_status').val('').change();
            $('#topic_face_sheet').val('').change();
            $('#face-sheet-select-car-modal').modal('hide');
            $('#face-sheet-modal').modal('show');
        }

        function openModalTemplateSelectCar() {
            $('#status_template').val('').change();
            $('#lot_no_template').val('').change();
            $('#leasing_template').val('').change();
            $('#car_id_template').val('').change();
            $('#car_class_template').val('').change();
            addFaceSheetVue.face_sheet_list = [];
            $('#template-select-car-modal').modal('show');
        }

        function openModalAvanceSelectCar() {
            addAvanceSelectedVue.face_sheet_list = [];
            addAvanceVue.face_sheet_list = [];
            $('#status_avance').val('').change();
            $('#lot_no_avance').val('').change();
            $('#leasing_avance').val('').change();
            $('#car_id_avance').val('').change();
            $('#car_class_avance').val('').change();
            $('#avance-select-car-modal').modal('show');
        }

        function clearSelectCar() {
            $('#status').val('').change();
            $('#lot_no').val('').change();
            $('#leasing').val('').change();
            $('#car_id').val('').change();
            $('#car_class').val('').change();
            $('#status_template').val('').change();
            $('#lot_no_template').val('').change();
            $('#leasing_template').val('').change();
            $('#car_id_template').val('').change();
            $('#car_class_template').val('').change();
            $('#status_avance').val('').change();
            $('#lot_no_avance').val('').change();
            $('#leasing_avance').val('').change();
            $('#car_id_avance').val('').change();
            $('#car_class_avance').val('').change();
        }

        function openModalAvance() {
            addAvanceSelectedVue.face_sheet_list = [];
            addAvanceSelectedVue.face_sheet_list = addAvanceVue.face_sheet_list;
            $('#avance-select-car-modal').modal('hide');
            $('#avance-modal').modal('show');
        }

        function BackToModalFaceSheetSelectCar() {
            $('#face-sheet-modal').modal('hide');
            $('#face-sheet-select-car-modal').modal('show');
        }

        function BackToModalAvanceSelectCar() {
            // addAvanceSelectedVue.face_sheet_list = [];
            $('#avance-modal').modal('hide');
            $('#avance-select-car-modal').modal('show');
        }

        var ExcelToJSON = function() {

            this.parseExcel = function(file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary',
                        blankrows: false
                    });
                    var json_object = [];
                    workbook.SheetNames.forEach(function(sheetName) {
                        var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[
                            sheetName]);
                        json_object = JSON.stringify(XL_row_object);
                    })

                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.registers.import-excel') }}",
                        data: {
                            json_object: JSON.parse(json_object),
                        },
                        success: function(data) {
                            if (data.success) {
                                // addTemplateImportVue.face_sheet_list = [];
                                addTemplateImportVue.importData(data);
                            } else {
                                return warningAlert("{{ __('registers.validate_import') }}");
                            }
                        },

                    });
                    // } else {
                    //     $.ajax({
                    //         type: 'GET',
                    //         url: "{{ route('admin.registers.import-excel') }}",
                    //         data: {
                    //             json_object: JSON.parse(json_object)
                    //         },
                    //         success: function(data) {
                    //             addImportCarVue.test(data.success);
                    //         }
                    //     });
                    // }
                };

                reader.onerror = function(ex) {
                    // console.log(ex);
                };

                reader.readAsBinaryString(file);
            };
        };

        function handleFileSelect(evt) {

            var files = evt.target.files;
            var xl2json = new ExcelToJSON();
            xl2json.parseExcel(files[0]);
        }

        document.getElementById('upload').addEventListener('change', handleFileSelect, false);
    </script>
@endpush
