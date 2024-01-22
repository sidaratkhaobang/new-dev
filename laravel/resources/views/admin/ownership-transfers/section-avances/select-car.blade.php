<x-blocks.block-search>
            <form action="" method="GET" id="form-search">
                <div class="row mb-4">
                    <div class="col-sm-4">
                        <x-forms.select-option id="status_avance" :value="null" :list="[]" :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => null,
                        ]"
                            :label="__('registers.status')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <div class="col-sm-ภ">
                            <x-forms.date-input id="month_last_payment_avance" :value="null" :label="__('ownership_transfers.month_last_payment')" />
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="leasing_avance" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.leasing')" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-4">
                        <x-forms.select-option id="car_id_avance" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.leasing')" :label="__('registers.engine_chassis_no')" />
                    </div>
               
                    <div class="col-sm-8">
                        <x-forms.select-option id="car_class_avance" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.car_class')" />
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="button" onclick="clearSelectCar()"
                            class="btn btn-outline-secondary btn-custom-size me-1"><i class="fa fa-rotate-left"></i>
                            {{ __('lang.clear_search') }}</button>
                        <button type="button" onclick="checkCar()" class="btn btn-primary btn-custom-size"><i
                                class="icon-add-circle"></i> {{ __('registers.add_car') }}</button>
                    </div>
                </div>
            </form>
        </x-blocks.block-search>
