<div class="block {{ __('block.styles') }}">
    @section('block_options_2')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    @if (empty($view))
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary" onclick="add2()" id="add-spare-part">{{ __('lang.add') }}</button>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    @endsection

    @include('admin.components.block-header', [
        'text' => __('accident_orders.spare_parts_supplier_all'),
        'block_option_id' => '_2',
    ])
    <div class="block-content">
        <div class="row mb-3 mt-2">
            <div class="mb-3" id="app2" v-cloak data-detail-uri="" data-title="">
                <div class="table-wrap">
                    <table class="table table-striped">
                        <thead class="bg-body-dark">
                            <th style="width: 25%">{{ __('accident_orders.spare_parts_supplier') }}</th>
                            <th style="width: 25%">{{ __('accident_orders.spare_part_cost') }}</th>
                            <th style="width: 25%">{{ __('accident_orders.spare_part_discount') }}</th>
                            <th style="width: 15%">{{ __('accident_orders.spare_part_total') }}</th>
                            <th class="sticky-col "></th>
                        </thead>
                        <tbody v-if="inputs.length > 0">
                            <tr v-for="(input,k) in inputs">
                                <td>
                                    <input type="text" class="form-control number-format supplier"
                                        v-model="inputs[k].supplier">
                                    <input type="hidden" v-bind:name="'spare['+ k+ '][supplier]'" id="supplier"
                                        v-model="inputs[k].supplier" class="supplier">
                                </td>
                                <td>
                                    <input type="text" class="form-control number-format spare_parts"
                                        v-model="inputs[k].spare_parts" @change="formatNumber(k)"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    <input type="hidden" v-bind:name="'spare['+ k+ '][spare_parts]'" id="spare_parts"
                                        v-model="inputs[k].spare_parts" class="spare_parts">
                                </td>
                                <td>
                                    <input type="text" class="form-control number-format discount_spare_parts"
                                        v-model="inputs[k].discount_spare_parts" @change="formatNumber2(k)"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    <input type="hidden" v-bind:name="'spare['+ k+ '][discount_spare_parts]'"
                                        id="discount_spare_parts" v-model="inputs[k].discount_spare_parts" class="discount_spare_parts">
                                </td>
                                <td class="number-format" id="total">
                                    @{{ inputs[k].total }}
                                </td>
                                <td>
                                    @if (empty($view))
                                        <a class="btn btn-light" v-on:click="remove(k)"><i class="fa-solid fa-trash-can"
                                                style="color:red"></i></a>
                                    @endif
                                </td>

                            </tr>
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
