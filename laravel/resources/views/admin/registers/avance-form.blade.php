@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
{{-- @section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('borrow_cars.status_' . $d->status . '_class'),
            __('borrow_cars.status_' . $d->status . '_text'),
            null,
        ) !!}
    @endif
@endsection --}}


@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />

    @include('admin.components.creator')
    <form id="save-form">

        @include('admin.registers.section-avances.select-car')
        <div id="avance" v-cloak data-detail-uri="" data-title="">
            @include('admin.registers.section-avances.car-list')
            @include('admin.registers.modals.avance-modal')
        </div>

        {{-- @include('admin.registers.sections.btn-group')
        @include('admin.registers.sections.purchase-order-detail')
        @include('admin.registers.sections.car-detail')
        @include('admin.registers.sections.registered-detail')
        @include('admin.registers.sections.avance-detail') --}}
        <x-forms.hidden id="id" :value="$d->id" />
        {{-- <x-forms.hidden id="status" :value="$d->status" /> --}}
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            {{-- @include('admin.registers.submit') --}}
            <div class="row push me-1">
                <div class="col-sm-12 text-end">
                    @if (isset($url))
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route($url) }}">{{ __('lang.back') }}</a>
                    @endif
                    {{-- @if (!isset($view)) --}}
                    {{-- <button type="submit" class="btn btn-primary btn-custom-size btn-save-form"><i class="icon-printer"></i> {{ __('registers.select_car_face_sheet') }}</button> --}}
                    <button type="button" class="btn btn-primary" onclick="openModalAvance()"><i
                            class="icon-menu-money"></i>
                        {{ __('registers.avance_withdraw') }}</button>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
{{-- @include('admin.components.form-save', [
    'store_uri' => route('admin.registers.export-excel-face-sheet'),
]) --}}
@include('admin.registers.scripts.avance-script')

{{-- @include('admin.transfer-cars.scripts.update-status') --}}
@include('admin.components.date-input-script')


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }


        function appendFormData() {
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
            return formData;
        }

        $("#facesheet_status").change(function() {
            var facesheet_status = $(this).val();
            if (facesheet_status == "{{ FaceSheetTypeEnum::REGISTER_NEW_CAR }}") {
                $("#topic_face_sheet").val('ใบนำส่งเอกสารจดทะเบียนรถใหม่');
            } else if (facesheet_status == "{{ FaceSheetTypeEnum::RETURN_LEASING }}") {
                $("#topic_face_sheet").val('ใบนำส่งเอกสารคืนเล่มทะเบียน');
            } else {
                $("#topic_face_sheet").val('');
            }

        });

        $(".btn-save-form-register").on("click", function() {
            let storeUri = "{{ route('admin.registers.store') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            formData.append('status', status);
            saveForm(storeUri, formData);
        });

        function checkCar() {
            console.log('1')
            var status = document.getElementById("status").value;
            var lot_no = document.getElementById("lot_no").value;
            var car_id = document.getElementById("car_id").value;
            var car_class = document.getElementById("car_class").value;
            console.log(status, lot_no, car_id, car_class)
            axios.get("{{ route('admin.registers.check-car') }}", {
                params: {
                    status: status,
                    lot_no: lot_no,
                    car_id: car_id,
                    car_class: car_class,
                }
            }).then(response => {
                if (response.data.success) {
                    // $("#zip_code").val(response.data.data.zip_code);
                    // console.log(response.data.register)
                    // console.log(response.data.register.length)
                    if (response.data.register.length > 0) {
                        addAvanceVue.addCar(response.data.register);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

        function exportExcel() {
            var register_lists = [];
            var face_sheet_list = addFaceSheetVue.face_sheet_list;

            face_sheet_list.forEach((item, index) => {
                // line.rental_line_extra.forEach((item, index) => {
                register_lists.push(item.id);
            });

            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.registers.export-excel-face-sheet') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    register_lists: register_lists,
                    // from_install_date: from_install_date,
                    // to_install_date: to_install_date,
                    // from_revoke_date: from_revoke_date,
                    // to_revoke_date: to_revoke_date,
                },
                success: function(result, status, xhr) {
                    var fileName = 'ใบปะหน้า.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;



                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#face-sheet-modal').modal('hide');
                    // window.location.href = "{{ route('admin.registers.index') }}";

                },
                error: function(result, status, xhr) {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });

        }

        function openModalAvance() {
            $('#avance-modal').modal('show');
        }

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form-modal'));
         
            return formData;
        }

        $(".btn-save-form-modal").on("click", function() {
            // let storeUri = "{{ route('admin.registers.store') }}";
            var avance_list = addAvanceVue.face_sheet_list;
            var avance_list_arr = {};

            avance_list.forEach((avance, index) => {
                if (!avance_list_arr[avance.id]) {
                    avance_list_arr[avance.id] = {
                        memo_no: [],
                        operation_fee_avance: [],
                        receipt_avance: [],
                        total: [],
                    };
                }

                avance_list_arr[avance.id].memo_no = avance.memo_no;
                avance_list_arr[avance.id].operation_fee_avance = avance.operation_fee_avance;
                avance_list_arr[avance.id].receipt_avance = avance.receipt_avance;
                avance_list_arr[avance.id].total = avance.total;
            });

            console.log(avance_list_arr);


            // var formData = new FormData(document.querySelector('#save-form-modal'));
            // console.log(avance_list_arr)
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.registers.export-excel-avance') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    avance_list_arr: avance_list_arr,
                    // from_install_date: from_install_date,
                    // to_install_date: to_install_date,
                    // from_revoke_date: from_revoke_date,
                    // to_revoke_date: to_revoke_date,
                },
                success: function(result, status, xhr) {
                    var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
        var customFileName = 'custom_filename.xlsx';

        if (contentDispositionHeader) {
            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
            var matches = contentDispositionHeader.match(filenameRegex);
            
            if (matches && matches.length > 1) {
                customFileName = matches[1].replace(/['"]/g, '');
            }
        }

                    console.log(customFileName)
                    var fileName = 'เบิกเงิน Advance.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = customFileName;



                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#avance-modal').modal('hide');
                    // window.location.href = "{{ route('admin.registers.index') }}";

                },
                error: function(result, status, xhr) {
                    console.log(result, status, xhr)
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
        });
    </script>
@endpush
