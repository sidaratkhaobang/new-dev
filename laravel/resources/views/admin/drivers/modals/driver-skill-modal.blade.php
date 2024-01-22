<div class="modal fade" id="modal-driver-skill" tabindex="-1" aria-labelledby="modal-driver-skill" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-skill-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('drivers.driver_skill') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="driving_skill_field" :value="null" :list="null" :label="__('drivers.skill_name')"
                            :optionals="[
                                'ajax' => true,
                                'select_class' => 'js-select2 js-select2-custom',
                            ]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'skill_file'" :label="__('drivers.skill_file')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveDriverSkill()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
