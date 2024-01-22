@push('custom_styles')
    <style>
        .body-add-btn {
            display: flex;
            flex-direction: column-reverse;
            justify-content: flex-start;
            align-items: flex-end;
        }

        #modal-form-non-approve .modal-dialog {
            width: 800px!important;
        }
    </style>
@endpush
<div class="modal fade" id="modal-form-non-approve" tabindex="-1" aria-labelledby="modal-edit-contract" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wage-job-modal-label">{{ __('เหตุผลไม่ยืนยันการเปลี่ยนแปลงข้อมูล') }} <span id="modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form">
                <x-forms.hidden id="non_approve_contract_id" :value="null"/>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <x-forms.text-area-new-line id="reason" :value="null" :label="__('หมายเหตุ')" :optionals="['row' => 3]"/>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom-size" data-bs-dismiss="modal"><i class="fa fa-rotate-left"></i> {{ __('กลับ') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-modal-non-approve">{{ __('บันทึก') }}</button>
            </div>
        </div>
    </div>
</div>
