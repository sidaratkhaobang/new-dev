<div class="mt-2 mb-2">
    <template v-if="selected_type == 'car'">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>
                        #
                    </th>
                    <th>
                        ยี่ห้อ
                    </th>
                    <th>
                        รุ่น
                    </th>
                    <th>
                        สี
                    </th>
                    <th>
                        เลขทะเบียน
                    </th>
                    <th>
                        เลขตัวถัง
                    </th>
                    <th>
                        น้ำหนัก (กิโลกรัม)
                    </th>
                    <th>
                        รูปภาพ
                    </th>
                    <th>

                    </th>
                </thead>
                <thead v-if="product_transport_modal_return_list.length > 0">
                    <tr v-for="(value,index) in product_transport_modal_return_list">
                        <td class="align-middle">
                            <input type="hidden" :id="index+ 'index'" :value="value.index">
                            <input type="hidden">
                            <input type="text" v-model="value.brand_name" :id="index+ 'brand_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.class_name" :id="index+ 'class_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.license_plate" :id="index+ 'license_plate'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.color_name" :id="index+ 'color_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.engine" :id="index+ 'engine'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.chassis" :id="index+ 'chassis'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input-number-format-vue type="text" v-model="value.weight_m" :id="index+ 'weight_m'" class="form-control" />
                        </td>
                        <td class="align-middle">
                            <drop-zone-vue :id="'product-img-'+index" :file="value.product_files_return">
                            </drop-zone-vue>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                <button class="border-0 bg-transparent" type="button" @click="removeDataTable(index,value.index)">
                                    <i class="fa-solid fa-trash-can pe-none" style="color: red;">

                                    </i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </template>
    <template v-if="selected_type == 'broken-car'">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>
                        #
                    </th>
                    <th>
                        ยี่ห้อ
                    </th>
                    <th>
                        รุ่น
                    </th>
                    <th>
                        สี
                    </th>
                    <th>
                        เลขทะเบียน
                    </th>
                    <th>
                        เลขตัวถัง
                    </th>
                    <th>
                        น้ำหนัก (กิโลกรัม)
                    </th>
                    <th>
                        รูปภาพ
                    </th>
                    <th>

                    </th>
                </thead>
                <thead v-if="product_transport_modal_return_list.length > 0">
                    <tr v-for="(value,index) in product_transport_modal_return_list">
                        <td class="align-middle">
                            <input type="hidden" :id="index+ 'index'" :value="value.index">
                            <input type="hidden">
                            <input type="text" v-model="value.brand_name" :id="index+ 'brand_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.class_name" :id="index+ 'class_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.license_plate" :id="index+ 'license_plate'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.color_name" :id="index+ 'color_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.engine" :id="index+ 'engine'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.chassis" :id="index+ 'chassis'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input-number-format-vue type="text" v-model="value.weight_m" :id="index+ 'weight_m'" class="form-control" />
                        </td>
                        <td class="align-middle">
                            <drop-zone-vue :id="'product-img-'+index" :file="value.product_files_return">
                            </drop-zone-vue>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                <button class="border-0 bg-transparent" type="button" @click="removeDataTable(index,value.index)">
                                    <i class="fa-solid fa-trash-can pe-none" style="color: red;">

                                    </i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </template>
    <template v-if="selected_type == 'big-bike'">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>
                        #
                    </th>
                    <th>
                        ยี่ห้อ
                    </th>
                    <th>
                        รุ่น
                    </th>
                    <th>
                        สี
                    </th>
                    <th>
                        เลขทะเบียน
                    </th>
                    <th>
                        เลขตัวถัง
                    </th>
                    <th>
                        น้ำหนัก (กิโลกรัม)
                    </th>
                    <th>
                        รูปภาพ
                    </th>
                    <th>

                    </th>
                </thead>
                <thead v-if="product_transport_modal_return_list.length > 0">
                    <tr v-for="(value,index) in product_transport_modal_return_list">
                        <td class="align-middle">
                            <input type="text" v-model="value.brand_name" :id="index+ 'brand_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.class_name" :id="index+ 'class_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.license_plate" :id="index+ 'license_plate'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.color_name" :id="index+ 'color_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.engine" :id="index+ 'engine'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.chassis" :id="index+ 'chassis'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input-number-format-vue type="text" v-model="value.weight_m" :id="index+ 'weight_m'" class="form-control" />
                        </td>
                        <td class="align-middle">
                            <drop-zone-vue :id="'product-img-'+index" :file="value.product_files_return">
                            </drop-zone-vue>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                <button class="border-0 bg-transparent" type="button" @click="removeDataTable(index,value.index)">
                                    <i class="fa-solid fa-trash-can pe-none" style="color: red;">

                                    </i>
                                </button>
                            </div>

                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </template>
    <template v-if="selected_type == 'industrial-products'">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>
                        #
                    </th>
                    <th>
                        ความกว้าง (เมตร)
                    </th>
                    <th>
                        ความยาว (เมตร)
                    </th>
                    <th>
                        ความสูง (เมตร)
                    </th>
                    <th>
                        น้ำหนัก (กิโลกรัม)
                    </th>
                    <th>
                        ยี่ห้อ
                    </th>
                    <th>
                        รุ่น
                    </th>
                    <th>
                        รูปภาพ
                    </th>
                    <th>

                    </th>
                </thead>
                <thead v-if="product_transport_modal_return_list.length > 0">
                    <tr v-for="(value,index) in product_transport_modal_return_list">
                        <td class="align-middle">
                            <input type="hidden" :id="index+ 'index'" :value="value.index">
                            <input type="hidden">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.width_m" :id="index+ 'width_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.long_m" :id="index+ 'long_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.height_m" :id="index+ 'height_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.weight_m" :id="index+ 'weight_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.brand_name" :id="index+ 'brand_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.class_name" :id="index+ 'class_name'" class="form-control">

                        </td>
                        <td class="align-middle">
                            <drop-zone-vue :id="'product-img-'+index" :file="value.product_files_return">
                            </drop-zone-vue>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                <button class="border-0 bg-transparent" type="button" @click="removeDataTable(index,value.index)">
                                    <i class="fa-solid fa-trash-can pe-none" style="color: red;">

                                    </i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </template>
    <template v-if="selected_type == 'product-more'">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <th>
                        #
                    </th>
                    <th>
                        ความกว้าง (เมตร)
                    </th>
                    <th>
                        ความยาว (เมตร)
                    </th>
                    <th>
                        ความสูง (เมตร)
                    </th>
                    <th>
                        น้ำหนัก (กิโลกรัม)
                    </th>
                    <th>
                        ยี่ห้อ
                    </th>
                    <th>
                        รุ่น
                    </th>
                    <th>
                        รูปภาพ
                    </th>
                    <th>

                    </th>
                </thead>
                <thead v-if="product_transport_modal_return_list.length > 0">
                    <tr v-for="(value,index) in product_transport_modal_return_list">
                        <td class="align-middle">
                            <input type="hidden" :id="index+ 'index'" :value="value.index">
                            <input type="hidden">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.width_m" :id="index+ 'width_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.long_m" :id="index+ 'long_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.height_m" :id="index+ 'height_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.weight_m" :id="index+ 'weight_m'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.brand_name" :id="index+ 'brand_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <input type="text" v-model="value.class_name" :id="index+ 'class_name'" class="form-control">
                        </td>
                        <td class="align-middle">
                            <drop-zone-vue :id="'product-img-'+index" :file="value.product_files_return">
                            </drop-zone-vue>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex justify-content-center align-items-center" style="height: 40px;">
                                <button class="border-0 bg-transparent" type="button" @click="removeDataTable(index,value.index)">
                                    <i class="fa-solid fa-trash-can pe-none" style="color: red;">

                                    </i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </template>
</div>