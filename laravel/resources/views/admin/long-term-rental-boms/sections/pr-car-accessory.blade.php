<div id="pr-car" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th style="width:20%">{{ __('purchase_requisitions.car_class') }}</th>
                <th style="width:20%">{{ __('purchase_requisitions.car_color') }}</th>
                <th style="width:20%">{{ __('purchase_requisitions.car_amount') }}</th>
                <th style="width:20%">{{ __('purchase_requisitions.remark') }}</th>
                @if (!isset($view))
                    <th style="width:20%" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="pr_car_list.length > 0">
                <tr v-for="(item, index) in pr_car_list">
                    <td style="width: 1%;">@{{ index + 1 }}</td>
                    <td style="width: 40%;white-space: normal;">@{{ item.car_class_text }}</td>
                    <td>@{{ item.car_color_text }}</td>
                    <td>@{{ item.amount_car }} {{ __('purchase_requisitions.car_unit') }}</td>
                    <td>@{{ item.remark }}</td>
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
                                            <a class="dropdown-item" v-on:click="editCar(index)"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                            <a class="dropdown-item btn-delete-row" v-on:click="removeCar(index)"><i
                                                    class="fa fa-trash-alt me-1"></i> ลบ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endif
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][car_class_id]'" id="car_class_id"
                        v-bind:value="item.car_class_id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][car_color_id]'" id="car_color_id"
                        v-bind:value="item.car_color_id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][amount_car]'" id="amount_car"
                        v-bind:value="item.amount_car">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][remark]'" id="remark"
                        v-bind:value="item.remark">
                </tr>
                <tr>
                    <td></td>
                    <td>{{ __('purchase_requisitions.summary_car_detail') }}</td>
                    <td></td>
                    <td id="total_car" v-bind:value="total_car">@{{ total_car }}
                        {{ __('purchase_requisitions.car_unit') }}</td>
                    <td></td>
                    @if (!isset($view))
                        <td></td>
                    @endif
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">"
                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="addCar()"
                    id="openModal">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif
</div>
<br>

<div id="acc-form">
    @if (!isset($view))
        <div class="row push">
            <div class="col-sm-8">
                <x-forms.select-option id="accessory_field" :value="null" :list="null" :label="__('purchase_requisitions.accessories')"
                    :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
            </div>
            <div class="col-sm-4">
                <x-forms.input-new-line id="amount_accessory_field" :value="null" :label="__('purchase_requisitions.car_amount')"
                    :optionals="['oninput' => true, 'type' => 'number']" />
            </div>
        </div>
    @endif
    @if (!isset($view))
        <div class="row mb-4">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary" onclick="addAccessory()">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif

</div>
<div class id="pr-accessory" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('car_classes.accessories') }}</th>
                <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="car_accessories.length > 0">
                <tr v-for="(item, index) in car_accessories">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.accessory_text }}</td>
                    <td>@{{ item.amount_accessory }}</td>
                    @if (!isset($view))
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <a class="dropdown-item btn-delete-row" v-on:click="removeAccessory(index)"><i
                                            class="fa fa-trash-alt me-1"></i></a>
                                </div>
                            </div>
                        </td>
                    @endif
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">
                        " {{ __('lang.no_list') . __('purchase_requisitions.accessories_table') }} "
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-for="(item, index) in car_accessories">
        <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_id]'" v-bind:value="item.accessory_id">
        <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_amount]'"
            v-bind:value="item.amount_accessory">
    </div>
</div>
@include('admin.long-term-rental-boms.modals.car-accessory-modal')
