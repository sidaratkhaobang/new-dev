<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('cmi_cars.premium'),

    ])
    <div class="block-content">
        <div class="table-wrap db-scroll mb-4">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th>{{ __('cmi_cars.refund') }}</th>
                        {{-- <th>{{ __('cmi_cars.discount') }}</th> --}}
                        <th>{{ __('cmi_cars.refund_stamp') }}</th>
                        <th>{{ __('cmi_cars.refund_vat') }}</th>
                        <th>{{ __('cmi_cars.refund_total') }}</th>
                        <th>{{ __('cmi_cars.withholding_tax_1') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <x-forms.input-new-line id="refund" :value="$refund_summary['premium_text']" :label="null" 
                                :optionals="['input_class' => 'number-format']" />
                        </td>
                        {{-- <td>
                            <x-forms.input-new-line id="discount" :value="$cancel_premium_summary['discount_text']" :label="null" 
                                :optionals="['input_class' => 'number-format']" />
                        </td> --}}
                        <td>
                            <x-forms.input-new-line id="refund_stamp" :value="$refund_summary['stamp_duty_text']" :label="null"
                                :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="refund_vat" :value="$refund_summary['tax_text']" :label="null"
                                :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="refund_total" :value="$refund_summary['premium_total_text']" :label="null"
                                :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="refund_withholding_tax" :value="$refund_summary['withholding_tax_text']" :label="null" 
                                :optionals="['input_class' => 'number-format']" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="credit_note" :value="$cancel_insurance->credit_note" :label="__('cmi_cars.credit_note')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="credit_note_date" :value="$cancel_insurance->credit_note_date" :label="__('cmi_cars.credit_note_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="refund_check_date" :value="$cancel_insurance->check_date" :label="__('cmi_cars.check_date_en')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="send_account_date" :value="$cancel_insurance->send_account_date" :label="__('cmi_cars.send_account_date')" 
                    :optionals="['date_enable_time' => true]" />
            </div>
        </div>
    </div>
</div>