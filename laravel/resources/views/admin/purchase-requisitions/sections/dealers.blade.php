<h4>{{ __('purchase_orders.dealer_list') }}</h4>
<hr>
<div class="mb-4" id="purchase-order-dealers" v-cloak data-detail-uri="" data-title="">
    <div class="table-responsive db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark bg-body-border-line">
                <tr>
                    <th class="custom-align-center" rowspan="2">#</th>
                    <th class="custom-align-center" rowspan="2">{{ __('purchase_orders.model') }}</th>
                    <th class="custom-align-center" rowspan="2">{{ __('purchase_orders.color') }}</th>
                    <th class="custom-border-right custom-align-center" rowspan="2">{{ __('lang.total') }}</th>
                    <template v-for="(purchase_order_dealer, index) in purchase_order_dealer_list" >
                        <th class="custom-border-right text-center custom-align-center" colspan="2">
                            <div class="custom-droppown-action">
                                @{{ purchase_order_dealer.creditor_text }}
                                <div class="btn-group">
                                    @if(!isset($view))
                                    @if(!isset($cannot_add))
                                    <button type="button" class="btn btn-sm" href="javascript:void(0)" v-on:click="edit(index)"><i class="far fa-edit text-primary"></i></button>
                                    @endif
                                    @endif
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm dropdown-toggle" id="dropdown-dropleft-dark"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="far fa-file text-primary"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <template v-if="purchase_order_dealer.dealer_files && purchase_order_dealer.dealer_files.length > 0">
                                                <template v-if="getFilesPendingCount(purchase_order_dealer.dealer_files) > 0">
                                                    <a class="dropdown-item text-muted" disabled >
                                                        {{ __('customers.pending_file') }} : @{{ getFilesPendingCount(purchase_order_dealer.dealer_files) }}
                                                        {{ __('lang.file') }}
                                                    </a>
                                                </template>
                                                <template v-for="(dealer_file, file_index) in purchase_order_dealer.dealer_files" >
                                                    <a v-if="dealer_file.saved" target="_blank" class="dropdown-item" v-bind:href="dealer_file.url">
                                                        @{{ dealer_file.name }}
                                                    </a>
                                                </template>
                                            </template>
                                            <template v-else> 
                                                <a class="dropdown-item text-muted" disabled>
                                                    {{ __('lang.no_attach_file') }}
                                                </a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <input type="hidden" v-bind:name="'pr_dealer['+ index+ '][id]'" id="compare_id" v-bind:value="purchase_order_dealer.id">
                        <input type="hidden" v-bind:name="'pr_dealer['+ index+ '][creditor_id]'" id="creditor_id" v-bind:value="purchase_order_dealer.creditor_id">
                        <input type="hidden" v-bind:name="'pr_dealer['+ index+ '][remark]'" id="remark" v-bind:value="purchase_order_dealer.remark">
                    </template>
                </tr>
                <tr v-if="purchase_order_dealer_list.length > 0">
                    <template v-for="(d, i) in purchase_order_dealer_list" >
                        <th class="custom-border-right text-end">ราคา / คัน</th>
                        <th class="custom-border-right text-end">ราคา / ทั้งหมด</th>
                    </template>
                </tr>
            </thead>
            <tbody v-if="purchase_order_dealer_list.length > 0">
                <tr v-for="(item, index) in purchase_requisition_car_list">
                   <td>@{{ index + 1}}</td>
                   <td style="width: 20%;">
                        <p>@{{ item.model_full_name }}</p>
                        <p>@{{ item.model }}</p>
                    </td>
                   <td style="width: 20%;">@{{ item.color }}</td>
                   <td class="custom-border-right">@{{ item.amount }} {{ __('purchase_orders.car_unit') }}</td>
                   <template v-for="(purchase_order_dealer, purchase_order_dealer_index) in purchase_order_dealer_list" >
                       <td class="text-end custom-border-right">
                            @{{ getNumberWithCommas(purchase_order_dealer.dealer_price_list[index].car_price) }}
                       </td>
                       <td class="text-end custom-border-right">
                            @{{ getNumberWithCommas(parseFloat(purchase_order_dealer.dealer_price_list[index].car_price * item.amount).toFixed(2)) }}
                        </td>
                        <input type="hidden" v-bind:name="'pr_dealer['+ purchase_order_dealer_index +'][dealer_price_list]['+ index +'][car_id]'" v-bind:value="purchase_order_dealer.dealer_price_list[index].car_id">
                        <input type="hidden" v-bind:name="'pr_dealer['+ purchase_order_dealer_index +'][dealer_price_list]['+ index +'][car_price]'" v-bind:value="purchase_order_dealer.dealer_price_list[index].car_price">
                        <input type="hidden" v-bind:name="'pr_dealer['+ purchase_order_dealer_index +'][dealer_price_list]['+ index +'][vat]'" v-bind:value="purchase_order_dealer.dealer_price_list[index].vat">
                        <input type="hidden" v-bind:name="'pr_dealer['+ purchase_order_dealer_index +'][dealer_price_list]['+ index +'][vat_exclude]'" v-bind:value="purchase_order_dealer.dealer_price_list[index].vat_exclude">
                   </template>
                   <input type="hidden" v-bind:name="'cars[amount]['+ item.id +']'" v-bind:value="item.amount">
                </tr>
                <tr>
                    <th></th>
                    <th colspan="3">สรุปราคารวมทั้งหมด</th>
                    <template v-for="(purchase_order_dealer, purchase_order_dealer_index) in purchase_order_dealer_list" >
                        <th class="text-end" colspan="2">
                            @{{ getNumberWithCommas(purchase_order_dealer.total) }}
                        </th>
                    </template>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">" {{ __('lang.no_list').__('purchase_orders.dealer_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if(!isset($view))
    @if(!isset($cannot_add))
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addDealer()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @endif
    @endif
</div>
@include('admin.purchase-requisitions.modals.dealers')
