<div class="modal fade" id="modal-start-cmi" aria-labelledby="modal-start-cmi" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="icon-document me-2"></i> {{ __('cmi_cars.make_cmi') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-3">
                    <div class="col-sm-4">
                        <x-forms.date-input id="_term_start_date" :value="null" :label="__('cmi_cars.policy_start_date')" 
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.date-input id="_term_end_date" :value="null" :label="__('cmi_cars.policy_end_date')" 
                        :optionals="['required' => true]"  />
                    </div>
                </div>
                <div class="mb-4">
                    @include('admin.components.block-header',[
                        'block_header_class' => 'ps-0',
                        'text' => __('lang.total_list')
                    ])
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>{{ __('cmi_cars.worksheet_no') }}</th>
                                <th>{{ __('cmi_cars.year_act') }}</th>
                                <th>{{ __('cmi_cars.license_plate_chassis') }}</th>
                                <th>{{ __('cmi_cars.insurance_company') }}</th>
                                <th class="sticky-col"></th>
                            </thead>
                            <tbody v-if="selected_list.length > 0">
                                <tr v-for="(item, index) in selected_list">
                                    <td>@{{ item.worksheet_no }}</td>
                                    <td>@{{ item.year }}</td>
                                    <td>
                                        @{{ (item.car && item.car.license_plate) ? item.car.license_plate : '-' }} /
                                        @{{ (item.car && item.car.chassis_no) ? item.car.chassis_no : '-' }} 
                                    </td>
                                    <td>@{{ (item.insurer && item.insurer.insurance_name_th) ? item.insurer.insurance_name_th : '-' }}</td>
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
                  
          </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary"
                    :disabled="selected_list.length <= 0" id="saveDetail"
                    @click.prevent="startCMI">
                    <i class="icon-save me-1"></i> {{ __('lang.save') }}
                </button>
            </div>
        </div>
    </div>
</div>


