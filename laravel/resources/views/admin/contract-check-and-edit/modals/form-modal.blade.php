@push('custom_styles')
    <style>
        .body-add-btn {
            display: flex;
            flex-direction: column-reverse;
            justify-content: flex-start;
            align-items: flex-end;
        }
    </style>
@endpush
<div class="modal fade" id="modal-edit-contract" tabindex="-1" aria-labelledby="modal-edit-contract" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wage-job-modal-label">{{ __('ขอเปลี่ยนแปลงข้อมูลสัญญา') }} <span id="modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form">
                <x-forms.hidden id="contract_id" :value="null"/>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_request" :value="null" :list="$statusRequestList" :label="__('ประเภทการขอเปลี่ยนแปลง')"/>
                        </div>
                        <div class="col-sm-9 form-change-user-car" style="display: none">
                            <div class="row">
                                <dib class="col-sm-12">
                                    <x-forms.input-new-line id="change_user_car_description" :value="null" :label="__('หมายเหตุ')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                                </dib>
                            </div>
                        </div>
                        <div class="col-sm-9 form-change-address" style="display: none">
                            <div class="row">
                                <dib class="col-sm-4">
                                    <x-forms.input-new-line id="change_address_description" :value="null" :label="__('หมายเหตุ')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                                </dib>
                                <dib class="col-sm-8">
                                    <x-forms.input-new-line id="change_address_new_address" :value="null" :label="__('ที่อยู่ใหม่')" :optionals="[
                                        'placeholder' => __('lang.input.placeholder'),
                                        'required' => true
                                        ]"/>
                                </dib>
                            </div>
                        </div>
                        <div class="col-sm-9 form-transfer" style="display: none">
                            <div class="row">
                                <dib class="col-sm-12">
                                    <x-forms.input-new-line id="transfer_description" :value="null" :label="__('หมายเหตุ')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                                </dib>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 form-change-user-car" style="display: none">
                        <div class="col-sm-3">
                            <x-forms.select-option id="change_user_license_plate" :value="null" :list="null" :label="__('ทะเบียน')"/>
                        </div>
                        <dib class="col-sm-3">
                            <x-forms.input-new-line id="change_user_name" :value="null" :label="__('ชื่อผู้ใช้')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </dib>
                        <dib class="col-sm-3">
                            <x-forms.input-new-line id="change_user_phone" :value="null" :label="__('เบอร์โทร')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </dib>
                        <div class="col-sm-3 body-add-btn">
                            <button class="btn btn-primary btn-custom-size btn-add-user-info" href="#">
                                <i class="fa fa-plus-circle"></i> {{ __('เพิ่ม') }}
                            </button>
                        </div>
                    </div>
                    <div class="row mb-2 mt-4 form-change-user-car">
                        @include('admin.contract-check-and-edit.sections.table-change-car-user')
                    </div>
                    <div class="row mb-2 form-transfer" style="display: none">
                        <div class="col-sm-3">
                            <x-forms.select-option id="transfer_customer" :value="null" :list="null" :label="__('ลูกค้า')"/>
                        </div>
                        <dib class="col-sm-3">
                            <x-forms.input-new-line id="transfer_customer_phone" :value="null" :label="__('เบอร์โทร')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </dib>
                        <dib class="col-sm-6">
                            <x-forms.input-new-line id="transfer_customer_address" :value="null" :label="__('ที่อยู่')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </dib>
                    </div>
                    <div class="row">
                        @include('admin.contract-check-and-edit.sections.table-file-upload')
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-form-modal">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#status_request').on("change" , function (e) {
            const val = $(this).val();
            hideAllForm();

            if (val === '{{\App\Enums\ContractEnum::REQUEST_CHANGE_USER_CAR}}') {
                $('.form-change-user-car').show();
            }
            else if (val === '{{\App\Enums\ContractEnum::REQUEST_CHANGE_ADDRESS}}') {
                $('.form-change-address').show();
            }
            else if (val === '{{\App\Enums\ContractEnum::REQUEST_TRANSFER_CONTRACT}}') {
                $('.form-transfer').show();
                $('#transfer_customer_phone').prop('disabled' , true);
                $('#transfer_customer_address').prop('disabled' , true);
            }
        });

        $('#transfer_customer').on("change" , function (e) {
            const val = $(this).val();
            getCustomerDetail(val);
        });

        // $('#transfer_customer_code').on("change.select2" , function (e) {
        //     const val = $(this).val();
        //     getCustomerDetail(val);
        // });

        function getCustomerDetail(value) {
            // mySwal.showLoading();
            $.ajax({
                type: 'GET' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                } ,
                url: "{{ route('admin.util.select2-customer.customer-detail') }}" ,
                data: {
                    customer_id: value ,
                } ,
                success: function (data) {
                    swal.close();
                    console.log(data)
                    if (data.success) {
                        $('#transfer_customer_phone').val(data.data.tel);
                        $('#transfer_customer_address').val(data.data.address);
                    }
                } ,
                error: function (data) {
                    swal.close();
                    {{--mySwal.fire({--}}
                    {{--    title: "{{ __('lang.store_error_title') }}",--}}
                    {{--    html: 'ไม่สามารถโหลดข้อมูลลูกค้าได้<br>กรุณาลองใหม่อีกครั้งภายหลัง',--}}
                    {{--    icon: 'warning',--}}
                    {{--    confirmButtonText: "{{ __('lang.ok') }}",--}}
                    {{--});--}}
                }
            });
        }

        function hideAllForm() {
            $('.form-change-user-car').hide();
            $('.form-change-address').hide();
            $('.form-transfer').hide();
        }

        $(".btn-save-form-modal").on("click" , function () {
            const storeUri = "{{ route('admin.contract-check-and-edit.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));

            if (window.tableChangeCarUser) {
                let allData = window.tableChangeCarUser.data_list;
                if (allData && allData.length > 0) {
                    allData.forEach((data , index) => {
                        formData.append('change_user_car[' + index + '][car_id]' , data.car_id);
                        formData.append('change_user_car[' + index + '][car_license_plate]' , data.car_license_plate);
                        formData.append('change_user_car[' + index + '][car_user]' , data.car_user);
                        formData.append('change_user_car[' + index + '][car_phone]' , data.car_phone);
                    });
                }
            }

            appendDataFileToForm(formData);

            saveForm(storeUri , formData);
        });

        function appendDataFileToForm(formData) {
            if (window.tableFileUpload) {
                let allData = window.tableFileUpload.data_list;
                if (allData && allData.length > 0) {
                    allData.forEach((file) => {
                        if ((!file.saved) && (file.raw_file)) {
                            formData.append('contract_file[][file_name]' , file.name);
                            formData.append('contract_file[][file]' , file.raw_file);
                        }
                    });
                }

                //delete driver skill row
                let delete_media_file_ids = window.tableFileUpload.pending_delete_media_file_ids;
                if (delete_media_file_ids && (delete_media_file_ids.length > 0)) {
                    delete_media_file_ids.forEach(function (delete_media_file_id) {
                        formData.append('delete_media_file_ids[]' , delete_media_file_id);
                    });
                }
            }
        }
    </script>

    <script>
        window.tableChangeCarUser = new window.Vue({
            el: '#table-change-car-user' ,
            data: {
                data_list: [
                    // {
                    //     car_id : 'xxx',
                    //     car_license_plate : 'ABc 456',
                    //     car_user : 'EiEi',
                    //     car_phone : '0987654352',
                    // },
                ] ,
            } ,
            methods: {
                clearDataList: function () {
                    this.data_list = []
                } ,
                add(car_id , car_license_plate , car_user , car_phone) {
                    if (this.data_list.some(row => row.car_id === car_id)) {
                        warningAlert(`รถทะเบียน ${ car_license_plate } ไม่สามารถเพิ่มซ้ำได้`)
                        return
                    }
                    this.data_list.push({ car_id , car_license_plate , car_user , car_phone });
                } ,
                removeRow(index) {
                    this.data_list.splice(index , 1)
                } ,
            } ,
            props: ['title'] ,
        });

        $('.btn-add-user-info').click(function () {
            const car_id = $('#change_user_license_plate option:selected').val();
            const change_user_license_plate = $('#change_user_license_plate option:selected').text();
            const change_user_phone = $('#change_user_phone').val();
            const change_user_name = $('#change_user_name').val();

            if (car_id == null || car_id == "") {
                warningAlert('{{__('กรุณาเลือกรถ')}}');
                return false;
            }

            if (change_user_license_plate == null || change_user_license_plate == "") {
                warningAlert('{{__('กรุณาเลือกรถ')}}');
                return false;
            }

            if (change_user_name == null || change_user_name == "") {
                warningAlert('{{__('กรุณากรอกชื่อผู้ใช้')}}');
                return false;
            }

            if (change_user_phone == null || change_user_phone == "") {
                warningAlert('{{__('กรุณากรอกเบอร์โทร')}}');
                return false;
            }

            window.tableChangeCarUser.add(car_id , change_user_license_plate.trim() , change_user_name , change_user_phone);

            $('#change_user_license_plate').val(null).trigger('change');
            $('#change_user_phone').val('');
            $('#change_user_name').val('');
        });
    </script>
@endpush
