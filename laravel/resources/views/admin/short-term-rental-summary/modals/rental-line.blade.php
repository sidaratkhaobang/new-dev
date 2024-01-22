<div class="modal fade" id="rental-line-modal" role="dialog" style="overflow:hidden;" aria-labelledby="rental-line-modal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rental-line-label">ออฟชั่นเสริม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    @foreach ($cars as $index => $car)
                    <div class="col-sm-12 col-lg-3">
                        <x-forms.checkbox-block id="car_modal_id_{{ $index }}" name="car_modal_id" value="{{ $car->car_id }}" selected="{{ $car->id }}" >
                            <p class="block-title p-0 m-0" >{{ $car->class_full_name }}</p>
                            <p class="block-title p-0 m-0" >{{ $car->license_plate }}</p>
                            <div class="block-img-wrap" >
                                <img src="{{ $car->image_url }}" class="block-img">
                            </div>
                        </x-forms.checkbox-block>
                    </div>
                    @endforeach
                </div>

                <div class="table-wrap db-scroll col-12 extra" style="border-radius:0px ">
                    <table class="table table-striped table-vcenter">
                        <thead style="background: var(--neutral-bg-03, #E2E8F0);border-radius: 0px;">
                            <th style="width: 1px;">#</th>
                            <th style="width: 30%;" >ชื่อสินค้าและบริการ</th>
                            <th style="width: 20%; text-align: right;" >จำนวน</th>
                            <th style="width: 25%; text-align: right;" >ราคาต่อหน่วย</th>
                            <th style="width: 25%; text-align: right;">รวม</th>
                            <th style="width: 1px;" ></th>
                        </thead>
                        <tbody>
                            <template v-if="modal_product_additional.length > 0">
                                <tr v-for="(product_additional,product_additional_key) in modal_product_additional">
                                    <td>
                                        @{{ product_additional_key + 1 }}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control"
                                            :id="'name-' + product_additional_key"
                                            v-bind:name="'product_additional[' + product_additional_key +'][name]'"
                                            v-model="product_additional.product_additional_name" readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-end"
                                            :id="'amount-' + product_additional_key"
                                            v-bind:name="'product_additional[' + product_additional_key +'][amount]'"
                                            v-model="product_additional.amount"  readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-end"
                                            :id="'price-' + product_additional_key"
                                            v-bind:name="'product_additional[' + product_additional_key +'][price]'"
                                            v-model="product_additional.unit_price"
                                            @keyup="setTotalProductAdditional(product_additional_key)"
                                            @change="setTotalProductAdditional(product_additional_key)" />
                                    </td>
                                    <td class="text-end" >
                                        <span> @{{ product_additional.subtotal }}</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </template>
                            <template v-else-if="modal_product_additional.length == 0">
                                <tr class="table-empty add-product-empty">
                                    <td class="text-center" colspan="7">“
                                        {{ __('lang.no_list') }}“</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary add-product"
                    onclick="saveProductAdditional()"><i class="icon-save me-1"></i> {{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
