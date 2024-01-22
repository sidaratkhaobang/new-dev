<div class="modal fade" id="modal-car-accessory" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static"
    aria-labelledby="modal-car-accessory" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.car_table') }}</h4>
                <hr>
                <div class="row push mb-3">
                    <div class="col-sm-12">
                        <x-forms.select-option id="car_class_field" :value="null" :list="null"
                            :label="__('long_term_rentals.car_class')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-8">
                        <x-forms.select-option id="car_color_field" :value="null" :list="null"
                            :label="__('long_term_rentals.car_color')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount_car_field" :value="null" :label="__('long_term_rentals.car_amount')"
                            :optionals="['required' => true, 'oninput' => true, 'type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="car_remark_field" :value="null" :label="__('long_term_rentals.remark')" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-12">
                        <x-forms.radio-inline id="have_accessory_field" :value="null" :list="[
                            ['name' => __('lang.have'), 'value' => 1],
                            ['name' => __('lang.no_have'), 'value' => 0],
                        ]"
                            :label="__('long_term_rentals.optional_accessory')" />
                        <input type="hidden" id="remark_tor" :value="null">
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.car_accessory') }}</h4>
                <hr>
                @if (!isset($view))
                    {{-- <div class="row push">
                        <div class="col-sm-8">
                            <x-forms.select-option id="accessory_field" :value="null" :list="null"
                                :label="__('long_term_rentals.car_accessory')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="amount_accessory_field" :value="null" :label="__('long_term_rentals.car_amount')"
                                :optionals="['oninput' => true, 'type' => 'number']" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-8">
                            <x-forms.input-new-line id="tor_section_field" :value="null" :label="__('long_term_rentals.tor_section')" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="remark_bom_field" :value="null" :label="__('long_term_rentals.remark')" />
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" id="add"
                                onclick="addAccessory()">{{ __('lang.add') }}</button>
                        </div>
                    </div> --}}

                    <div class="row push">
                        <div class="col-sm-8">
                            <x-forms.select-option id="accessory_field" :value="null" :list="null"
                                :label="__('long_term_rentals.car_accessory')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="amount_per_car_accessory_field" :value="null"
                                :label="__('long_term_rentals.amount_per_car')" :optionals="['oninput' => true, 'type' => 'number']" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-4">
    
                            <x-forms.input-new-line id="amount_accessory_field" :value="null" :label="__('long_term_rentals.total_amount')"
                                :optionals="['oninput' => true, 'type' => 'number']" />
                        </div>
                    </div>
                    <div class="row push mb-3">
                        <div class="col-sm-12">
                            <x-forms.text-area-new-line id="remark_bom_field" :value="null" :label="__('long_term_rentals.remark')" />
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" id="add_accessory"
                                onclick="addAccessory()">{{ __('lang.add') }}</button>
                        </div>
                    </div>
                @endif
                <div id="accessory" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('long_term_rentals.car_accessory') }}</th>
                                <th>{{ __('long_term_rentals.amount_per_car') }}</th>
                                <th>{{ __('long_term_rentals.total_amount') }}</th>
                                {{-- <th>{{ __('long_term_rentals.tor_section') }}</th> --}}
                                <th>{{ __('long_term_rentals.remark') }}</th>
                                @if (!isset($view))
                                    <th class="sticky-col text-center remove_accessory_th">{{ __('lang.tools') }}
                                    </th>
                                @endif
                            </thead>
                            <tbody v-if="car_accessories.length > 0">
                                <tr v-for="(item, index) in car_accessories">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.accessory_text }}</td>
                                    <td>@{{ item.amount_per_car_accessory }}</td>
                                    <td>@{{ item.amount_accessory }}</td>
                                    {{-- <td>@{{ item.tor_section }}</td> --}}
                                    <td>@{{ item.remark }}</td>
                                    @if (!isset($view))
                                        <td class="sticky-col text-center remove_accessory">
                                            <div class="btn-group">
                                                <div class="col-sm-12">
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeAccessory(index)"><i
                                                            class="fa fa-trash-alt me-1"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">"
                                        {{ __('lang.no_list') . __('long_term_rentals.accessories_table') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                @if (!isset($view))
                    <button type="button" class="btn btn-primary" id="save"
                        onclick="saveCarAccessory()">{{ __('lang.save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
