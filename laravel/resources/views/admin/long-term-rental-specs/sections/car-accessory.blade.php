<div class="row push mb-3">
    <div class="col-auto mt-2">
        <h4>{{ __('long_term_rentals.car_table') }}</h4>
    </div>
    @if (!isset($view_only))
    @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) !== 0)
        @if (!isset($accessory_controller))
            <div class="col-sm-3">
                <div class="col-md-3 text-start">
                    <button type="button" class="btn btn-primary" onclick="addBom()">Bom</button>
                </div>
            </div>
        @endif
        @endif
    @endif
</div>
<hr>
<div id="car-accessory" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th></th>
                <th class="text-center">#</th>
                <th width="1%"></th>
                <th style="max-width:200px; word-break: break-all; white-space: normal;">
                    {{ __('long_term_rentals.tor_description') }}</th>
                <th>{{ __('long_term_rentals.car_class') }}</th>
                <th>{{ __('long_term_rentals.car_color') }}</th>
                <th>{{ __('long_term_rentals.optional_accessory') }}</th>
                <th>{{ __('long_term_rentals.car_amount') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="car_list.length > 0">
                <template v-for="(item, index) in car_list">
                    <tr>
                        <td class="text-center toggle-table" style="width: 30px">
                            <i class="fa fa-angle-right text-muted"></i>
                        </td>
                        <td class="text-center">@{{ item.tor_index }}</td>
                        <td class="text-center">
                            <div class="custom-control custom-checkbox d-inline-block form-check">
                                <input type="checkbox" class="form-check-input custom-control-input"
                                    :name="'tor_line_check_input[]'" :data-parent-id="item.lt_rental_tor_id"
                                    :data-id="item.id" v-model="item.is_rental_line"
                                    @change="checkedCheckBox(item.lt_rental_tor_id, item.id)" :value="item.id">
                            </div>
                        </td>
                        <td style="max-width:200px; word-break: break-all; white-space: normal;">@{{ item.remark_tor }}
                        </td>
                        <td>@{{ item.car_class_text }}</td>
                        <td>@{{ item.car_color_text }}</td>
                        <td>@{{ convertToText(item.have_accessories) }}</td>
                        <td>@{{ item.amount_car }} {{ __('long_term_rentals.car_unit') }}</td>
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        @if (!isset($view_only))
                                            @if (isset($accessory_controller))
                                                <template v-if="item.have_accessories == {{ STATUS_ACTIVE }}">
                                                    <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                </template>
                                                <template v-else>
                                                    <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false" disabled>
                                                        <i class="fa fa-ellipsis-vertical"></i>

                                                    </button>
                                                </template>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                        @endif
                                        @if (isset($view_only))
                                            @if (isset($accessory_controller))
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    @can(Actions::View . '_' . Resources::LongTermRentalSpecsAccessory)
                                                        <a class="dropdown-item"
                                                            :href="redirectToViewAccessory(item.lt_rental_tor_id, item.id)">
                                                            <i class="fa fa-eye me-1"></i> ดูข้อมูล
                                                        </a>
                                                    @endcan
                                                </div>
                                            @else
                                                @can(Actions::View . '_' . Resources::LongTermRentalSpec)
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item"
                                                        :href="redirectToView(item.lt_rental_tor_id)">
                                                        <i class="fa fa-eye me-1"></i> ดูข้อมูล
                                                    </a>
                                                </div>
                                                @endcan
                                            @endif
                                        @else
                                            @if (isset($accessory_controller))
                                                @can(Actions::Manage . '_' . Resources::LongTermRentalSpecsAccessory)
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item"
                                                        :href="redirectToEditAccessory(item.lt_rental_tor_id, item.id)">
                                                        <i class="far fa-edit me-1"></i> แก้ไข
                                                    </a>
                                                </div>
                                                @endcan
                                            @else
                                                @can(Actions::Manage . '_' . Resources::LongTermRentalSpec)
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                        <a class="dropdown-item"
                                                            :href="redirectToEdit(item.lt_rental_tor_id)">
                                                            <i class="far fa-edit me-1"></i> แก้ไข
                                                        </a>
                                                    </div>
                                                @endcan
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <template v-if="item.accessory_list.length > 0" id="list">
                        <tr class="tr-last-item">
                            <td class="td-table"></td>
                            <td class="td-table table-wrap" colspan="8">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                        <th style="width: 10%;">#</th>
                                        <th>{{ __('long_term_rentals.car_accessory') }}</th>
                                        <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                                        <th>{{ __('long_term_rentals.remark') }}</th>
                                    </thead>
                                    <tbody v-for="(accessory, accessory_index) in item.accessory_list">
                                        <td>@{{ accessory_index + 1 }}</td>
                                        <td>@{{ accessory.accessory_text }}</td>
                                        <td>@{{ accessory.amount_accessory }}</td>
                                        <td>@{{ accessory.remark }}</td>
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][id]'"
                                            id="accessory_id" v-bind:value="accessory.accessory_id">
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][amount]'"
                                            id="accessory_amount" v-bind:value="accessory.amount_accessory">
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </template>
                    <template v-else id="list">
                        <tr class="tr-last-item">
                            <td class="td-table"></td>
                            <td class="td-table table-wrap" colspan="8">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                        <th style="width: 10%;">#</th>
                                        <th>{{ __('long_term_rentals.car_accessory') }}</th>
                                        <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                                        <th>{{ __('long_term_rentals.remark') }}</th>
                                    </thead>
                                    <tbody>
                                        <td class="text-center td-table table-wrap" colspan="4">
                                            " {{ __('lang.no_list') }} "
                                        </td>
                                </table>
                            </td>
                        </tr>
                    </template>
                </template>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ __('long_term_rentals.summary_car_detail') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td id="total_car" v-bind:value="total_car">@{{ total_car }}
                        {{ __('long_term_rentals.car_unit') }}</td>
                    <td></td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="9">"
                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
@include('admin.long-term-rental-specs.modals.car-accessory-modal')
@include('admin.long-term-rental-specs.modals.bom-car-modal')
