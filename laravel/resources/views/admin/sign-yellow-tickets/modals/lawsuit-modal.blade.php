<div class="modal fade" id="modal-lawsuit" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static"
    aria-labelledby="modal-lawsuit" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lawsuit-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.car_table') }}</h4>
                <hr> --}}
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.date-input id="incident_date" :value="$d->incident_date" :label="__('sign_yellow_tickets.incident_date')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="lawsuit_detail" :value="null" :label="__('sign_yellow_tickets.lawsuit')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="province" :value="null" :list="null" :label="__('sign_yellow_tickets.accident_place')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true,'required' => true]" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount" :value="$d->amount" :label="__('sign_yellow_tickets.amount')"
                            :optionals="['input_class' => 'number-format', 'required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="responsible" :value="null" :list="null" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]"
                            :label="__('sign_yellow_tickets.responsible')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="training" :value="null" :list="[]" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true,'required' => true]"
                            :label="__('sign_yellow_tickets.training')" />
                    </div>

                </div>
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver_type" :value="null" :label="__('sign_yellow_tickets.driver_type')"
                            :optionals="[]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver" :value="null" :label="__('sign_yellow_tickets.driver')"
                            :optionals="[]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="tel" :value="null" :label="__('sign_yellow_tickets.tel')"
                            :optionals="[]" />
                    </div>
                </div>
                <div class="row push mb-3" @if (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_WRONG,SignYellowTicketStatusEnum::WAITING_PAY_DLT,SignYellowTicketStatusEnum::WAITING_PAY_FINE])) style="display: visible;" @else style="display: none;" @endif>
                    <div class="col-sm-4">
                        <x-forms.select-option id="mistake" :value="null" :list="[]" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true,'required' => true]"
                            :label="__('sign_yellow_tickets.is_wrong')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="notification_date" :value="$d->notification_date" :label="__('sign_yellow_tickets.notification_date')"
                            :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row push mb-3 waiting_pay_dlt"  @if (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_DLT,SignYellowTicketStatusEnum::WAITING_PAY_FINE])) style="display: visible;" @else style="display: none;" @endif>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="receipt_no" :value="null" :label="__('sign_yellow_tickets.receipt_no')"
                            :optionals="[]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="payment_fine_date" :value="$d->payment_fine_date" :label="__('sign_yellow_tickets.payment_fine_date')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount_total" :value="null" :label="__('sign_yellow_tickets.amount_total')"
                            :optionals="['input_class' => 'number-format', 'required' => true]" />
                    </div>
                </div>

                <div class="row push mb-4 waiting_pay_dlt"  @if (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_DLT,SignYellowTicketStatusEnum::WAITING_PAY_FINE])) style="display: visible;" @else style="display: none;" @endif>
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'receipt_files'" :label="__('purchase_orders.attach_files')" />
                    </div>
                </div>

                <div class="row push mb-4 waiting_pay_fine" @if (strcmp($d->status, SignYellowTicketStatusEnum::WAITING_PAY_FINE) == 0) style="display: visible;" @else style="display: none;" @endif>
                    <div class="col-sm-4">
                        <x-forms.select-option id="is_payment_fine" :value="null" :list="[]"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true, 'required' => true]" :label="__('sign_yellow_tickets.payment')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="payment_date" :value="$d->payment_date" :label="__('sign_yellow_tickets.paid_date')"
                            :optionals="['required' => true]" />
                    </div>
                </div>

            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                @if (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_WRONG]))
                    <button type="button" class="btn btn-primary btn-save-car"
                        onclick="saveMistake()">{{ __('lang.save') }}</button>
                @elseif (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_DLT]))
                    <button type="button" class="btn btn-primary btn-save-car"
                        onclick="savePaid()">{{ __('lang.save') }}</button>
                @elseif (in_array($d->status, [SignYellowTicketStatusEnum::WAITING_PAY_FINE]))
                    <button type="button" class="btn btn-primary btn-save-car"
                        onclick="savePaidFine()">{{ __('lang.save') }}</button>
                @else
                    <button type="button" class="btn btn-primary btn-save-car"
                        onclick="saveLawsuit()">{{ __('lang.save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
