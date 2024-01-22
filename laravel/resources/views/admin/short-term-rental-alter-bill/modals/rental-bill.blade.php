<div class="modal fade" id="rental-bill-modal" role="dialog" style="overflow:hidden;" aria-labelledby="rental-bill-modal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rental-bill-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.bill_info') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_name" :value="null" :label="__('short_term_rentals.bill_name')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.bill_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_description" :value="null" :label="__('short_term_rentals.description')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_amount" :value="null" :label="__('short_term_rentals.amount')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_subtotal" :value="null" :label="__('short_term_rentals.price_per_unit')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>

                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.select-option id="rental_car_id" :value="null" :list="$cars"
                            :label="__('short_term_rentals.car_select')" :optionals="['required' => true]" />
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-primary"
                            onclick="addRentalLine()">{{ __('lang.add') }}</button>
                    </div>
                </div>
                <div data-detail-uri="" data-title="" id="rental-lines">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 1px;">#</th>
                                <th>ชื่อ</th>
                                <th>รายละเอียด</th>
                                <th class="text-end">ทะเบียน</th>
                                <th class="text-end">จำนวน</th>
                                <th class="text-end">ราคา</th>
                                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                            </thead>
                            <tbody v-if="rental_line_list.length > 0">
                                <tr v-for="(item, index) in rental_line_list">

                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.summary_name_i }}</td>
                                    <td>
                                        <span v-if="item.item_type != extracost">@{{ item.summary_description_i }}</span>
                                        <span v-else>@{{ item.summary_description_i }}</span>
                                    </td>
                                    <td class="text-end">@{{ item.license_plate }}</td>
                                    <td class="text-end">@{{ item.amount }}</td>
                                    <td class="text-end">@{{ getNumberWithCommas(item.total) }}</td>
                                    {{-- <td class="text-end">@{{ getNumberWithCommas(getTotalOfEachRentalLine(item.subtotal, item.amount)) }}</td> --}}
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
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="dropdown-dropleft-dark">
                                                            <a v-if="item.item_type === extracost"
                                                                class="dropdown-item btn-delete-row"
                                                                href="javascript:void(0)" v-on:click="remove(index)"><i
                                                                    class="fa fa-trash-alt me-1"></i> ลบ
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][id]'" id="id"
                                        v-bind:value="item.id">
                                    <input type="hidden"
                                        v-bind:name="'rental_lines['+ index +'][summary_display_name]'"
                                        id="rental_line_summary_display_name" v-bind:value="item.summary_name_i">
                                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][summary_description]'"
                                        id="rental_line_summary_description" v-bind:value="item.summary_description_i">
                                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][amount]'"
                                        id="rental_lines_amount" v-bind:value="item.amount">
                                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][subtotal]'"
                                        id="rental_lines_subtotal" v-bind:value="item.subtotal">
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="7">
                                        " {{ __('lang.no_list') . __('short_term_rentals.bill_info') }} "
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h4>{{ __('short_term_rentals.tax_invoice_detail') }}</h4>
                <hr>
                <div class="row push mb-5">
                    <div class="col-sm-12">
                        <x-forms.checkbox-inline id="check_customer_address" :list="[
                            [
                                'id' => 1,
                                'name' => __('short_term_rentals.check_customer_address'),
                                'value' => 1,
                            ],
                        ]" :label="null"
                            :value="['1']" />
                    </div>
                </div>
                @include('admin.short-term-rental-alter-bill.sections.tax-invoice')
                <x-forms.hidden id="is_customer_address" :value="'1'" />
                <x-forms.hidden id="customer_billing_address_id" :value="null" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addRentalBill()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
