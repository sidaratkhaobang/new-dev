<div class="modal fade" id="modal-tor-line" tabindex="-1" aria-labelledby="modal-tor-line" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable"
        style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tor-line-modal-label"><i class="fa fa-plus-circle me-1"></i>เพิ่มรถ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bom_car_show" style="display: none;">
                    <div class="row mb-3">
                        <div class="col-md-6"></div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="bom_id" :value="null" :list="null" :label="'เลือกชื่อ/เลขที่ BOM'"
                                :optionals="['ajax' => true]" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 mb-3" id="manual_car_show" style="display: none;">
                    <div class="row">
                        <div class="col-md-9 text-left">
                            <span id="manual-car-body-modal-label">{{ __('long_term_rentals.add_list_car') }}</span>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-primary" onclick="addManualLine()"><i
                                    class="fa fa-plus-circle"></i> {{ __('lang.add') }}</button>
                        </div>
                    </div>
                </div>

                <div id="tor-line" data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 2%;">#</th>
                                <th style="width: 25%;">{{ __('long_term_rentals.car_class') }}</th>
                                <th style="width: 10%;">{{ __('long_term_rentals.car_color') }}</th>
                                <th style="width: 10%;">{{ __('long_term_rentals.car_amount_unit') }}</th>
                                <th style="width: 15%;">{{ __('long_term_rentals.remark') }}</th>
                                <th style="width: 20%;">{{ __('long_term_rentals.need_accessory') }}</th>
                            </thead>
                            <tbody v-if="tor_lines.length > 0">
                                <tr v-for="(item, index) in tor_lines">
                                    <td>@{{ index + 1 }}</td>
                                    <td>
                                        <select-car-class :id="'car_class_' + index" class="form-control list"
                                            name="car_class_id" v-model="tor_lines[index].car_class_id"
                                            style="width: 250px;">
                                        </select-car-class>
                                    </td>
                                    <td>
                                        <select-car-color :id="'car_color_' + index" class="form-control list"
                                            name="car_color_id" v-model="tor_lines[index].car_color_id"
                                            style="width: 200px;">
                                        </select-car-color>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="tor_lines[index].amount">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="tor_lines[index].remark">
                                    </td>
                                    <td>
                                        <div class="form-check form-check-inline mt-1">
                                            <input type="radio" class="form-check-input" :id="'is_have_1_' + index"
                                                value="1" v-model="tor_lines[index].is_have">ต้องซื้อ
                                        </div>
                                        <div class="form-check form-check-inline mt-1">
                                            <input type="radio" class="form-check-input" :id="'is_have_0_' + index"
                                                value="0" v-model="tor_lines[index].is_have">ไม่ต้องซื้อ
                                        </div>
                                    </td>
                                    <input type="hidden" v-bind:name="'tor_lines['+ index +'][bom_line_id]'"
                                        v-bind:value="item.bom_line_id">
                                    <input type="hidden" v-bind:name="'tor_lines['+ index +'][tor_line_id]'"
                                        v-bind:value="item.tor_line_id">
                                    <input type="hidden" v-bind:name="'tor_lines['+ index +'][car_class_id]'"
                                        id="car_class_id" v-bind:value="item.car_class_id">
                                    <input type="hidden" v-bind:name="'cars['+ index +'][car_color_id]'"
                                        id="car_color_id" v-bind:value="item.car_color_id">
                                    <input type="hidden" v-bind:name="'tor_lines['+ index +'][amount]'" id="amount"
                                        v-bind:value="item.amount">
                                    <input type="hidden" v-bind:name="'tor_lines['+ index+ '][is_have]'" id="is_have"
                                        v-model="tor_lines[index].is_have">
                                    <input type="hidden" v-bind:name="'tor_lines['+ index+ '][remark]'" id="remark"
                                        v-model="tor_lines[index].remark">
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">"
                                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row push mb-5">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="remark_tor" :value="$d->remark_tor" :label="__('long_term_rentals.tor_description')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveTorLine()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
<style>
    .hidden {
        display: none;
    }
</style>
