@section('block_options_bom')
    @if ($mode == MODE_CREATE)
        <div class="block-options-item">
            <a class="btn btn-primary" onclick="openBOMModal()" href="javascript:void(0)">
                <i class="icon-add-circle me-1"></i>BOM
            </a>
        </div>
    @endif
@endsection
<div class="block {{ __('block.styles') }}" id="install-equipments" v-cloak data-detail-uri="" data-title="">
    @include('admin.install-equipments.modals.bom')
    @include('admin.components.block-header', [
        'block_header_class' => 'justify-content-start',
        'block_title_class' => 'flex-grow-0',
        'text' => __('install_equipments.accessory_list'),
        'block_option_id' => '_bom'
    ])
    <div class="block-content">
        <div class="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>#</th>
                        <th>{{ __('install_equipments.accessory') }}</th>
                        <th>{{ __('install_equipments.class') }}</th>
                        <th class="text-end">{{ __('install_equipments.amount_per_unit') }}</th>
                        <th class="text-end">{{ __('install_equipments.price_per_unit') }}</th>
                        <th>{{ __('install_equipments.supplier_en') }}</th>
                        <th>{{ __('lang.remark') }}</th>
                        @if (!$is_view_only)
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="install_equipment_line_list.length > 0">
                        <tr v-for="(item, index) in install_equipment_line_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.accessory_text }}</td>
                            <td>@{{ item.accessory_class }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.amount) }}</td>
                            <td class="text-end">@{{ getNumberWithCommas(item.price) }}</td>
                            <td>@{{ item.supplier_text }}</td>
                            <td>@{{ item.remark }}</td>
                            @if (!$is_view_only)
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action-vue')
                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][id]'" id="install_equipment_id" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][accessory_id]'" id="accessory_id" v-bind:value="item.accessory_id">
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][supplier_id]'" id="supplier_id" v-bind:value="item.supplier_id">
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][amount]'" id="amount" v-bind:value="item.amount">
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][price]'" id="price" v-bind:value="item.price">
                            <input type="hidden" v-bind:name="'install_equipments['+ index +'][remark]'" id="remark" v-bind:value="item.remark">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="{{ !isset($view_only) ? '8' : '9' }}">" {{ __('lang.no_data') }} "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="pending_delete_install_equipment_ids.length > 0">
                <template v-for="item in pending_delete_install_equipment_ids"> 
                    <input type="hidden" v-bind:name="'delete_install_equipment_ids[]'" v-bind:value="item">
                </template>
            </div>
            @if (!$is_view_only)
            <div class="row">
                <div class="col-md-12 text-end">
                    @can(Actions::Manage . '_' . Resources::InstallEquipment)
                    <button id="add-accessory-btn" type="button" class="btn btn-primary" onclick="addInstallEquipment()">{{ __('lang.add') }}</button>
                    @endcan
                </div>
            </div>
            @endif
            @include('admin.install-equipments.modals.accessory')
        </div>
    </div>
</div>