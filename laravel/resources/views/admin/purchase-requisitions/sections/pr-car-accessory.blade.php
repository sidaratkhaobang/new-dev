<h4>{{ __('purchase_requisitions.data_car_table') }}</h4>
<hr>
<div id="pr-car" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('purchase_requisitions.car_class') }}</th>
                <th>{{ __('purchase_requisitions.car_color') }}</th>
                <th>{{ __('purchase_requisitions.car_amount') }}</th>
                <th>{{ __('purchase_requisitions.remark') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="pr_car_list.length > 0">
                <tr v-for="(item, index) in pr_car_list">
                    <td style="width: 1%;">@{{ index + 1 }}</td>
                    <td style="width: 40%;white-space: normal;">@{{ item.car_class_text }}</td>
                    <td>@{{ item.car_color_text }}</td>
                    <td>@{{ item.amount_car }} {{ __('purchase_requisitions.car_unit') }}</td>
                    <td>@{{ item.remark_car }}</td>
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
                                        <a class="dropdown-item" v-on:click="editCar(index,item.amount_car_total)"><i
                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                        <a class="dropdown-item btn-delete-row" v-on:click="removeCar(index)"><i
                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][car_class_id]'" id="car_class_id"
                        v-bind:value="item.car_class_id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][car_color_id]'" id="car_color_id"
                        v-bind:value="item.car_color_id">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][amount_car]'" id="amount_car"
                        v-bind:value="item.amount_car">
                    <input type="hidden" v-bind:name="'pr_car['+ index+ '][remark_car]'" id="remark_car"
                        v-bind:value="item.remark_car">

                </tr>
                <tr>
                    <td></td>
                    <td>{{ __('purchase_requisitions.summary_car_detail') }}</td>
                    <td></td>
                    <td id="total_car" v-bind:value="total_car">@{{ total_car }}
                        {{ __('purchase_requisitions.car_unit') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">"
                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                </tr>
            </tbody>
        </table>
        <div v-for="(item, index) in all_accessories">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_id]'"
                v-bind:value="item.accessory_id">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][car_index]'" v-bind:value="item.car_index">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_amount]'"
                v-bind:value="item.amount_per_car_accessory">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][remark_accessory]'"
                v-bind:value="item.remark_accessory">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][type_accessories]'"
                v-bind:value="item.type_accessories">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="addCar()"
                id="openModal">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
<br>
@include('admin.purchase-requisitions.modals.car-accessory-modal')
