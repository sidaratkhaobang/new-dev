<div class="col-sm-12  mb-2">
    <div class="row">
        <div class="col-md-9 text-left">
            <span>{{ __('check_distances.distance_table') }}</span>
        </div>
        @if (empty($view))
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-primary" onclick="add()"><i class="fa fa-plus-circle"></i>
                    {{ __('lang.add_data') }}</button>
            </div>
        @endif
    </div>
</div>
<div class="mb-3" id="check-distance" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 5%"></th>
                <th>{{ __('check_distances.distance') }}</th>
                <th>{{ __('check_distances.month') }}</th>
                <th>{{ __('check_distances.amount') }}</th>
                <th class="sticky-col "></th>
            </thead>
            <template v-if="check_distances.length > 0">
                <tbody v-for="(input,k) in check_distances">
                    <tr>
                        <td>
                            <div class="form-check d-inline-block">
                                <label class="form-check-label"></label>
                                <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)"
                                    :id="'arrow-' + k"></i>
                            </div>
                        </td>
                        <td>
                            <input type="number" class="form-control" v-model="check_distances[k].distance">
                            <input type="hidden" v-bind:name="'data_distances['+ k+ '][distance]'" id="distance"
                                v-model="check_distances[k].distance">
                        </td>
                        <td>
                            <input type="number" class="form-control" min="0"
                                v-model="check_distances[k].month">
                            <input type="hidden" v-bind:name="'data_distances['+ k+ '][month]'" id="month"
                                v-model="check_distances[k].month">
                        </td>
                        <td>
                            @{{ setAmount(k) }}
                            <input type="hidden" v-bind:name="'data_distances['+ k+ '][amount]'" id="amount"
                                v-bind:value="amount">
                        </td>
                        <td>
                            @if (empty($view))
                                <a class="btn btn-light" v-on:click="remove(k)" v-show="check_distances.length > 1"><i
                                        class="fa-solid fa-trash-can" style="color:red"></i></a>
                            @endif
                        </td>
                        <input type="hidden" v-bind:name="'data_distances['+ k+ '][id]'" id="id"
                            v-model="check_distances[k].id">
                    </tr>
                    <tr :id="'sub-section' + k" class="hidden hd">
                        <td></td>
                        <td colspan="4">
                            <div class="row">
                                <div class="col-md-9 text-left">
                                    <span>{{ __('check_distances.check_distance_table') }}</span>
                                </div>
                                @if (empty($view))
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-primary" v-on:click="addSub(k)"><i
                                                class="fa fa-plus-circle"></i> {{ __('lang.add_data') }}</button>
                                    </div>
                                @endif
                            </div>
                            <br>
                            <div class="table-wrap">
                                <table class="table table-striped" :id="'sub-table-' + k">
                                    <thead class="bg-body-dark">
                                        <th>{{ __('check_distances.code_name') }}</th>
                                        <th>{{ __('check_distances.is_check') }}</th>
                                        <th>{{ __('check_distances.price') }}</th>
                                        <th>{{ __('check_distances.remark') }}</th>
                                        <th class="sticky-col text-center"></th>
                                    </thead>
                                    <tbody v-if="check_distances[k].check_line.length > 0">
                                        <tr v-for="(input2,k2) in check_distances[k].check_line">
                                            <td>
                                                <select-code :id="'code_' + k + k2" class="form-control list"
                                                    name="code" style="width: 100%;" v-model="input2.code">
                                                </select-code>
                                                <input type="hidden"
                                                    v-bind:name="'data_distances['+ k+ '][check_line]['+ k2+ '][code]'"
                                                    id="code" v-model="input2.code">
                                            </td>
                                            <td>
                                                <select-check :id="'check_' + k + k2" class="form-control list"
                                                    name="check" style="width: 100%;" v-model="input2.check">
                                                </select-check>
                                                <input type="hidden"
                                                    v-bind:name="'data_distances['+ k+ '][check_line]['+ k2+ '][check]'"
                                                    id="check" v-model="input2.check">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" v-model="input2.price">
                                                <input type="hidden"
                                                    v-bind:name="'data_distances['+ k+ '][check_line]['+ k2+ '][price]'"
                                                    id="price" v-model="input2.price">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" v-model="input2.remark">
                                                <input type="hidden"
                                                    v-bind:name="'data_distances['+ k+ '][check_line]['+ k2+ '][remark]'"
                                                    id="remark" v-model="input2.remark">
                                                <input type="hidden"
                                                    v-bind:name="'data_distances['+ k+ '][check_line]['+ k2+ '][id]'"
                                                    id="id" v-model="input2.id">
                                            </td>
                                            <td>
                                                @if (empty($view))
                                                    <a class="btn btn-light" v-on:click="removeList(k,k2)"
                                                        v-show="check_distances[k].check_line.length > 0">
                                                        <i class="fa-solid fa-trash-can" style="color:red"></i></a>
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
