<x-blocks.block title="ข้อมูลออฟชั่นเสริม" >
    <div id="car-options" >
        <div v-if="car_data.length > 0">
            <template v-for="(value,key) in car_data">
                <div class="row gx-0 mb-2" >
                    <div class="col-12 d-flex pt-2 ps-2 pb-2 car-header">
                        <div class="d-flex justify-content-start align-items-center flex-grow-1 h-100">
                            <img class="img-block car-image" :src="value.image_url" alt="">
                            <div class="ms-3 me-3 d-block ">
                                <p class="car-class-text mb-0">
                                    @{{ value.class_full_name ?? "" }}
                                </p>
                                <p class="car-name-text mb-0">
                                    @{{ value.license_plate ?? "-" }}
                                </p>
                            </div>
                        </div>
                        <div style="position: sticky;left: 100%;"
                            class="d-flex justify-content-center align-items-center">
                            <div class="block-options-item ms-2 ">
                                <button type="button" class="btn btn-primary me-3"
                                    v-on:click="add(key)">
                                    <i class="icon-add-circle"></i> เพิ่ม
                                </button>
                                <a class="btn-toggle-car" data-bs-toggle="collapse"
                                    :href="'#car-' + key" role="button" aria-expanded="false"
                                    aria-controls="collapseExample">
                                    <i class=" me-2 icon-arrow-down"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="collapse ps-0 pe-0 show" :id="'car-' + key">
                        <div class="table-wrap db-scroll" style="border-radius:0px ">
                            <table class="table table-striped table-vcenter">
                                <thead style="background: var(--neutral-bg-03, #E2E8F0);border-radius: 0px;">
                                    <th style="width: 1px; border-top-left-radius: 0;">#</th>
                                    <th style="width: 40%;" >ชื่อออฟชั่นเสริม</th>
                                    <th style="width: 20%;" class="text-end" >จำนวน</th>
                                    <th style="width: 20%;" class="text-end" >ราคาต่อหน่วย</th>
                                    <th style="width: 20%;" class="text-end" >รวม</th>
                                    <th style="min-width: 56px; border-top-right-radius: 0;" ></th>
                                </thead>
                                <tbody>

                                <template v-if="value.product_additionals.length > 0">
                                    <tr v-for="(value_product_additionals,key_product_additionals) in value.product_additionals">
                                        <input type="hidden"
                                            v-bind:name="'product_additionals[' + key + key_product_additionals +'][rental_line_id]'"
                                            id=""
                                            v-bind:value="value_product_additionals.rental_line_id">
                                        <input type="hidden"
                                            v-bind:name="'product_additionals[' + key + key_product_additionals +'][car_id]'"
                                            id="_product_additional_item_car_id"
                                            v-bind:value="value.car_id">
                                        <td>
                                            @{{ key_product_additionals+1 }}
                                        </td>
                                        <template v-if="value_product_additionals.is_from_product == 0" >
                                            <td>
                                                <select-2-ajax :id="'option_' +key+'_'+ key_product_additionals" 
                                                    v-bind:name="'product_additionals[' + key + key_product_additionals +'][product_additional_id]'"
                                                    class="form-control list_in" style="width: 100%;"
                                                    v-model="value_product_additionals.product_additional_id" 
                                                    :defaultname="value_product_additionals.product_additional_name" 
                                                    @input="handleSelectChange(key,key_product_additionals,value_product_additionals.product_additional_id)"
                                                    :data-index="key_product_additionals"
                                                    url="{{ route('admin.util.select2.product-additionals') }}" >
                                                </select-2-ajax>
                                            </td>
                                            <td class="text-end" >
                                                <input type="number" class="number-format form-control text-end input-total" min="0" 
                                                    :data-index_car="key"
                                                    :data-index_product="key_product_additionals"
                                                    v-bind:name="'product_additionals[' + key + key_product_additionals +'][amount]'"
                                                    v-model="value_product_additionals.amount"
                                                    @change="setTotal(key,key_product_additionals,value_product_additionals.amount)">
                                            </td>
                                            <td class="text-end" >
                                                @{{ number_format(value_product_additionals.price) }}
                                            </td>
                                            <td class="text-end" >
                                                @{{ number_format(value_product_additionals.subtotal) }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                                    <button type="button" href="javascript:void(0)" @click="remove(key,key_product_additionals)"
                                                        class="border-0 bg-transparent"><i
                                                        class="fa-solid fa-trash-can pe-none"
                                                        style="color: red;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </template>
                                        <template v-else>
                                            <template>
                                            <input type="hidden"
                                                v-bind:name="'product_additionals[' + key + key_product_additionals +'][product_additional_id]'"
                                                v-bind:value="value_product_additionals.product_additional_id" />
                                            <input type="hidden"
                                                v-bind:name="'product_additionals[' + key + key_product_additionals +'][amount]'"
                                                v-bind:value="value_product_additionals.amount" />
                                            </template>

                                            <td>@{{ value_product_additionals.product_additional_name }}</td>
                                            <td class="text-end" >@{{ number_format(value_product_additionals.amount) }}</td>
                                            <td class="text-end" >@{{ number_format(value_product_additionals.price) }}</td>
                                            <td class="text-end" >@{{ number_format(value_product_additionals.subtotal) }}</td>
                                            <td>&nbsp;</td>
                                        </template>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="12" class="text-center">" ไม่มีรายการ "</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
            <template v-for="(value,key) in del_input_id" >
                <input type="hidden" 
                    v-bind:name="'product_additionals_del[' + key +']'"
                    v-bind:value="value" >
            </template>
        </div>
    </div>
</x-blocks.block>