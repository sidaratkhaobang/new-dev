<div class="modal fade" id="modal-appointment" tabindex="-1" aria-labelledby="modal-appointment" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="min-width:60%">
        <div class="modal-content">
            <div class="modal-header">
                {{-- <h5 class="modal-title" id="appointment-modal-label"><i class="icon-menu-calendar-2 me-1"></i>แจ้งนัดหมาย</h5> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.components.block-header', [
                    'text' => __('accident_orders.appointment'),
                    'block_icon_class' => 'icon-menu-calendar-2',
                ])
                <div class="block-content">
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="topic" :value="$d->topic" :label="__('accident_orders.topic')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="appointment_date_modal" name="appointment_date_modal"
                                :value="$accident_order->appointment_date" :label="__('accident_orders.appointment_date')" :optionals="['date_enable_time' => true, 'required' => true]" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="appointment_place_modal" :value="$accident_order->appointment_place" :label="__('accident_orders.appointment_place')"
                                :optionals="['required' => true]" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="cradle_id_model" :value="$accident_order->cradle_id" :list="$garage_list"
                                :label="__('accident_orders.garage_repair')" />
                        </div>
                        <div class="col-sm-9">
                            <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('accident_orders.remark')" />
                        </div>
                    </div>
                </div>
                @include('admin.components.block-header', [
                    'text' => __('accident_orders.appointment_name'),
                    // 'block_icon_class' => 'icon-menu-calendar-2',
                ])
                <div class="block-content">
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            {{-- <x-forms.select-option id="true_leasing" :value="null" :list="$user_list" :label="__('accident_orders.true_leasing')" /> --}}
                            <x-forms.select-option id="true_leasing" :list="null" :value="null"
                                :label="__('accident_orders.true_leasing')" :optionals="[
                                    'ajax' => true,
                                    'required' => true,
                                ]" />
                            {{-- <x-forms.input-new-line id="true_leasing" :value="$d->true_leasing" :label="__('accident_orders.true_leasing')" /> --}}
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="true_leasing_email" :value="$d->true_leasing_email" :label="__('accident_orders.email')"
                                :optionals="['required' => true]" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            {{-- <x-forms.select-option id="insurance" :value="null" :list="$insurer_list" :label="__('accident_orders.insurance')" /> --}}
                            <x-forms.select-option id="insurance" :list="null" :value="null"
                                :label="__('accident_orders.insurance')" :optionals="[
                                    'ajax' => true,
                                    'required' => true,
                                ]" />
                            {{-- <x-forms.input-new-line id="insurance" :value="$d->insurance" :label="__('accident_orders.insurance')" /> --}}
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="insurance_email" :value="$d->insurance_email" :label="__('accident_orders.email')"
                                :optionals="['required' => true]" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="customer" :value="$d->customer" :label="__('accident_orders.customer')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="customer_email" :value="$d->customer_email" :label="__('accident_orders.email')"
                                :optionals="['required' => true]" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                @if (!isset($view))
                    <button type="button" class="btn btn-primary" onclick="sendMail()"
                        id="save_appointment">{{ __('lang.save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
