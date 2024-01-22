<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('compensations.negotiation_detail'),
    ])
    <div class="block-content">
        @foreach ($negotiation_list as $item)
            @include('admin.compensations.sections.negotiation-block', [
                'id' => $item->id,
                'item' => $item,
            ])
        @endforeach
        @if ($d->status == CompensationStatusEnum::UNDER_NEGOTIATION)
        <div class="block">
            <div class="block-content">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.select-option id="negotiation_type" :value="null" :list="$negotiation_type_list" :label="__('compensations.negotiation_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="negotiator" :value="null" :label="__('compensations.negotiator')" />
                    </div>
                </div>
                <div class="insurance-section" style="display: none;">
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.date-input id="insurance_report_date" :value="null" :label="__('compensations.report_date')"  :optionals="['required' => true]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="insurance_negotiation_result" :value="null" :list="$negotiation_result_list" :label="__('compensations.negotiation_result')" 
                            :optionals="['required' => true]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_negotiation_amount" :value="null" :label="__('compensations.negotiation_amount')" 
                            :optionals="['required' => true, 'input_class' => 'number-format']" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_channel_result" :value="null" :label="__('compensations.channel_result')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_negotiation_person" :value="null" :label="__('compensations.negotiation_person')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_negotiation_tel" :value="null" :label="__('compensations.negotiation_tel')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_negotiation_remark" :value="null" :label="__('lang.remark')"/>
                        </div>
                    </div>
                </div>
                <div class="oic-section" style="display: none;">
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.date-input id="oic_report_date" :value="null" :label="__('compensations.oic_report_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_receipt_no" :value="null" :label="__('compensations.oic_report_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_sss_no" :value="null" :label="__('compensations.sss_no')"/>
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.date-input id="oic_report_date" :value="null" :label="__('compensations.nogatiation_date')"
                            :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="oic_negotiation_result" :value="null" :list="$negotiation_result_list" :label="__('compensations.negotiation_result')" 
                            :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_negotiation_amount" :value="null" :label="__('compensations.negotiation_amount')"
                                :optionals="['required' => true, 'input_class' => 'number-format']" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_negotiation_person" :value="null" :label="__('compensations.insurance_represent')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_negotiation_tel" :value="null" :label="__('compensations.insurance_contact')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="oic_negotiation_remark" :value="null" :label="__('lang.remark')"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

