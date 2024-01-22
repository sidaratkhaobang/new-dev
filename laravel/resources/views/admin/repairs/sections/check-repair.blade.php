<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div class="col-sm-6">
                <h4><i class="fa fa-file-lines me-1"></i>
                    {{ __('repairs.description_repair_table') }}</h4>
            </div>
            <div class="col-sm-6 text-end">
                @if (!isset($view))
                    <button type="button" class="btn btn-primary" onclick="addData()">{{ __('lang.add_data') }}</button>
                @endif
            </div>
        </div>
        <div id="check-repair" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>{{ __('repairs.date') }}</th>
                        <th>{{ __('repairs.description') }}</th>
                        <th>{{ __('repairs.check') }}</th>
                        <th>{{ __('repairs.qc') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="check_repair_list.length > 0">
                        <template v-for="(item, index) in check_repair_list">
                            <tr>
                                <td>@{{ formatDate(item.date) }}</td>
                                <input type="hidden" id="date" v-bind:value="item.date">
                                <td>@{{ item.description }}</td>
                                <td>@{{ item.check_text }}</td>
                                <td>@{{ item.qc }}</td>
                                @if (!isset($view))
                                    <td class="sticky-col text-center">
                                        <div class="btn-group">
                                            <div class="col-sm-12">
                                                <div class="dropdown dropleft">
                                                    <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                        <a class="dropdown-item" v-on:click="editData(index)"><i
                                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                                        <a class="dropdown-item btn-delete-row"
                                                            v-on:click="removeData(index)"><i
                                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            <input type="hidden" v-bind:name="'check_repairs['+ index +'][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'check_repairs['+ index +'][date]'" id="date"
                                v-bind:value="item.date">
                            <input type="hidden" v-bind:name="'check_repairs['+ index +'][description]'"
                                id="description" v-bind:value="item.description">
                            <input type="hidden" v-bind:name="'check_repairs['+ index +'][check]'" id="check"
                                v-bind:value="item.check">
                            <input type="hidden" v-bind:name="'check_repairs['+ index +'][qc]'" id="qc"
                                v-bind:value="item.qc">
                        </template>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="5">"
                                {{ __('lang.no_list') . __('repairs.description_repair_table') }} "</td>
                        </tr>
                    </tbody>
                    <template v-for="(input,k) in pending_check_repair_ids">
                        <input type="hidden" v-bind:name="'del_check_repair[]'" id="del_input_id" v-bind:value="input">
                    </template>
                </table>
            </div>
        </div>
    </div>
    @include('admin.repairs.modals.check-repair-modal')
</div>
