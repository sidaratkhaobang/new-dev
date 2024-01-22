<div>
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th style="width: 1px;">#</th>
                <th>ชื่อ</th>
                <th>รายละเอียด</th>
                <th class="text-end">ทะเบียนรถ</th>
                <th class="text-end">จำนวน</th>
                <th class="text-end">ราคา</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
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
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" href="javascript:void(0)"
                                                v-on:click="edit(index)"><i class="far fa-edit me-1"></i> แก้ไข</a>
                                            <a v-if="item.item_type === extracost" class="dropdown-item btn-delete-row"
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
                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][summary_display_name]'"
                        id="rental_line_summary_display_name" v-bind:value="item.summary_name_i">
                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][summary_description]'"
                        id="rental_line_summary_description" v-bind:value="item.summary_description_i">
                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][amount]'" id="rental_lines_amount"
                        v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][subtotal]'" id="rental_lines_subtotal"
                        v-bind:value="item.subtotal">
                    <input type="hidden" v-bind:name="'rental_lines['+ index +'][car_id]'" id="rental_lines_car_id"
                        v-bind:value="item.car_id">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (!isset($view))
        <div class="row mb-6">
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-primary"
                    onclick="openRentalLineModal()">{{ __('lang.add') }}</button>
            </div>
        </div>
    @endif
    {{-- @include('admin.short-term-rental-summary.modals.rental-line') --}}
</div>
