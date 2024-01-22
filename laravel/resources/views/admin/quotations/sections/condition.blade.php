<br>
<div class="col-sm-12  mb-2">
    @if (empty($view))
        <div class="row">
            <div class="col-md-9 text-left">
                @if (isset($lt_rental_lines))
                <h4>{{ __('quotations.condition_table') }}</h4>
                @else
                <h4>{{ __('quotations.condition_rental') }}</h4>
                @endif
            </div>
            <div class="col-md-3 text-end">
                @if (isset($lt_rental_id))
                <button type="button" class="btn btn-primary" onclick="addCondition('{{$lt_rental_id}}')">{{ __('quotations.condition') }}</button>
                @endif
                <button type="button" class="btn btn-primary" onclick="addConditionQuotation()"><i
                        class="fa fa-arrow-up-from-bracket"></i>&nbsp; {{ __('quotations.condition_add') }}</button>

            </div>
        </div>
    @endif
</div>
<div class="mb-3" id="condition-quotation" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 5%"></th>
                <th style="width: 5%">ลำดับหัวข้อ</th>
                <th style="width: 85%">หัวข้อเงื่อนไข</th>
                <th class="sticky-col "></th>
            </thead>
            <template v-if="quotation_forms.length >0">
            <tbody v-for="(item,k) in quotation_forms">
                <tr>
                    <td>
                        <div class="form-check d-inline-block">
                            <input class="form-check-input form-check-input-each" type="checkbox"
                                v-model="quotation_forms[k].quotation_form_status">
                            <input type="hidden" v-bind:name="'data['+ k+ '][quotation_form_status]'"
                                id="quotation_form_status" v-model="quotation_forms[k].quotation_form_status">
                            <label class="form-check-label"></label>
                            <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)"
                                :id="'arrow-' + k"></i>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control" v-model="quotation_forms[k].seq" min="0">
                        <input type="hidden" v-bind:name="'data['+ k+ '][seq]'" id="seq"
                            v-model="quotation_forms[k].seq">
                    </td>
                    <td>
                        <input type="text" class="form-control" v-model="quotation_forms[k].name">
                        <input type="hidden" v-bind:name="'data['+ k+ '][name]'" id="name"
                            v-model="quotation_forms[k].name">
                        <input type="hidden" v-bind:name="'data['+ k+ '][id]'" id="id"
                            v-model="quotation_forms[k].id">
                    </td>
                    <td>
                        @if (empty($view))
                            <a class="btn btn-light" v-on:click="removeConditionQuotation(k)"
                                v-show="k || ( !k && quotation_forms.length > 1)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        @endif
                    </td>

                </tr>
                <tr :id="'sub-quotation-form-checklist' + k" class="hidden hd">
                    <td></td>
                    <td colspan="3">
                        <div class="table-wrap">
                            <table class="table table-striped" :id="'sub-table-' + k">
                                <thead class="bg-body-dark">
                                    <th style="width: 5%"></th>
                                    <th style="width: 5%">ลำดับรายการ</th>
                                    <th style="width: 75%">รายการ</th>
                                    <th class="sticky-col text-center"></th>
                                </thead>
                                <tbody v-if="quotation_forms[k].sub_quotation_form_checklist.length > 0">
                                    <tr v-for="(item_checklist,k2) in quotation_forms[k].sub_quotation_form_checklist">
                                        <td>
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input form-check-input-each" type="checkbox"
                                                    v-model="item_checklist.quotation_form_checklist_status">
                                                <input type="hidden"
                                                    v-bind:name="'data['+ k+ '][sub_quotation_form_checklist]['+ k2+ '][quotation_form_checklist_status]'"
                                                    id="quotation_form_checklist_status" v-model="item_checklist.quotation_form_checklist_status">
                                                <label class="form-check-label"></label>

                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" v-model="item_checklist.quotation_form_checklist_seq"
                                                min="0" required>
                                            <input type="hidden"
                                                v-bind:name="'data['+ k+ '][sub_quotation_form_checklist]['+ k2+ '][quotation_form_checklist_seq]'"
                                                id="quotation_form_checklist_seq" v-model="item_checklist.quotation_form_checklist_seq">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="item_checklist.quotation_form_checklist_name" required>
                                            <input type="hidden"
                                                v-bind:name="'data['+ k+ '][sub_quotation_form_checklist]['+ k2+ '][quotation_form_checklist_name]'"
                                                id="quotation_form_checklist_name" v-model="item_checklist.quotation_form_checklist_name">
                                            <input type="hidden"
                                                v-bind:name="'data['+ k+ '][sub_quotation_form_checklist]['+ k2+ '][id]'"
                                                id="id" v-model="item_checklist.id">
                                        </td>
                                        <td>
                                            @if (empty($view))
                                                <a class="btn btn-light" v-on:click="removeConditionQuotationChecklist(k,k2)"><i
                                                        class="fa-solid fa-trash-can" style="color:red"></i></a>
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
                            @if (empty($view))
                                <div class="col-md-12 text-end">
                                    <button type="button" class="btn btn-primary"
                                        v-on:click="addConditionQuotationChecklist(k)">{{ __('lang.add') }}</button>
                                </div>
                            @endif
                        </div>
                </tr>
            </tbody>
            
            <template v-for="(item,k) in del_input_id">
                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id" v-bind:value="item">
            </template>

            <template v-for="(item_checklist,k) in del_input_sub_id">
                <input type="hidden" v-bind:name="'del_checklist[]'" id="del_input_sub_id" v-bind:value="item_checklist">
            </template>
        </template>
        <template v-else>
            <tbody>
            <tr>
                <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "
                </td>
            </tr>
            </tbody>
        </template>
        </table>

    </div>
</div>
<style>
    .hidden {
        display: none;
    }
</style>
