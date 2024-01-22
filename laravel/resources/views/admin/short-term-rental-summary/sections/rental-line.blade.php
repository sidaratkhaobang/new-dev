<div>
    <div class="table-wrap">
        <table class="table table-striped-custom">
            <thead class="bg-body-dark">
                <th style="width: 1px;">#</th>
                <th>{{ __('short_term_rentals.package_name') }}</th>
                {{-- <th>{{ __('short_term_rentals.description') }}</th> --}}
                {{-- <th></th> --}}
                <th class="text-start">{{ __('short_term_rentals.license_plate') }}</th>
                <th class="text-start">{{ __('short_term_rentals.car') }}</th>
                <th class="text-end">{{ __('short_term_rentals.price_per_unit') }}</th>
                <th class="text-end">{{ __('short_term_rentals.amount') }}</th>
                <th class="text-end">{{ __('short_term_rentals.summary_subtotal') }} <span class="font-size-xs" >{{ __('short_term_rentals.excl_vat') }}</span></th>
                <th class="text-end">{{ __('short_term_rentals.discount') }}</th>
                <th class="text-end">{{ __('short_term_rentals.summary_total') }} <span class="font-size-xs" >{{ __('short_term_rentals.incl_vat') }}</span></th>
                <th class="sticky-col text-center"></th>
            </thead>
            <tbody v-if="cars.length > 0">
                <template v-for="(item, index) in cars">
                    <tr :class="{ 'pair-row': index % 2 === 0 }">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ item.product_name }}</td>
                        <td class="text-start">
                            @{{ item.license_plate }}
                        </td>
                        <td class="text-start border-bottom">
                            <img :src="item.image_url" alt="..." class="style-img" style="width: 100px; max-height:50px;">
                            @{{ item.class_full_name }}
                        </td>
                        <td class="text-end border-bottom">@{{ number_format(item.unit_price) }}</td>
                        <td class="text-end border-bottom">@{{ number_format(item.amount) }}</td>
                        <td class="text-end border-bottom">@{{ number_format(item.subtotal) }}</td>
                        <td class="text-end border-bottom">@{{ number_format(item.discount) }}</td>
                        <td class="text-end border-bottom">@{{ number_format(item.total) }}</td>

                        <td class="text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" href="javascript:void(0)"
                                                v-on:click="editProduct(index, item, item.car_id)"><i class="far fa-edit me-1"></i> แก้ไข
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <input type="hidden" v-bind:name="'rental_lines['+ index +'][rental_line_id]'" v-bind:value="item.rental_line_id">
                        <input type="hidden" v-bind:name="'rental_lines['+ index +'][amount]'" v-bind:value="item.amount">
                        <input type="hidden" v-bind:name="'rental_lines['+ index +'][subtotal]'" v-bind:value="item.subtotal">
                        <input type="hidden" v-bind:name="'rental_lines['+ index +'][car_id]'" v-bind:value="item.car_id">
                    </tr>

                    <template v-if="item.product_additionals && item.product_additionals.length > 0">
                        <tr :class="{ 'pair-row': index % 2 === 0 }">
                            <td class="pt-0 pb-2" colspan="3"></td>
                            <td class="pt-2 pb-2 text-start fw-bold">ออฟชั่นเสริม</td>
                            <td class="pt-0 pb-2" colspan="5"></td>
                            <td class="pt-0 pb-2 text-center">
                                <div class="btn-group">
                                    <div class="col-sm-12">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    v-on:click="editProductAdditional(index, item, item.car_id)"><i
                                                        class="far fa-edit me-1"></i> แก้ไข</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <template v-for="(product_value, product_index) in item.product_additionals">
                            <tr :class="{ 'pair-row': index % 2 === 0 }">
                                <td class="pt-0 pb-2" colspan="3"></td>
                                <td class="pt-0 pb-2 text-start">@{{ product_value.product_additional_name }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(product_value.unit_price) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(product_value.amount) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(product_value.subtotal) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(product_value.discount) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(product_value.total) }}</td>
                                <td class="pt-0 pb-2" ></td>
                            </tr>
                        </template>
                    </template>
                    <template v-if="item.extras && item.extras.length > 0">
                        <tr :class="{ 'pair-row': index % 2 === 0 }">
                            <td class="pt-0 pb-2" colspan="3"></td>
                            <td class="pt-2 pb-2 text-start fw-bold">สินค้าและบริการเพิ่มเติม</td>
                            <td class="pt-0 pb-2" colspan="5"></td>
                            <td class="pt-0 pb-2 text-center">
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
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    v-on:click="editExtra(index, item, item.car_id)"><i
                                                        class="far fa-edit me-1"></i> แก้ไข</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <template v-for="(extra_value, extra_index) in item.extras">
                            <tr :class="{ 'pair-row': index % 2 === 0 }">
                                <td class="pt-0 pb-2" colspan="3"></td>
                                <td class="pt-0 pb-2 text-start">@{{ extra_value.extra_name }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(extra_value.unit_price) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(extra_value.amount) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(extra_value.subtotal) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(extra_value.discount) }}</td>
                                <td class="pt-0 pb-2 text-end">@{{ number_format(extra_value.total) }}</td>
                                <td class="pt-0 pb-2"></td>
                            </tr>
                        </template>
                    </template>
                </template>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@include('admin.short-term-rental-summary.modals.rental-line-car')
@include('admin.short-term-rental-summary.modals.rental-line')
@include('admin.short-term-rental-summary.modals.extra')