<div id="customer-billing-address" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 2px;">#</th>
                <th style="width: 25%;">{{ __('customers.name_all') }}</th>
                <th style="width: 20%;">{{ __('customers.tax_no') }}</th>
                <th style="width: 30%;">{{ __('customers.address') }}</th>
                <th style="width: 10%;">{{ __('customers.province') }}</th>
                <th style="width: 10%;">{{ __('customers.email') }}</th>
                <th style="width: 10%;">{{ __('customers.tel_driver') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="customer_billing_address_list.length > 0">
                <tr v-for="(item, index) in customer_billing_address_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.tax_no }}</td>
                    <td class="td-break" style="white-space: pre-wrap;">@{{ item.address }}</td>
                    <td>@{{ item.province_name }}</td>
                    <td>@{{ item.email }}</td>
                    <td>@{{ item.tel }}</td>
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
                                        <a class="dropdown-item" v-on:click="editBillingAddress(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row"
                                            v-on:click="removeBillingAddress(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][name]'" id="name"
                        v-bind:value="item.name">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][tax_no]'" id="tax_no"
                        v-bind:value="item.tax_no">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][address]'" id="address"
                        v-bind:value="item.address">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][province_id]'" id="province_id"
                        v-bind:value="item.province_id">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][district_id]'" id="district_id"
                        v-bind:value="item.district_id">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][subdistrict_id]'" id="subdistrict_id"
                        v-bind:value="item.subdistrict_id">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][email]'" id="email"
                        v-bind:value="item.email">
                    <input type="hidden" v-bind:name="'customer_billing_address['+ index+ '][tel]'" id="tel"
                        v-bind:value="item.tel">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">“
                        {{ __('lang.no_list') . __('customers.billing_address_table') }} “</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addBillingAddress()"
                id="openModal">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
@include('admin.customers.modals.billing-address-modal')
