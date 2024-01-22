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
                        <div class="col-sm-4">
                            <x-forms.select-option id="status_request" :value="null" :list="$statusRequestList" :label="__('ประเภทการขอเปลี่ยนแปลง')"/>
                        </div>
                        <div class="col-sm-8">
                            <x-forms.input-new-line id="remark" :value="null" :label="__('หมายเหตุ')"/>
                        </div>
                    </div>

                    <div class="row form-change-address" style="display: none">
                        <div class="col-sm-9">
                            <x-forms.input-new-line id="change_address_new_address" :value="null" :label="__('ที่อยู่ใหม่')"/>
                        </div>
                    </div>

                    <div class="row mb-2 mt-4 form-change-user-car" style="display: none">
                        @include('admin.contracts.sections.table-change-car-user')
                    </div>

                    <div class="row form-transfer" style="display: none">
                        <dib class="col-sm-4">
                            <x-forms.input-new-line id="transfer_customer" :value="null" :label="__('ลูกค้า')"/>
                        </dib>
                        <dib class="col-sm-4">
                            <x-forms.input-new-line id="transfer_customer_phone" :value="null" :label="__('เบอร์โทร')"/>
                        </dib>
                        <dib class="col-sm-4">
                            <x-forms.input-new-line id="transfer_customer_address" :value="null" :label="__('ที่อยู่')"/>
                        </dib>
                    </div>
                    <div class="row mt-2">
                        @include('admin.contracts.table.table-file-upload')
                    </div>
{{--                    <div class="row mt-4">--}}
{{--                        <div class="col-sm-12 text-end">--}}
{{--                            <a href="{{ URL::current() }}" class="btn btn-outline-secondary btn-clear-search btn-custom-size"><i class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</a>--}}
{{--                            <button type="submit" class="btn btn-primary btn-custom-size"><i class="fa fa-magnifying-glass"></i> {{ __('lang.search') }}</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom-size" data-bs-dismiss="modal"><i class="fa fa-rotate-left"></i> {{ __('กลับ') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-modal-approve">{{ __('ยืนยัน') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-show-form-modal-non-approve">{{ __('ไม่ยืนยัน') }}</button>
            </div>
        </div>
    </div>
</div>
