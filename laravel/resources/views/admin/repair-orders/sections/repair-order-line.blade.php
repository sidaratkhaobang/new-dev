<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div class="col-sm-6">
                <h4><i class="fa fa-file-lines me-1"></i>
                    {{ __('repair_orders.table_order_line') }}</h4>
            </div>
            <div class="col-sm-6 text-end">
                @if (!isset($view))
                    <button type="button" class="btn btn-primary" onclick="addData()">{{ __('lang.add_data') }}</button>
                @endif
            </div>
        </div>
        <div id="repair-order-line" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>{{ __('repair_orders.date') }}</th>
                        <th>{{ __('repair_orders.code_name') }}</th>
                        <th>{{ __('repair_orders.remark') }}</th>
                        <th>{{ __('repair_orders.check') }}</th>
                        <th>{{ __('repair_orders.price') }}</th>
                        <th>{{ __('repair_orders.amount') }}</th>
                        <th>{{ __('repair_orders.discount') }}</th>
                        <th>{{ __('repair_orders.total') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="repair_order_line_list.length > 0">
                        <template v-for="(item, index) in repair_order_line_list">
                            <tr>
                                <td>@{{ formatDate(item.date) }}</td>
                                <input type="hidden" id="date" v-bind:value="item.date">
                                <td>@{{ item.code_name }}</td>
                                <td>@{{ item.remark }}</td>
                                <td>@{{ item.check_text }}</td>
                                <td>@{{ numberWithCommas(item.price) }}</td>
                                <td>@{{ item.amount }}</td>
                                <td>@{{ item.discount }}</td>
                                <td>@{{ numberWithCommas(item.total) }}</td>
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
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark"
                                                        v-if="item.add_item">
                                                        <a class="dropdown-item" v-on:click="editData(index)"><i
                                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                                        <a class="dropdown-item btn-delete-row"
                                                            v-on:click="removeData(index)"><i
                                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                    </div>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark"
                                                        v-else>
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
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][id]'"
                                v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][date]'" id="date"
                                v-bind:value="item.date">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][repair_list_id]'"
                                id="repair_list_id" v-bind:value="item.repair_list_id">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][code_name]'"
                                id="code_name" v-bind:value="item.code_name">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][remark]'" id="remark"
                                v-bind:value="item.remark">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][check]'" id="check"
                                v-bind:value="item.check">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][price]'" id="price"
                                v-bind:value="item.price">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][amount]'" id="amount"
                                v-bind:value="item.amount">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][discount]'"
                                id="discount" v-bind:value="item.discount">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][vat]'"
                                id="vat" v-bind:value="item.vat">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][total]'" id="total"
                                v-bind:value="item.total">
                            <input type="hidden" v-bind:name="'repair_order_line['+ index +'][repair_type]'"
                                id="repair_type" v-bind:value="item.repair_type">
                        </template>
                        <tr class="bg-body-dark">
                            <th colspan="5" class="text-end">รวม</th>
                            <template>
                                <th id="total_amount" v-bind:value="total_amount">
                                    @{{ total_amount }}
                                </th>
                                <th id="total_discount" v-bind:value="total_discount">
                                    @{{ numberWithCommas(total_discount) }}
                                </th>
                                <th colspan="2" id="total_sum" v-bind:value="total_sum">
                                    @{{ numberWithCommas(total_sum) }}
                                </th>
                            </template>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="9">"
                                {{ __('lang.no_list') . __('repair_orders.table_order_line') }} "</td>
                        </tr>
                    </tbody>
                    <template v-for="(input,k) in pending_repair_order_line_ids">
                        <input type="hidden" v-bind:name="'del_repair_order_line[]'" id="del_input_id"
                            v-bind:value="input">
                    </template>
                </table>
            </div>
        </div>
    </div>
    @include('admin.repair-orders.modals.repair-order-line-modal')
</div>
