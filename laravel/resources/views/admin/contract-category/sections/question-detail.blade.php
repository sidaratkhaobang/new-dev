<div class="col-sm-12  mb-2">
    @if(!Route::is('*.show'))
        <div class="row mb-2">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="add()"><i class="fa fa-plus-circle"></i>&nbsp; {{ __('เพิ่มหัวข้อ') }}</button>
            </div>
        </div>
    @endif
</div>
<div class="mb-4" id="app" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 5%"></th>
            <th style="width: 5%">{{ __('contract_category.form.table.seq') }}</th>
            <th style="width: 85%">{{ __('contract_category.form.table.name') }}</th>
            <th class="sticky-col "></th>
            </thead>
            <tbody v-for="(input,k) in inputs">
            <tr>
                <td>
                    <div class="form-check d-inline-block">
                        <input class="form-check-input form-check-input-each" type="checkbox" v-model="inputs[k].status">
                        <input type="hidden" v-bind:name="'data['+ k+ '][status]'" id="status" v-model="inputs[k].status">
                        <label class="form-check-label"></label>
                        <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)" :id="'arrow-'+k"></i>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" v-model="inputs[k].seq" v-bind:name="'data['+ k+ '][seq]'" min="0">
                </td>
                <td>
                    <textarea class="form-control pre-wrap" rows="1" v-model="inputs[k].name" v-bind:name="'data['+ k+ '][name]'" >
                    </textarea>
                    {{-- <input type="text" class="form-control" v-model="inputs[k].name" v-bind:name="'data['+ k+ '][name]'" maxlength="255" > --}}
                    <input type="hidden" v-bind:name="'data['+ k+ '][id]'" id="id" v-model="inputs[k].id">
                </td>
                <td>
                    @if(!Route::is('*.show'))
                        <a class="btn btn-light" v-on:click="remove(k)" v-show="k || ( !k && inputs.length > 1)"><i class="fa-solid fa-trash-can" style="color:red"></i></a>
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
                            <th style="width: 5%">{{ __('contract_category.form.table.sub.seq') }}</th>
                            <th style="width: 75%">{{ __('contract_category.form.table.sub.name') }}</th>
                            <th class="sticky-col text-center"></th>
                            </thead>
                            <tbody v-if="inputs[k].condition_qoutation_checklists.length > 0">
                            <tr v-for="(input2,k2) in inputs[k].condition_qoutation_checklists">
                                <td>
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox" v-model="input2.status">
                                        <input type="hidden" v-bind:name="'data['+ k+ '][condition_qoutation_checklists]['+ k2+ '][status]'" id="status" v-model="input2.status">
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="input2.seq" min="0" v-bind:name="'data['+ k+ '][condition_qoutation_checklists]['+ k2+ '][seq]'" required>
                                </td>
                                <td>
                                    {{-- <input type="text" class="form-control" v-model="input2.name" v-bind:name="'data['+ k+ '][condition_qoutation_checklists]['+ k2+ '][name]'" id="name" maxlength="255" required> --}}
                                    <textarea class="form-control pre-wrap" rows="1" v-model="input2.name" v-bind:name="'data['+ k+ '][condition_qoutation_checklists]['+ k2+ '][name]'" id="name" required >
                                    </textarea>
                                    <input type="hidden" v-bind:name="'data['+ k+ '][condition_qoutation_checklists]['+ k2+ '][id]'" id="id" v-model="input2.id">
                                </td>
                                <td>
                                    @if(!Route::is('*.show'))
                                        <a class="btn btn-light" v-on:click="removeList(k,k2)"><i class="fa-solid fa-trash-can" style="color:red"></i></a>
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
                    @if(!Route::is('*.show'))
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" v-on:click="addSub(k)">{{ __('lang.add') }}</button>
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
    .hidden {
        display: none;
    }
</style>
