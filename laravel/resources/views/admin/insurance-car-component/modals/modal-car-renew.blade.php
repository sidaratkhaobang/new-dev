<div id="modal-cmi-renew" class="modal fade" tabindex="-1" aria-labelledby="modal-cmi-renew" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="save-form-renew">
                    <x-forms.hidden id="id" :value="$d->id"/>
                    <x-forms.hidden id="type" :value="$type"/>
                    <div class="block-content">
                        <div class="row push mb-4">
                            @include('admin.components.block-header',[
            'text' =>   'ต่ออายุ พรบ.'   ,
           'block_icon_class' => 'icon-document',
           ])
                            <div class="col-sm-3">
                                <x-forms.select-option :value="'RenewCmi'" id="modal-renew-cmi-jobtype"
                                                       :list="$insurance_job_type_list"
                                                       :label="__('insurance_car.job_type')"
                                                       :optionals="['required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.input-new-line id="modal_renew_cmi_year" :value="$year_renew.'ปี'" :label="__('insurance_car.cmi_year')" :optionals="['required' => false]" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_startdate" :value="null"
                                                    :label="__('insurance_car.insurance_start_date')"
                                                    :optionals="['date_enable_time' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_enddate" :value="null"
                                                    :label="__('insurance_car.insurance_end_date')"
                                                    :optionals="['date_enable_time' => true]"/>
                            </div>
                        </div>
                        <div class="row push mb-1">
                            <div class="col-sm-3">
                                <x-forms.checkbox-inline id="modal-renew-cmi-insurance-status" :list="[
                                    [
                                        'id' => 1,
                                        'name' => 'ต้องการต่อกับบริษัทเดิม',
                                        'value' => 1,
                                    ],
                                ]" :label="null"
                                                         :value="null"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option :value="null" id="modal_renew_insurance_company"
                                                       :list="$insurer_list"
                                                       :label="__('insurance_car.company')"
                                                       :optionals="['required' => true]"/>
                            </div>
                        </div>
                        <div class="row push mb-4">
                            @include('admin.components.block-header',[
            'text' =>   'ข้อมูล พรบ. เดิม'   ,
           'block_icon_class' => 'icon-document',
           ])
                            <div class="col-sm-3">
                                @if($type == "CMI")
                                    <x-forms.input-new-line id="modal-renew-cmi-old-cminumber"
                                                            :value="$d?->policy_reference_cmi"
                                                            :label="__('insurance_car.cmi_number')"
                                                            :optionals="['required' => true]"/>
                                @elseif($type == "VMI")
                                    <x-forms.input-new-line id="modal-renew-cmi-old-cminumber"
                                                            :value="$d?->policy_reference_vmi"
                                                            :label="'เลขกรมธรรม์มาสเตอร์'"
                                                            :optionals="['required' => true]"/>
                                @endif
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option :value="$d?->insurer_id" id="modal-renew-cmi-old-company"
                                                       :list="$insurer_list" :label="__('insurance_car.company')"
                                                       :optionals="['required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal-renew-cmi-old-startdate" :value="$d?->term_start_date"
                                                    :label="__('insurance_car.insurance_start_date')"
                                                    :optionals="['date_enable_time' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal-renew-cmi-old-enddate" :value="$d?->term_end_date"
                                                    :label="__('insurance_car.insurance_end_date')"
                                                    :optionals="['date_enable_time' => true]"/>
                            </div>
                        </div>
                        <div class="block {{ __('block.styles') }}" style="border: none; box-shadow: none;">
                            <div class="block-content group-submit text-end">
                                <button type="button" class="btn btn-outline-secondary btn-custom-size"
                                        data-bs-dismiss="modal" style="min-width: 150px;cursor: pointer">{{ __('lang.back') }}</button>
                                <button type="button"
                                        class="btn btn-primary btn-custom-size btn-save-renew" style="min-width: 150px;cursor: pointer">
                                <i class="icon-save">

                                </i>    {{ __('lang.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#modal-renew-cmi-old-cminumber').prop('disabled', true);
        $('#modal-renew-cmi-old-company').prop('disabled', true);
        $('#modal-renew-cmi-old-startdate').prop('disabled', true);
        $('#modal-renew-cmi-old-enddate').prop('disabled', true);
        $('#modal-renew-cmi-jobtype').prop('disabled', true);
        $('#modal_renew_cmi_year').prop('disabled', true);
        $(document).on('change', "#modal-renew-cmi-insurance-status_0", function () {
            let insurance_status = $(this).is(':checked');
            if (insurance_status == true) {
                $('#modal_renew_insurance_company').parent().hide()
            } else {
                $('#modal_renew_insurance_company').parent().show()
            }
        })
    //     save renew
        function saveFormRenew(storeUri, formData, modalCallback) {
            showLoading();
            axios.post(storeUri, formData).then(response => {
                if (response.data.success) {
                    hideLoading();
                    @if (isset($save_callback))
                    if (typeof(modalCallback) == "function") {
                        modalCallback(response.data);
                    } else {
                        saveCallback(response.data);
                    }
                    @else
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            if (response.data.redirect === 'false'){
                                if (typeof(modalCallback) == "function") {
                                    modalCallback(response.data);
                                }
                            } else {
                                window.location.href = response.data.redirect;
                            }
                        } else {
                            window.location.reload();
                        }
                    });
                    @endif
                } else {
                    hideLoading();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                hideLoading();
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }
        $(".btn-save-renew").on("click", function() {
            let storeUri = "{{ route('admin.insurance-car.car-renew') }}";
            var formData = new FormData(document.querySelector('#save-form-renew'));
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
                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }
            saveFormRenew(storeUri, formData);
        });
    </script>
@endpush
