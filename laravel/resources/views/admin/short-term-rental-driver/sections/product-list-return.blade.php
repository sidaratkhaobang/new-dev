<template v-if="item.type == 'industrial-products' || item.type == 'product-more' ">
    <div class="col-sm-6 col-lg-4 border-product ms-2 me-2 mt-2 mb-2" style="padding: 24px;">
        <div class="d-flex">
            <div class="me-2 d-flex justify-content-center align-items-center">
                <template v-if="item.product_files_return.length > 0">
                    <img class="img-block   img-car-send" :src="item.product_files_return[0].url" alt="">
                </template>
                <template v-else>
                    <img class="img-block   img-car-send" src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                </template>
            </div>
            <div class="ms-2 me-2 d-flex justify-content-center align-items-center">
                สินค้าชิ้นที่ @{{index+1}}
            </div>
            <div class="d-flex align-items-center" style="position: sticky;left: 100%">
                <div class="btn-group">
                    <div class="col-sm-12">
                        <div class="dropdown dropleft">
                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                <a class="dropdown-item" v-on:click="edit(index)" href="javascript:void(0)"><i class="far fa-edit me-1"></i>
                                    แก้ไข</a>
                                <a class="dropdown-item btn-delete-row" href="javascript:void(0)" v-on:click="remove(index)"><i class="fa fa-trash-alt me-1"></i> ลบ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ประเภทสินค้า</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.product_type }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ความกว้าง (เมตร)</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.width_m }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ความยาว (เมตร)</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.long_m }}</b>
            </div>

        </div>
        <div class="row push mb-4">
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ความสูง (เมตร)</label>
                <b style="font-weight: bold;" class="text_class d-block"> @{{ item.height_m }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">น้ำหนัก (กิโลกรัม)</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ Number(item.weight_m.replace(/,/g,
                    '')).toLocaleString('en-US') }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">รุ่น</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.class_name }}</b>
            </div>

        </div>
        <div class="row push mb-4">
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ยี่ห้อ</label>
                <b style="font-weight: bold;" class="text_class d-block"> @{{ item.brand_name }}</b>
            </div>
        </div>
    </div>
</template>
<template v-else>
    <div class="col-sm-6 col-lg-4 border-product ms-2 me-2 mt-2 mb-2" style="padding: 24px;">
        <div class="d-flex">
            <div class="me-2 d-flex justify-content-center align-items-center">
                <template v-if="item.product_files_return.length > 0">
                    <img class="img-block   img-car-send" :src="item.product_files_return[0].url" alt="">
                </template>
                <template v-else>
                    <img class="img-block   img-car-send" src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                </template>
            </div>
            <div class="ms-2 me-2 d-flex justify-content-center align-items-center">
                สินค้าชิ้นที่ @{{index+1}}
            </div>
            <div class="d-flex align-items-center" style="position: sticky;left: 100%">
                <div class="btn-group">
                    <div class="col-sm-12">
                        <div class="dropdown dropleft">
                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                <a class="dropdown-item" v-on:click="edit(index)" href="javascript:void(0)"><i class="far fa-edit me-1"></i>
                                    แก้ไข</a>
                                <a class="dropdown-item btn-delete-row" href="javascript:void(0)" v-on:click="remove(index)"><i class="fa fa-trash-alt me-1"></i> ลบ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ประเภทสินค้า</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.product_type }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">สี</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.color_name }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">เลขตัวถัง</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.chassis }}</b>
            </div>

        </div>
        <div class="row push mb-4">
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">ยี่ห้อ</label>
                <b style="font-weight: bold;" class="text_class d-block"> @{{ item.brand_name }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">เลขทะเบียน</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.license_plate }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">รุ่น</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ item.class_name }}</b>
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="" class="">น้ำหนัก (กิโลกรัม)</label>
                <b style="font-weight: bold;" class="text_class d-block">@{{ Number(item.weight_m.replace(/,/g,
                    '')).toLocaleString('en-US') }}</b>
            </div>
        </div>
    </div>
</template>