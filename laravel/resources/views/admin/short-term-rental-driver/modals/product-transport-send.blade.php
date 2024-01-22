<x-modal :id="'product-transport'" :title="'เพิ่มสินค้านำส่ง'" :icon="'icon-add-circle'">
    <div class="row push">
        <div class="col-lg-12 col-sm-12">
            <p>รถที่เลือก</p>
            <template v-if="car_data.length > 0">
                <div class="row push border-car-modal-send " v-for="(value,key) in car_data">
                    <div class="col-sm-1 col-lg-1 d-flex justify-content-center align-items-center">
                        <img v-if="value.image_url != null " class="img-block"
                             style="max-width: 73px;width: 100%;max-height: 50px;height: 100%" :src="value.image_url"
                             alt="">
                        <img v-else class="img-block " style="max-width: 73px;width: 100%;max-height: 50px;height: 100%"
                             src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                    </div>
                    <div class="col-sm-11 col-lg-11">
                        <span class="d-block text-start">
                            @{{ value.class_full_name ?? "" }}
                        </span>
                        <span class="d-block text-start">
                            @{{ value.class_name ?? "" }}
                        </span>
                        <span class="d-block text-start">
                            @{{ value.license_plate ?? "-" }}
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-12 col-lg-12">
            <div class="d-flex justify-content-between">
                <p class="m-0 pt-2">เลือกประเภทสินค้า</p>
                <div class="d-flex flex-row mb-3" style="cursor: pointer;">
                    <div id="to_left" class="svg-container" data-interval="false" data-bs-target="#transport-carousel"
                         data-bs-slide="prev">
                        <img src="{{ asset('images/btn_arrow_left.png') }}">
                    </div>
                    <div id="to_right" class="svg-container ms-3" data-interval="false"
                         data-bs-target="#transport-carousel" data-bs-slide="next">
                        <img src="{{ asset('images/btn_arrow_right.png') }}">
                    </div>
                </div>
            </div>
            <div id="transport-carousel" class="carousel carousel-dark slide" data-bs-touch="false"
                 data-bs-interval="false">
                <div class="carousel-inner">
                    @include('admin.short-term-rental-driver.components.product-transports-send-carousel-item')
                </div>
            </div>
        </div>
    </div>
    <div class="row push">
        <div class="col-lg-12 d-flex align-items-center">
            <span>เพิ่มสินค้านำส่ง</span>
            <div class="position-sticky " style="left: 100%;">
                <button type="button" :class="{ 'd-none': mode === 'edit' }" class="btn btn-primary"
                        @click="addDataTable">
                    เพิ่ม
                </button>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12">
            @include('admin.short-term-rental-driver.modals.product-type')
        </div>
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-secondary btn-custom-size btn-clear-search"
                data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="saveProduct()">{{ __('lang.save') }}</button>
    </x-slot>
</x-modal>
