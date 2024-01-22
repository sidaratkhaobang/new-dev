<div class="modal fade" id="modal-config" role="dialog" style="overflow:hidden;" aria-labelledby="modal-config">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >{{ __('config_approves.add_new') }}</h5>
            </div>
            <div class="modal-body pt-0">
                <form id="form-modal-config" method="POST" >
                    <div class="row mb-2">
                        <div class="col-sm-6" >
                            <x-forms.input-new-line id="m_seq" :value="''" :label="__('config_approves.seq')"
                                :optionals="['type' => 'number', 'min' => 0]" />
                        </div>
                        <div class="col-sm-6" >
                            <x-forms.radio-inline id="is_person" :value="null" :list="get_radio_value_yes_no()" :label="__('config_approves.is_person')"/>
                        </div>
                    </div>
                    <div class="row mb-2 row-user">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="m_user_id" :value="null" :list="[]" :label="__('config_approves.user')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                        <div class="col-sm-6" >
                            <x-forms.radio-inline id="m_is_super_user" :value="null" :list="get_radio_value_yes_no()" :label="__('config_approves.super_user')"/>
                        </div>
                    </div>
                    <div class="row mb-2 row-department">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="m_department_id" :value="null" :list="[]" :label="__('config_approves.department')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                        <div class="col-sm-6" >
                            <x-forms.radio-inline id="m_is_all_department" :value="null" :list="get_radio_value_yes_no()" :label="__('config_approves.all_department')"/>
                        </div>
                    </div>
                    <div class="row mb-2 row-section">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="m_section_id" :value="null" :list="[]" :label="__('config_approves.section')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                        <div class="col-sm-6" >
                            <x-forms.radio-inline id="m_is_all_section" :value="null" :list="get_radio_value_yes_no()" :label="__('config_approves.all_section')"/>
                        </div>
                    </div>
                    <div class="row mb-2 row-role">
                        <div class="col-sm-6" >
                            <x-forms.select-option id="m_role_id" :value="null" :list="[]" :label="__('config_approves.role')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                            ]" />
                        </div>
                        {{-- <div class="col-sm-6" >
                            <x-forms.radio-inline id="is_all_role" :value="null" :list="get_radio_value_yes_no()" :label="__('config_approves.all_role')"/>
                        </div> --}}
                    </div>
                    <input type="hidden" name="config_type" id="config_type" value="" >
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search me-2" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-config">{{ __('config_approves.add_new') }}</button>
            </div>
        </div>
    </div>
</div>