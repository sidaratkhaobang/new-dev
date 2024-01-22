<div class="modal fade" id="modal-create-contract-contract" tabindex="-1" aria-labelledby="modal-create-contract-contract" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wage-job-modal-label">{{ __('เลือกรถที่ต้องการจัดทำสัญญา') }} <span id="modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save-form-modal">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <x-forms.select-option id="license_plate_and_chassis_no" :value="[]" :list="$car_list" :label="__('ทะเบียนรถ/หมายเลขตัวถัง')" :optionals="['multiple' => true]"/>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom-size" data-bs-dismiss="modal"><i class="fa fa-rotate-left"></i> {{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-modal-create-contract">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
