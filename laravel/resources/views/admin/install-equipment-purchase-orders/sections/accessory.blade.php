<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('install_equipments.info'),
    ])
    <div class="block-content">
        <div class="mb-4" id="install-equipment-pos" v-cloak data-detail-uri="" data-title="">
            @include('admin.install-equipment-purchase-orders.sections.summary')
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>#</th>
                        {{-- <th>{{ __('install_equipments.accessory_code') }}</th> --}}
                        <th>{{ __('install_equipments.accessory') }}</th>
                        <th>{{ __('install_equipments.class') }}</th>
                        <th class="text-end">{{ __('install_equipments.amount_per_unit') }}</th>
                        <th class="text-end">{{ __('install_equipments.price_per_unit') }}</th>
                        <th class="text-end">{{ __('install_equipments.discount') }}</th>
                        <th class="text-end">{{ __('install_equipment_pos.exclude_vat_price') }}</th>
                        <th class="text-end">{{ __('install_equipment_pos.vat_7') }}</th>
                        <th class="text-end">{{ __('install_equipment_pos.total_net_price') }}</th>
                        @if (!isset($view_only))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="install_equipment_po_line_list.length > 0">
                        <tr v-for="(item, index) in install_equipment_po_line_list">
                            <td>@{{ index + 1 }}</td>
                            {{-- <td>@{{ item.accessory_code }}</td> --}}
                            <td>@{{ item.accessory_text }}</td>
                            <td>@{{ item.accessory_class }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.amount) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.total) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.discount) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.overall_subtotal) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.overall_vat) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.overall_total) }}</td>
                            @if (!isset($view_only))
                                <td class="sticky-col text-center">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item" href="javascript:void(0)" v-on:click="edit(index)"><i class="far fa-edit me-1"></i> แก้ไข</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][id]'" id="install_equipment_id" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][accessory_id]'" id="accessory_id" v-bind:value="item.accessory_id">
                            {{-- <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][supplier_id]'" id="supplier_id" v-bind:value="item.supplier_id"> --}}
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][amount]'" id="amount" v-bind:value="item.amount">
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][discount]'" id="discount" v-bind:value="item.discount">
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][subtotal]'" id="subtotal" v-bind:value="item.subtotal">
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][vat]'" id="vat" v-bind:value="item.vat">
                            <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][total]'" id="subtotal" v-bind:value="item.total">
                            {{-- <input type="hidden" v-bind:name="'install_equipment_po_lines['+ index +'][remark]'" id="remark" v-bind:value="item.remark"> --}}
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="{{ !isset($view_only) ? '8' : '9' }}">" {{ __('lang.no_data') }} "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- <div v-if="pending_delete_install_equipment_po_line_ids.length > 0">
                <template v-for="item in pending_delete_install_equipment_po_line_ids"> 
                    <input type="hidden" v-bind:name="'delete_install_equipment_po_line_ids[]'" v-bind:value="item">
                </template>
            </div>
            <div class="row">
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary" onclick="addInstallEquipmentPOLine()">{{ __('lang.add') }}</button>
                </div>
            </div> --}}
            @include('admin.install-equipment-purchase-orders.modals.accessory')
        </div>
    </div>
</div>