<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.header_title'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="title" :value="$d->title" :label="__('litigations.title')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="case" :value="$d->case" :list="$case_name_list" :label="__('litigations.case')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="case_type" :value="$d->case_type" :list="$case_type_list" :label="__('litigations.case_type')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="tls_type" :value="$d->tls_type" :list="$tls_type_list" :label="__('litigations.tls_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="accuser_defendant" :value="$d->accuser_defendant" :label="__('litigations.plaintiff_defendent')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="incident_date" :value="$d->incident_date" :label="__('litigations.incident_date')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="consultant" :value="$d->consultant" :label="__('litigations.consultant')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="fund" :value="$d->fund" :label="__('litigations.fund')" 
                    :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="responsible_person_id" :value="$d->responsible_person_id" :list="null" :label="__('litigations.responsible_person')"
                    :optionals="['ajax' => true, 'default_option_label' => $responsible_person_name]"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="legal_service_provider" :value="$d->legal_service_provider" :label="__('litigations.legal_service_provider')"  />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="legal_service_fee" :value="$d->legal_service_fee" :label="__('litigations.legal_service_fee')" 
                    :optionals="['input_class' => 'number-format']" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="legal_note" :value="$d->legal_note" :label="__('litigations.legal_note')" />
            </div>
        </div>
    </div>
</div>
