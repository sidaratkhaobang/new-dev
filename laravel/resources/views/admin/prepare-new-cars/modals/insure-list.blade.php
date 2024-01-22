<div class="modal fade" id="modal-insure" aria-labelledby="modal-edit-purchase" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="icon-document me-2"></i> {{ __('import_cars.set_insure') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="_lot_no" :value="null" :label="__('import_cars.lot_no')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="_insure_year" :value="null" :label="__('import_cars.insure_year')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="leasing_id" :value="null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => null,
                            ]"
                                               :label="__('import_cars.leasing')"/>
                    </div>
                </div>
            </div>
            <div id="modal-insure-display">
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                <th>{{ __('cars.chassis_no') }}</th>
                                <th>{{ __('cars.engine_no') }}</th>
                                <th>{{ __('import_cars.delivery_date') }}</th>
                                <th>{{ __('import_cars.registration_type') }}</th>
                                <th class="sticky-col"></th>
                                </thead>
                                <tbody v-if="selected_list.length > 0">
                                <tr v-for="(item, index) in selected_list">
                                    <td>@{{ item.chassis_no }}</td>
                                    <td>@{{ item.engine_no }}</td>
                                    <td>@{{ item.delivery_date }}</td>
                                    <td>@{{ item.registration_type }}</td>
                                    {{-- <td>
                                        <input type="text" class="form-control" v-model="item.registration_type">
                                    </td> --}}
                                    <td class="text-center">
                                        <a class="btn btn-light" v-on:click="remove(index)">
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td class="text-center" colspan="5">{{ __('lang.no_data') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    awd
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-clear-search"
                            data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                    <button type="button" class="btn btn-primary"
                            :disabled="selected_list.length <= 0" id="saveDetail"
                            @click.prevent="createNewInsureGroup">
                        <i class="icon-save me-1"></i> {{ __('lang.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



