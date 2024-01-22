@if(empty($view))
<div class="row push">
    <div class="col-md-9 text-left">
        <span>การตอบคำถามจะตอบในรูปแบบของการเลือกตอบข้อใดข้อหนึ่ง และพิมพ์ข้อความในกล่องหมายเหตุ</span>
    </div>
    <div class="col-md-3 text-end">
        <button type="button" class="btn btn-primary"
            onclick="add()"><i class="fa fa-arrow-up-from-bracket"></i>&nbsp; {{ __('car_inspections.add_topic') }}</button>

    </div>
</div>
@endif
<div id="app" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap mb-0">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 5%"></th>
                <th style="width: 5%">{{ __('car_inspections.seq') }}</th>
                <th style="width: 85%">{{ __('car_inspections.section_topic') }}</th>
                <th class="sticky-col "></th>
            </thead>

            <tbody v-for="(input,k) in inputs">
                <tr>
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox" v-model="inputs[k].status_section">
                            <input type="hidden" v-bind:name="'data['+ k+ '][status_section]'" id="status_section" v-model="inputs[k].status_section">
                            <label class="form-check-label" ></label>
                            <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)" :id="'arrow-'+k"></i>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control" v-model="inputs[k].seq" min="0">
                        <input type="hidden" v-bind:name="'data['+ k+ '][seq]'" id="seq" v-model="inputs[k].seq">
                    </td>
                    <td>
                    <input type="text" class="form-control" v-model="inputs[k].name">
                    <input type="hidden" v-bind:name="'data['+ k+ '][name]'" id="name" v-model="inputs[k].name">
                    <input type="hidden" v-bind:name="'data['+ k+ '][id]'" id="id" v-model="inputs[k].id">
                    </td>
                    <td>
                        @if(empty($view))
                            <a class="btn btn-danger btn-auto-width" v-on:click="remove(k)" v-show="k || ( !k && inputs.length > 1)"><i class="fa-solid fa-trash-can" ></i></a>
                        @endif
                    </td>

                </tr>
                <tr :id="'sub-section'+k" class="hidden hd">
                    <td></td>
                    <td colspan="3">
                        <div class="table-wrap">

                            <table class="table table-striped" :id="'sub-table-'+k">
                                <thead class="bg-body-dark">
                                    <th style="width: 5%"></th>
                                    <th style="width: 5%">{{ __('car_inspections.seq_question') }}</th>
                                    <th style="width: 75%">{{ __('car_inspections.list_inspection') }}</th>
                                    <th style="width: 15%">{{ __('car_part_types.titile') }}</th>
                                    <th class="sticky-col text-center"></th>
                                </thead>
                                <tbody v-if="inputs[k].subseq.length > 0">
                                    <tr v-for="(input2,k2) in inputs[k].subseq">
                                    <td>
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input form-check-input-each" type="checkbox" v-model="input2.status_list">
                                                <input type="hidden" v-bind:name="'data['+ k+ '][subseq]['+ k2+ '][status_list]'" id="status_list" v-model="input2.status_list">
                                            <label class="form-check-label"></label>

                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model="input2.seq2" min="0" required>
                                        <input type="hidden" v-bind:name="'data['+ k+ '][subseq]['+ k2+ '][seq2]'" id="seq2" v-model="input2.seq2">
                                    </td>
                                    <td>
                                    <input type="text" class="form-control" v-model="input2.name2" required>
                                    <input type="hidden" v-bind:name="'data['+ k+ '][subseq]['+ k2+ '][name2]'" id="name2" v-model="input2.name2">
                                    <input type="hidden" v-bind:name="'data['+ k+ '][subseq]['+ k2+ '][id]'" id="id" v-model="input2.id">
                                    </td>
                                    {{-- TODOU implement select2 --}}
                                    <td>
                                        <select :name="'car_part_type_selection' + k + '_' + k2"
                                                :id="'car_part_type_selection' + k + '_' + k2"
                                                class="form-control car-part-type-selection" v-model="input2.car_part">
                                            <option value=""></option>
                                            <option v-for="car_part_type in car_part_type_list" v-bind:value="car_part_type.id">
                                                @{{ car_part_type.name }}
                                            </option>
                                        </select>
                                        <input type="hidden" v-bind:name="'data['+ k +'][subseq]['+ k2 +'][car_part]'" v-model="input2.car_part">
                                    </td>
                                    <td>
                                        @if(empty($view))
                                            <a class="btn btn-sm btn-danger btn-auto-width" v-on:click="removeList(k,k2)"><i class="fa-solid fa-trash-can" ></i></a>
                                        @endif
                                    </td>
                                    </tr>
                                </tbody>

                                <tbody v-else>
                                    <tr class="table-empty" id='empty-data'>
                                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if(empty($view))
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary"
                                        v-on:click="addSub(k)">{{ __('lang.add') }}</button>
                            </div>
                        @endif
                </tr>
            </tbody>
            <template v-for="(input,k) in del_input_id">
                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id" v-bind:value="input">
            </template>

            <template v-for="(input2,k) in del_input_sub_id">
                <input type="hidden" v-bind:name="'del_checklist[]'" id="del_input_sub_id" v-bind:value="input2">
            </template>

        </table>

    </div>
  </div>
    <style>
    .hidden{
        display: none;
    }
    </style>
