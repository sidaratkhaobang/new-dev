<div class="modal fade" id="modal-repair-list" tabindex="-1" aria-labelledby="modal-complete" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start">{{__('maintenance_costs.title_modal_add_repair')}}</h5>
            </div>
            <div class="modal-body pb-1">
                <div class="block {{ __('block.styles') }} border-0">
                    @include('admin.components.block-header', [
                        'text' => __('maintenance_costs.title_modal_repair'),
                        'block_icon_class' => 'icon-document',
                    ])
                    <div class="block-content">
                        <div class="justify-content-between">
                            <div class="row mb-4">
                                <div class="col-sm-4">
                                    {{--                                    <x-forms.input-new-line id="modal_repair_list_name"--}}
                                    {{--                                                            :value="null"--}}
                                    {{--                                                            :label="__('maintenance_costs.title_repair_list')"/>--}}
                                    <x-forms.select-option id="modal_repair_list_name" :value="null"
                                                           :list="[]"
                                                           :optionals="[
                                                               'placeholder' => __('lang.search_placeholder'),
                                                               'ajax' => true,
                                                               'default_option_label' => null,
                                                           ]"
                                                           :label="__('maintenance_costs.title_repair_list')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="modal_price_total"
                                                            :value="null"
                                                            :optionals="['input_class' => 'number-format']"
                                                            :label="__('maintenance_costs.price_total')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="modal_amount"
                                                            :value="null"
                                                            :optionals="['input_class' => 'number-format']"
                                                            :label="__('maintenance_costs.amount')"/>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="modal_discount"
                                                            :value="null"
                                                            :optionals="['input_class' => 'number-format']"
                                                            :label="__('maintenance_costs.discount')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="modal_add_debt"
                                                            :value="null"
                                                            :optionals="['input_class' => 'number-format']"
                                                            :label="__('maintenance_costs.add_debt')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="modal_reduce_debt"
                                                            :value="null"
                                                            :optionals="['input_class' => 'number-format']"
                                                            :label="__('maintenance_costs.reduce_debt')"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-custom-size me-2"
                                onclick="toggleModalRepairData()">{{ __('lang.back') }}</button>
                        <button type="button"
                                class="btn btn-primary btn-custom-size"
                                onclick="addRepairData()">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
