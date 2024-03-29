<div id="premium_details" class="block {{ __('block.styles') }}">
    <div class="block-header">
        <h3 class="block-title"><i class="fa fa-file-lines me-2"></i> {{ __('cmi_cars.act_detail') }}</h3>
        <div class="block-options">
            <div class="block-options-item">
            </div>
        </div>
    </div>
    <div class="block-content">
        <div class="block-content">
            <div class="table-wrap db-scroll mb-4">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>{{ __('cmi_cars.premium_net') }}</th>
                        <th>{{ __('cmi_cars.discount') }}</th>
                        <th>{{ __('cmi_cars.stamp_duty') }}</th>
                        <th>{{ __('cmi_cars.tax') }}</th>
                        <th>{{ __('cmi_cars.premium_total') }}</th>
                        <th>{{ __('cmi_cars.withholding_tax_1') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <x-forms.input-new-line id="premium" :value="$premium_summary['premium_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="discount" :value="$premium_summary['discount_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="stamp_duty" :value="$premium_summary['stamp_duty_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="tax" :value="$premium_summary['tax_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="premium_total" :value="$premium_summary['premium_total_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                        <td>
                            <x-forms.input-new-line id="withholding_tax" :value="$premium_summary['withholding_tax_text']" :label="null"
                                                    :optionals="['input_class' => 'number-format']" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="statement_no" :value="$d->statement_no" :label="__('cmi_cars.statement_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="tax_invoice_no" :value="$d->tax_invoice_no" :label="__('cmi_cars.tax_invoice_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="statement_date" :value="$d->statement_date" :label="__('cmi_cars.statement_date')" :optionals="['date_enable_time' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="account_submission_date" :value="$d->account_submission_date" :label="__('cmi_cars.account_submission_date')" :optionals="['date_enable_time' => true]"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="operated_date" :value="$d->operated_date" :label="__('cmi_cars.operated_date')" :optionals="['date_enable_time' => true]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$d->status_pay_premium" id="status_pay_premium" :list="$premium_status_list"
                                           :label="__('cmi_cars.status_pay_premium')" />
                </div>
            </div>
        </div>
    </div>
</div>
