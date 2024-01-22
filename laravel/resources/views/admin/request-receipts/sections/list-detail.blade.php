<div id="request-receipt-vue" data-detail-uri="" data-title="">
    @include('admin.request-receipts.modals.list-modal')
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th class="text-center">#</th>
                <th class="text-center">{{ __('request_receipts.list_name') }}</th>
                <th class="text-center">{{ __('request_receipts.amount') }} </th>
                <th class="text-center">{{ __('request_receipts.fee_deducted') }} </th>
                <th class="text-center">{{ __('request_receipts.total') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center"></th>
                @endif
            </thead>
            <tbody v-if="request_receipt_list.length > 0">
                <tr v-for="(item, index) in request_receipt_list">
                    <td class="text-center">@{{ index + 1 }}</td>
                    <td class="text-center">@{{ item.list_name }}</td>
                    <td class="text-center">@{{ getNumberWithCommas(item.amount) }}</td>
                    <td class="text-center">@{{ getNumberWithCommas(item.fee_deducted) }}</td>
                    <td class="text-center">@{{ getNumberWithCommas(item.total) }}</td>
                    @if (!isset($view))
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" v-on:click="editList(index)"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                            <a class="dropdown-item btn-delete-row" v-on:click="removeList(index)"><i
                                                    class="fa fa-trash-alt me-1"></i> ลบ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <input type="hidden" v-bind:name="'request_receipt_data['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'request_receipt_data['+ index+ '][list_name]'"
                        v-bind:value="item.list_name">
                    <input type="hidden" v-bind:name="'request_receipt_data['+ index+ '][amount]'"
                        v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'request_receipt_data['+ index+ '][fee_deducted]'"
                        v-bind:value="item.fee_deducted">
                    <input type="hidden" v-bind:name="'request_receipt_data['+ index+ '][total]'"
                        v-bind:value="item.total">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">"
                        {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
        </div>
    </div>

</div>
<br>
