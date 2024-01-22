<div id="modal-cmi-renew" class="modal fade" tabindex="-1" aria-labelledby="modal-cmi-renew" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <form id="save-form">
                    <x-forms.hidden id="modal_cmi_id" :value="$d->id" />
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
                                <x-forms.select-option :value="null" id="modal_renew_cmi_year" :list="[]"
                                                       :label="__('insurance_car.cmi_year')"
                                                       :optionals="['required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_startdate" :value="null"
                                                    :label="__('insurance_car.insurance_start_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_renew_cmi_enddate" :value="null"
                                                    :label="__('insurance_car.insurance_end_date')"/>
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
                                <x-forms.input-new-line id="modal-renew-cmi-old-cminumber" :value="null"
                                                        :label="__('insurance_car.cmi_number')"
                                                        :optionals="['required' => true]"/>

                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option :value="$d?->insurer_id" id="modal-renew-cmi-old-company"
                                                       :list="$insurer_list" :label="__('insurance_car.company')"
                                                       :optionals="['required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal-renew-cmi-old-startdate" :value="$d?->term_start_date"
                                                    :label="__('insurance_car.insurance_start_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal-renew-cmi-old-enddate" :value="$d?->term_end_date"
                                                    :label="__('insurance_car.insurance_end_date')"/>
                            </div>
                        </div>
                        <div class="block {{ __('block.styles') }}" style="border: none; box-shadow: none;">
                            <div class="block-content group-submit">
                                <x-forms.submit-group
                                    :optionals="['url' => 'admin.insurance-car.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCompanies]"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
