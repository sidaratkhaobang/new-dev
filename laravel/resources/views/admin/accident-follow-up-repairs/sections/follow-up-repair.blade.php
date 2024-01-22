<div class="block {{ __('block.styles') }}">
    @section('block_options_2')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::AccidentFollowUpRepair)
                    @if (empty($view))
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" onclick="add2()" id="add-follow-up">{{ __('lang.add') }}</button>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    @endsection

    @include('admin.components.block-header', [
        'text' => __('accident_follow_up_repairs.follow_repair'),
        'block_option_id' => '_2',
    ])
    <div class="block-content">
        <div class="row mb-3 mt-2">
            <div class="mb-3" id="app2" v-cloak data-detail-uri="" data-title="">
                <div class="table-wrap">
                    <table class="table table-striped">
                        <thead class="bg-body-dark">
                            <th style="width: 25%">{{ __('accident_follow_up_repairs.repair_status') }}</th>
                            <th style="width: 25%">{{ __('accident_follow_up_repairs.recieve_date') }}</th>
                            <th style="width: 25%">{{ __('accident_follow_up_repairs.detail') }}</th>
                            <th style="width: 25%">{{ __('accident_follow_up_repairs.solution') }}</th>
                            {{-- <th style="width: 15%">{{ __('accident_orders.spare_part_total') }}</th> --}}
                            @if (!isset($view))
                                <th class="sticky-col "></th>
                            @endif
                        </thead>
                        <tbody v-if="inputs.length > 0">
                            @if (!isset($view))
                                <tr v-for="(input,k) in inputs">
                                    <td>
                                        <select-2-repair :id="'follow_up_status' + k" class="form-control list_in" v-bind:name="'follow_up['+ k+ '][follow_up_status]'"
                                            style="width: 100%;" v-model="inputs[k].follow_up_status">
                                        </select-2-repair>
                                        <input type="hidden" v-bind:name="'follow_up['+ k+ '][follow_up_status]'"
                                            id="follow_up_status" v-model="inputs[k].follow_up_status">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <flatpickr id="received_data_date" v-model="inputs[k].received_data_date"
                                                :id="'received_data_date' + k"
                                                v-bind:name="'inputs['+ k+ '][received_data_date]'"
                                                :options="{
                                                
                                                }">
                                            </flatpickr> <span class="input-group-text">
                                                <i class="far fa-calendar-check"></i>
                                            </span>
                                            <input type="hidden" v-bind:name="'follow_up['+ k+ '][received_data_date]'"
                                                id="received_data_date" v-model="inputs[k].received_data_date">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="inputs[k].problem" v-bind:name="'inputs['+ k+ '][problem]'">
                                        <input type="hidden" v-bind:name="'follow_up['+ k+ '][problem]'" id="problem"
                                            v-model="inputs[k].problem">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="inputs[k].solution" v-bind:name="'inputs['+ k+ '][solution]'">
                                        <input type="hidden" v-bind:name="'follow_up['+ k+ '][solution]'"
                                            id="solution" v-model="inputs[k].solution">
                                    </td>
                                    <td>
                                        <template v-if="input.is_saved != true">
                                            @if (empty($view))
                                                <a class="btn btn-light" v-on:click="remove(k)"><i
                                                        class="fa-solid fa-trash-can" style="color:red"></i></a>
                                            @endif
                                        </template>
                                    </td>
                                    <input type="hidden" v-bind:name="'follow_up['+ k+ '][id]'" id="id"
                                        v-model="inputs[k].id">
                                </tr>
                            @else
                                <tr v-for="(input,k) in inputs">
                                    <td>
                                        @{{ input.repair_status_text }}
                                    </td>
                                    <td>
                                        @{{ input.received_data_date }}
                                    </td>
                                    <td>
                                        @{{ input.problem }}
                                    </td>
                                    <td>
                                        @{{ input.solution }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tbody v-else>
                            <tr class="table-empty">
                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
