<div class="modal fade" id="modal-bom-car" tabindex="-1" aria-labelledby="modal-bom-car" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูลรถ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.car_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="bom_id" :value="null" :list="null" :label="'ชื่อ/เลขที่ Bom'"
                            :optionals="['ajax' => true]" />
                    </div>
                </div>

                <div id="bom-car-line" data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 2%;">#</th>
                                <th style="width: 30%;">{{ __('long_term_rentals.car_class') }}</th>
                                <th style="width: 10%;">{{ __('long_term_rentals.car_color') }}</th>
                                <th style="width: 5%;">{{ __('long_term_rentals.car_amount') }}</th>
                                <th style="width: 5%;">{{ __('long_term_rentals.have_accessory') }}</th>
                                <th style="width: 20%;">{{ __('long_term_rentals.remark') }}</th>
                            </thead>
                            <tbody v-if="bom_cars.length > 0">
                                <tr v-for="(item, index) in bom_cars">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.car_name }}</td>
                                    <td>@{{ item.color_name }}</td>
                                    <td>@{{ item.amount }}</td>
                                    <td>
                                        <select-3 :id="'is_have' + index" class="form-control list" name="is_have"
                                            style="width: 100%;" v-model="bom_cars[index].is_have">
                                        </select-3>

                                    </td>
                                    <td><input type="text" class="form-control" v-model="bom_cars[index].remark">

                                    </td>
                                    <input type="hidden" v-bind:name="'bom_cars['+ index +'][bom_line_id]'" v-bind:value="item.bom_line_id">
                                    <input type="hidden" v-bind:name="'bom_cars['+ index +'][car_class_id]'"
                                        id="car_class_id" v-bind:value="item.car_class_id">
                                    <input type="hidden" v-bind:name="'cars['+ index +'][car_color_id]'"
                                        id="car_color_id" v-bind:value="item.car_color_id">
                                    <input type="hidden" v-bind:name="'bom_cars['+ index +'][amount]'" id="amount"
                                        v-bind:value="item.amount">
                                    <input type="hidden" v-bind:name="'bom_cars['+ index+ '][is_have]'" id="is_have"
                                        v-model="bom_cars[index].is_have">
                                    <input type="hidden" v-bind:name="'bom_cars['+ index+ '][remark]'" id="remark"
                                        v-model="bom_cars[index].remark">
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
            </div>



            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveBomCar()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
