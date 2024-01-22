<div class="col-sm-12 mb-2">
    @if(!Route::is('*.show'))
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="add()"><i class="fa fa-plus-circle"></i>&nbsp; {{ __('เพิ่มหัวข้อ') }}</button>
            </div>
        </div>
    @endif
</div>
<div class="mb-3" id="app" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 5%"></th>
            <th style="width: 5%">{{ __('ลำดับหัวข้อ') }}</th>
            <th style="width: 85%">{{ __('หัวข้อสัญญา') }}</th>
            <th class="sticky-col "></th>
            </thead>
            <tbody v-for="(input,k) in inputs">
            <tr>
                <td>
                    <div class="form-check d-inline-block">
                        <input class="form-check-input form-check-input-each" type="checkbox" v-model="inputs[k].status">
                        <input type="hidden" v-bind:name="'contract_quotation['+ k+ '][status]'" id="status" v-model="inputs[k].status">
                        <label class="form-check-label"></label>
                        <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)" :id="'arrow-'+k"></i>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control" v-model="inputs[k].seq" v-bind:name="'contract_quotation['+ k+ '][seq]'" min="0">
                </td>
                <td>
                    <textarea class="form-control pre-wrap" rows="1" v-model="inputs[k].name" v-bind:name="'contract_quotation['+ k+ '][name]'" required >
                    </textarea>
                    {{-- <input type="text" class="form-control" v-model="inputs[k].name" v-bind:name="'contract_quotation['+ k+ '][name]'"> --}}
                    <input type="hidden" v-bind:name="'contract_quotation['+ k+ '][id]'" id="id" v-model="inputs[k].id">
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
                            <th style="width: 5%">{{ __('ลำดับหัวข้อ') }}</th>
                            <th style="width: 75%">{{ __('หัวข้อย่อย') }}</th>
                            <th class="sticky-col text-center"></th>
                            </thead>
                            <tbody v-if="inputs[k].contract_form_check_lists.length > 0">
                            <tr v-for="(input2,k2) in inputs[k].contract_form_check_lists">
                                <td>
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox" v-model="input2.status">
                                        <input type="hidden" v-bind:name="'contract_quotation['+ k+ '][contract_form_check_lists]['+ k2+ '][status]'" id="status" v-model="input2.status">
                                        <label class="form-check-label"></label>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="input2.seq" min="0" v-bind:name="'contract_quotation['+ k+ '][contract_form_check_lists]['+ k2+ '][seq]'" required>
                                </td>
                                <td>
                                    <textarea class="form-control pre-wrap" rows="1" v-model="input2.name" v-bind:name="'contract_quotation['+ k+ '][contract_form_check_lists]['+ k2+ '][name]'" id="name" required >
                                    </textarea>
                                    {{-- <input type="text" class="form-control" v-model="input2.name" v-bind:name="'contract_quotation['+ k+ '][contract_form_check_lists]['+ k2+ '][name]'" id="name" required> --}}
                                    <input type="hidden" v-bind:name="'contract_quotation['+ k+ '][contract_form_check_lists]['+ k2+ '][id]'" id="id" v-model="input2.id">
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
@push('scripts')
    <script>
        function add() {
            carVue.add();
        }

        function addSub() {
            carVue.addSub();
        }

        function remove() {
            carVue.remove();
        }

        function removeList(k) {
            carVue.removeList(k);
        }


        let carVue = new Vue({
            el: '#app' ,

            data() {
                return {
                    inputs: @if (count($data->contract_forms) > 0) @json($data->contract_forms) @else [{
                        status: true ,
                        seq: null ,
                        name: null ,
                        id: null ,
                        contract_form_check_lists: []
                    }] @endif,
                    del_input_id: [] ,
                    del_input_sub_id: [] ,
                }
            } ,
            methods: {
                add() {
                    this.inputs.push({
                        status: true ,
                        seq: null ,
                        name: null ,
                        contract_form_check_lists: [] ,
                        id: null ,
                    })
                } ,
                addSub(k) {
                    this.inputs[k].contract_form_check_lists.push({
                        status: true ,
                        seq: null ,
                        name: null ,
                        id: null ,
                    })
                } ,
                hide(k) {
                    if ($("#sub-section" + k).is(":hidden")) {
                        $("hd").removeClass('hidden');
                        $('#arrow-' + k).removeClass('fa-angle-right');
                        $('#arrow-' + k).addClass('fa-angle-down');
                        $("#sub-section" + k).show();
                    }
                    else {
                        $("#sub-section" + k).hide()
                        $("hd").addClass('hidden')
                        $('#arrow-' + k).removeClass('fa-angle-down');
                        $('#arrow-' + k).addClass('fa-angle-right');
                        $("#sub-section" + k).hide()
                    }
                } ,

                remove(k) {
                    if (this.inputs[k].id != null) {
                        this.del_input_id.push(this.inputs[k].id);
                        console.log(this.del_input_id);
                    }
                    this.inputs.splice(k , 1)
                } ,
                removeList(k , k2) {
                    if (this.inputs[k].contract_form_check_lists[k2].id != null) {
                        this.del_input_sub_id.push(this.inputs[k].contract_form_check_lists[k2].id);
                    }
                    this.inputs[k].contract_form_check_lists.splice(k2 , 1)
                } ,
            }
        })
    </script>
@endpush

