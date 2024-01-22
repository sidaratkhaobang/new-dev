<div class="block">
    <div class="block-content content-readonly">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.select-option id="{{ $id }}negotiation_type" :value="$item->type" :list="$negotiation_type_list" :label="__('compensations.negotiation_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="{{ $id }}negotiator" :value="$item->negotiator" :label="__('compensations.negotiator')" />
            </div>
        </div>
        @if ($item->type === NegotiationTypeEnum::INSURANCE)
            <div class="insurance-section-{{ $id }}">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.date-input id="{{ $id }}insurance_report_date" :value="$item->report_date" :label="__('compensations.report_date')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="{{ $id }}insurance_negotiation_result" :value="$item->result" :list="$negotiation_result_list" :label="__('compensations.negotiation_result')" 
                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}insurance_negotiation_amount" :value="$item->amount" :label="__('compensations.negotiation_amount')" 
                        :optionals="['required' => true, 'input_class' => 'number-format']" />
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}insurance_channel_result" :value="$item->channel_result" :label="__('compensations.channel_result')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}insurance_negotiation_person" :value="$item->person" :label="__('compensations.negotiation_person')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}insurance_negotiation_tel" :value="$item->tel" :label="__('compensations.negotiation_tel')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}insurance_negotiation_remark" :value="$item->remark" :label="__('lang.remark')"/>
                    </div>
                </div>
            </div>
        @endif
        @if ($item->type === NegotiationTypeEnum::OIC)
            <div class="oic-section-{{ $id }}">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.date-input id="{{ $id }}oic_report_date" :value="$item->report_date" :label="__('compensations.oic_report_date')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_receipt_no" :value="$item->receipt_no" :label="__('compensations.oic_report_no')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_sss_no" :value="$item->sss_no" :label="__('compensations.sss_no')"/>
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.date-input id="{{ $id }}oic_report_date" :value="$item->report_date" :label="__('compensations.nogatiation_date')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="{{ $id }}oic_negotiation_result" :value="$item->result" :list="$negotiation_result_list" :label="__('compensations.negotiation_result')" 
                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_negotiation_amount" :value="$item->amount" :label="__('compensations.negotiation_amount')"
                            :optionals="['required' => true, 'input_class' => 'number-format']" />
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_negotiation_person" :value="$item->person" :label="__('compensations.insurance_represent')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_negotiation_tel" :value="$item->tel" :label="__('compensations.insurance_contact')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="{{ $id }}oic_negotiation_remark" :value="$item->remark" :label="__('lang.remark')"/>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>