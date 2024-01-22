<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('cmi_cars.act_detail'),

    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="year_act" :value="$d->year . ' ' . __('lang.year')" 
                    :label="__('cmi_cars.year_act')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option :value="$d->insurer_id" id="insurer_id" :list="$insurer_list" :label="__('cmi_cars.insurance_company')" 
                    :optionals="['required' => true]" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.select-option :value="$d->beneficiary_id" id="beneficiary_id" :list="$leasing_list" :label="__('cmi_cars.beneficiary')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')" />
            </div>    
        </div>
        <hr>
        <div class="row  mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="send_date" :value="$d->send_date" :label="__('cmi_cars.delivery_doc_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_date" :value="$d->receive_date" :label="__('cmi_cars.receive_doc_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="check_date" :value="$d->check_date" :label="__('cmi_cars.check_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
        </div>
        <hr>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="number_bar_cmi" :value="$d->number_bar_cmi" :label="__('cmi_cars.cmi_bar_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="policy_reference_cmi" :value="$d->policy_reference_cmi" :label="__('cmi_cars.cmi_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="endorse_cmi" :value="$d->endorse_cmi" :label="__('cmi_cars.endorsement')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="term_start_date" :value="$d->term_start_date" :label="__('cmi_cars.policy_start_date')" 
                    :optionals="['date_enable_time' => false]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="term_end_date" :value="$d->term_end_date" :label="__('cmi_cars.policy_end_date')" 
                    :optionals="['date_enable_time' => false]" />
            </div>
        </div>
    </div>
</div>
