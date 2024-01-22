<div class="col-sm-12  mb-2">
    <div class="row">
        <div class="col-md-9 text-left">
            <span>{{ __('users.total_items') }}</span>
        </div>
        @if (empty($view))
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-primary" onclick="add()"><i class="fa fa-plus-circle"></i>
                    {{ __('lang.add_data') }}</button>
            </div>
        @endif
    </div>
</div>
<div class="mb-3" id="condition-repair" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 5%"></th>
                <th style="width: 10%">{{ __('condition_quotations.condition_seq') }}</th>
                <th>{{ __('repair_orders.condition_main') }}</th>
                <th class="sticky-col"></th>
            </thead>
            <template v-if="condition_repair.length > 0">
                <tbody v-for="(input,k) in condition_repair">
                    <tr>
                        <td>
                            <div class="form-check d-inline-block">
                                <label class="form-check-label"></label>
                                <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)"
                                    :id="'arrow-' + k"></i>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control" min="0" v-model="condition_repair[k].seq">
                            <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][seq]'" id="seq"
                                v-model="condition_repair[k].seq">
                        </td>
                        <td>
                            <input type="text" class="form-control" v-model="condition_repair[k].name">
                            <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][name]'" id="name"
                                v-model="condition_repair[k].name">
                        </td>
                        <td>
                            @if (empty($view))
                                <a class="btn btn-light" v-on:click="remove(k)"
                                    v-show="k || ( !k && condition_repair.length > 1)"><i class="fa-solid fa-trash-can"
                                        style="color:red"></i></a>
                            @endif
                        </td>
                        <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][id]'" id="id"
                            v-model="condition_repair[k].id">
                    </tr>
                    <tr :id="'sub-service' + k" class="hidden hd">
                        <td></td>
                        <td colspan="3">
                            <div class="row mb-3">
                                <div class="col-md-9 text-left">
                                    <span>{{ __('condition_quotations.checklist_table') }}</span>
                                </div>
                                @if (empty($view))
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-primary" v-on:click="addSub(k)"><i
                                                class="fa fa-plus-circle"></i> {{ __('lang.add_data') }}</button>
                                    </div>
                                @endif
                            </div>
                            <div class="table-wrap">
                                <table class="table table-striped" :id="'sub-table-' + k">
                                    <thead class="bg-body-dark">
                                        <th style="width: 10%">{{ __('condition_quotations.checklist_seq') }}</th>
                                        <th>{{ __('repair_orders.condition_sub') }}</th>
                                        <th class="sticky-col text-center"></th>
                                    </thead>
                                    <tbody v-if="condition_repair[k].sub_condition_repair.length > 0">
                                        <tr v-for="(input2,k2) in condition_repair[k].sub_condition_repair">
                                            <td>
                                                <input type="number" class="form-control" min="0"
                                                    v-model="input2.seq">
                                                <input type="hidden"
                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_repair]['+ k2+ '][seq]'"
                                                    id="seq" v-model="input2.seq">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" v-model="input2.name">
                                                <input type="hidden"
                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_repair]['+ k2+ '][name]'"
                                                    id="name" v-model="input2.name">
                                                <input type="hidden"
                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_repair]['+ k2+ '][id]'"
                                                    id="id" v-model="input2.id">
                                            </td>
                                            <td>
                                                @if (empty($view))
                                                    <a class="btn btn-light" v-on:click="removeList(k,k2)"
                                                        v-show="condition_repair[k].sub_condition_repair.length > 0">
                                                        <i class="fa-solid fa-trash-can" style="color:red"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="table-empty" id='empty-data'>
                                            <td class="text-center" colspan="4">"
                                                {{ __('lang.no_list') }} "</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </template>
            <template v-else>
                <tbody>
                    <tr class="table-empty" id='empty-data'>
                        <td class="text-center" colspan="5">" {{ __('lang.no_list') }} "</td>
                    </tr>
                </tbody>
            </template>
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
    .hidden {
        display: none;
    }
</style>
