<div class="row push">
    <div class="col-sm-4 mb-3">
        <x-forms.radio-inline id="status_in" :value="$d->is_need_customer_sign_in" :list="$listStatus" :label="__('car_inspection_types.customer_signature_in')" :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
    </div>
</div>
<div id="app3" v-cloak>
    <div class="table-wrap mb-3">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 5%">{{ __('car_inspection_types.seq_inspection') }}</th>
                <th style="width: 13%" class="th-min-width-sm">{{ __('car_inspection_types.condition') }}</th>
                <th style="width: 20%" class="th-min-width">{{ __('car_inspection_types.use_form') }}</th>
                <th style="width: 20%" class="th-min-width">{{ __('car_inspection_types.responsible_department') }}</th>
                <th style="width: 20%" class="th-min-width">{{ __('car_inspection_types.responsible_section') }}</th>
                {{-- <th style="width: 20%" class="th-min-width">{{ __('car_inspection_types.role') }}</th> --}}
                <th style="width: 7%">{{ __('car_inspection_types.photo') }}</th>
                <th style="width: 7%">{{ __('car_inspection_types.inspector_signature') }}</th>
                <th style="width: 7%">{{ __('car_inspection_types.send_mobile') }}</th>
                <th style="width: 7%">{{ __('car_inspection_types.dpf_oil') }}</th>
                <th style="width: 5%"></th>
            </thead>
            <tbody v-if="inputs2.length > 0">
                <tr v-for="(input,k) in inputs2">

                    <td>
                        <input type="number" class="form-control" v-model="inputs2[k].seq" min="0">
                        <input type="hidden" v-bind:name="'data3['+ k+ '][seq]'" id="seq"
                            v-model="inputs2[k].seq">
                    </td>
                    <td>

                        <select-2-default :id="'condition_in' + k" class="form-control list_in" style="width: 100%;"
                            v-model="input.condition" :list="listCondition" :defaultname="input.condition_in" />
                        </select-2-default>
                        <input type="hidden" v-bind:name="'data3['+ k+ '][condition]'" id="condition"
                            v-model="inputs2[k].condition">
                    </td>

                    <td>
                        <select-2-default :id="'in_form_in' + k" class="form-control list_in" style="width: 100%;"
                            v-model="input.in_form" :list="listForm" :defaultname="input.in_form">
                        </select-2-default>
                        <input type="hidden" v-bind:name="'data3['+ k+ '][in_form]'" id="in_form"
                            v-model="inputs2[k].in_form">


                    </td>
                    <td>
                        <select-2-ajax :id="'department_in' + k" class="form-control list_in" style="width: 100%;"
                            v-model="input.department" :defaultname="input.department_name"
                            url="{{ route('admin.util.select2.departments') }}">
                        </select-2-ajax>
                        <input type="hidden" v-bind:name="'data3['+ k+ '][department]'" id="department"
                            v-model="inputs2[k].department">
                    </td>
                    <td>
                        <select-2-ajax :id="'section_in' + k" class="form-control list_in" style="width: 100%;"
                        v-model="input.section" :defaultname="input.section_name"
                        :parentid="input.department" url="{{ route('admin.util.select2.sections') }}">
                    </select-2-ajax>
                        <input type="hidden" v-bind:name="'data3['+ k+ '][section]'" id="section"
                            v-model="inputs2[k].section">
                    </td>
                    {{-- <td>
                        <select-role-list-in :id="'role_in' + k" class="form-control list_in" style="width: 100%;"
                            v-model="inputs2[k].role">
                        </select-role-list-in>
                        <input type="hidden" v-bind:name="'data3['+ k+ '][role]'" id="role"
                            v-model="inputs2[k].role">
                    </td> --}}
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox"
                                v-model="inputs2[k].photo">
                            <input type="hidden" v-bind:name="'data3['+ k+ '][photo]'" id="photo"
                                v-model="inputs2[k].photo">
                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox"
                                v-model="inputs2[k].inspector_signature">
                            <input type="hidden" v-bind:name="'data3['+ k+ '][inspector_signature]'"
                                id="inspector_signature" v-model="inputs2[k].inspector_signature">
                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox"
                                v-model="inputs2[k].send_mobile">
                            <input type="hidden" v-bind:name="'data3['+ k+ '][send_mobile]'" id="send_mobile"
                                v-model="inputs2[k].send_mobile">
                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox"
                                v-model="inputs2[k].dpf_oil">
                            <input type="hidden" v-bind:name="'data3['+ k+ '][dpf_oil]'" id="dpf_oil"
                                v-model="inputs2[k].dpf_oil">
                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                        </div>
                    </td>

                    <td>
                        @if (empty($view))
                            <a class="btn btn-danger btn-auto-width" v-on:click="remove2(k)"><i
                                    class="fa-solid fa-trash-can"></i></a>
                        @endif
                    </td>
                    <input type="hidden" v-bind:name="'data3['+ k+ '][flow_type]'" id="flow_type"
                        v-model="flow_type">
                    <input type="hidden" v-bind:name="'data3['+ k+ '][transfer_type]'" id="transfer_type"
                        v-model="inputs2[k].transfer_type">
                    <input type="hidden" v-bind:name="'data3['+ k+ '][id]'" id="id" v-model="inputs2[k].id">
                </tr>
            </tbody>

            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
            <template v-for="(input,k) in del_input_id">
                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id" v-bind:value="input">
            </template>
        </table>
    </div>
    @if (empty($view))
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="add3()">{{ __('lang.add') }}</button>
        </div>
    @endif
</div>
