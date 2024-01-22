@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)

@section('content')
    <form id="save-form">
        @include('admin.registers.section-face-sheets.select-car')
        <div id="face-sheet" v-cloak data-detail-uri="" data-title="">
            @include('admin.registers.section-face-sheets.car-list')
        </div>
        @include('admin.registers.modals.face-sheet-modal')
        <x-forms.hidden id="id" :value="$d->id" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row push me-1">
                <div class="col-sm-12 text-end">
                    @if (isset($url))
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route($url) }}">{{ __('lang.back') }}</a>
                    @endif
                    <button type="button" class="btn btn-primary" onclick="openModalFaceSheet()"><i class="icon-printer"></i>
                        {{ __('registers.select_car_face_sheet') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.registers.scripts.face-sheet-script')
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
            if(facesheet_status == "{{FaceSheetTypeEnum::REGISTER_NEW_CAR}}"){
                $("#topic_face_sheet").val('ใบนำส่งเอกสารจดทะเบียนรถใหม่');
            }else if(facesheet_status == "{{FaceSheetTypeEnum::RETURN_LEASING}}"){
                $("#topic_face_sheet").val('ใบนำส่งเอกสารคืนเล่มทะเบียน');
            }else{
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
            // console.log('1')
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
                    if (response.data.register.length > 0) {
                        addFaceSheetVue.addCar(response.data.register);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

        function exportExcel() {
            var topic_face_sheet = document.getElementById("topic_face_sheet").value;
            var facesheet_status = document.getElementById("facesheet_status").value;
            if (!topic_face_sheet || !facesheet_status) {
                return warningAlert("{{ __('lang.required_field_inform') }}");
            }
            var register_lists = [];
            var face_sheet_list = addFaceSheetVue.face_sheet_list;

            face_sheet_list.forEach((item, index) => {
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
                    topic_face_sheet: topic_face_sheet,
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

        function openModalFaceSheet(){
            $('#face-sheet-modal').modal('show');
        }
    </script>
@endpush
