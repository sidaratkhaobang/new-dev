<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('vmi_cars.act_detail'),

    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="year_act" :value="$d->year . ' ' . __('lang.year')" 
                    :label="__('cmi_cars.year_act')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="insurance_type" :value="$d->insurance_type" :list="$insurance_type_list"
                    :label="__('vmi_cars.insurance_type')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.select-option :value="$d->insurer_id" id="insurer_id" :list="$insurer_list" :label="__('cmi_cars.insurance_company')" 
                    :optionals="['required' => true]" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.select-option :value="$d->beneficiary_id" id="beneficiary_id" :list="$leasing_list" :label="__('cmi_cars.beneficiary')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.select-option :value="$d->insurance_package_id" id="insurance_package_id" :list="null" :label="__('vmi_cars.condition')"
                    :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $package_name]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')" />
            </div>    
        </div>
        <hr>
        <div class="row  mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="send_date" :value="$d->send_date" :label="__('cmi_cars.delivery_doc_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_date" :value="$d->receive_date" :label="__('cmi_cars.receive_doc_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="check_date" :value="$d->check_date" :label="__('cmi_cars.check_date')" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="policy_reference_vmi" :value="$d->policy_reference_vmi" :label="__('vmi_cars.policy_reference_vmi')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="endorse_vmi" :value="$d->endorse_vmi" :label="__('vmi_cars.endorse_vmi')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="policy_reference_child_vmi" :value="$d->policy_reference_child_vmi" :label="__('vmi_cars.policy_reference_child_vmi')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="term_start_date" :value="$d->term_start_date" :label="__('cmi_cars.policy_start_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="term_end_date" :value="$d->term_end_date" :label="__('cmi_cars.policy_end_date')" />
            </div>
        </div>
    </div>
</div>
