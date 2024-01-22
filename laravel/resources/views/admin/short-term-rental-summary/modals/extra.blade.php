<x-modal :id="'extra'" :title="'สินค้าและบริการเพิ่มเติม'">
    <div class="row g-3 mb-3">
        @foreach ($cars as $index => $car)
            <div class="col-sm-12 col-lg-3">
                <x-forms.checkbox-block id="car_modal_extra_id_{{ $index }}" name="car_modal_extra_id"
                    value="{{ $car->car_id }}" selected="{{ $car->car_id }}">
                    <p class="block-title p-0 m-0">{{ $car->class_full_name }}</p>
                    <p class="block-title p-0 m-0">{{ $car->license_plate }}</p>
                    <div class="block-img-wrap">
                        <img src="{{ $car->image_url }}" class="block-img">
                    </div>
                </x-forms.checkbox-block>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-primary mb-3 float-end add-product-btn" onclick="addExtraLine()">
        <i class="icon-add-circle"></i> เพิ่ม
    </button>

    <div class="table-wrap db-scroll col-12 extra" style="border-radius:0px ">
        <table class="table table-striped table-vcenter">
            <thead style="background: var(--neutral-bg-03, #E2E8F0);border-radius: 0px;">
                <th style="width: 1px;">#</th>
                <th style="width: 30%;">ชื่อสินค้าและบริการ</th>
                <th style="width: 20%;">จำนวน</th>
                <th style="width: 25%;">ราคาต่อหน่วย</th>
                <th style="width: 25%;">รวม</th>
                <th style="width: 1px;"></th>
            </thead>
            <tbody>
                <template v-if="modal_extra_product.length > 0">
                    <tr v-for="(extra,extra_index) in modal_extra_product">
                        <td>
                            @{{ extra_index + 1 }}
                        </td>
                        <td>
                            <input type="text" class="form-control" :id="'name-' + extra_index"
                                v-bind:name="'modal_extra_product[' + extra_index +'][name]'" v-model="extra.name" />
                        </td>
                        <td>
                            <input-number-format-vue v-model="extra.amount" :id="'amount-' + extra_index"
                                class="form-control" :value="extra.amountl"
                                :name="'modal_extra_product[' + extra_index + '][amount]'"
                                @input="setTotalExtra(extra_index)" />
                        </td>
                        <td>
                            <input-number-format-vue v-model="extra.unit_price" :id="'unit-price-' + extra_index"
                                class="form-control" :value="extra.unit_price"
                                :name="'modal_extra_product[' + extra_index + '][unit_price]'"
                                @input="setTotalExtra(extra_index)" />
                        </td>
                        <td class="text-end">
                            <span> @{{ extra.subtotal }}</span>
                        </td>
                        <td>
                            <a class="btn btn-outline-light btn-mini"
                                v-on:click="removeExtraLine(extra_index, extra.rental_line_id)"><i
                                    class="fa-solid fa-trash-can" style="color:red"></i></a>
                        </td>

                        <input type="hidden" v-bind:name="'modal_extra_product[' + extra_index +'][rental_line_id]'"
                            v-model="extra.rental_line_id" />
                    </tr>
                </template>
                <template v-else-if="product_add.length == 0">
                    <tr class="table-empty add-product-empty">
                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-secondary btn-clear-search"
            data-bs-dismiss="modal">{{ __('lang.back') }}</button>
        <button type="button" class="btn btn-primary add-extra" onclick="saveExtra()"><i class="icon-save me-1"></i>
            {{ __('lang.save') }}</button>
    </x-slot>
</x-modal>
