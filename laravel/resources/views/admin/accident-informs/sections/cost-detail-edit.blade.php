<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.cost_detail'),
    ])
    <div class="block-content">
        <div id="cost-vue" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th>{{ __('accident_informs.date') }}</th>
                        <th>{{ __('accident_informs.list') }}</th>
                        <th>{{ __('accident_informs.price') }}</th>
                        <th>{{ __('accident_informs.remark') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="cost_list.length > 0">
                        <tr v-for="(item, index) in cost_list">
                            <td>@{{ format_date(item.cost_date) }}</td>
                            <td>@{{ item.cost_name }}</td>
                            <td>@{{ getNumberWithCommas(item.cost_price) }}</td>
                            <td>@{{ item.cost_remark }}</td>
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
                                                    <a class="dropdown-item" v-on:click="editCost(index)"><i
                                                            class="far fa-edit me-1"></i> แก้ไข</a>
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeCost(index)"><i
                                                            class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'cost['+ index+ '][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'cost['+ index+ '][cost_date]'"
                                v-bind:value="item.cost_date">
                            <input type="hidden" v-bind:name="'cost['+ index+ '][cost_name]'"
                                v-bind:value="item.cost_name">
                            <input type="hidden" v-bind:name="'cost['+ index+ '][cost_price]'"
                                v-bind:value="item.cost_price">
                            <input type="hidden" v-bind:name="'cost['+ index+ '][cost_remark]'"
                                v-bind:value="item.cost_remark">
                            {{-- <input type="hidden" v-bind:name="'slide['+ index+ '][lift_price]'" v-bind:value="item.lift_price"> --}}
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">“
                                {{ __('lang.no_list') . __('accident_informs.cost_detail') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (!isset($view))
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-primary" onclick="addCost()"
                            id="openModal">{{ __('lang.add') }}</button>
                    </div>
                </div>
            @endif
        </div>
        <br>
    </div>
</div>
@include('admin.accident-informs.modals.cost-modal')
