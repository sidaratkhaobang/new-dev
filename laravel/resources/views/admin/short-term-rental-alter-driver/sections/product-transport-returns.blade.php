<h4>{{ __('short_term_rentals.product_return') }}</h4>
<hr>
<div class="mb-5" id="product-transport-returns" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <div class="row push mb-4">
            <template v-if="product_transport_return_list.length > 0">
                <template v-for="(item, index) in product_transport_return_list">
                    <div class="col-sm-4">
                        <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                            <div
                                class="block-content block-content-full d-flex justify-content-around block-car-content">
                                <div class="item item-block">
                                    <span class="fs-base mb-0 text-dark">
                                        @{{ item.brand_name_return }}
                                    </span>
                                    <span class="text-muted">
                                        @{{ item.class_name_return }}
                                    </span>
                                    <template v-if="item.product_files_return.length > 0">
                                        <img class="img-block img-fluid img-car" :src="item.product_files_return[0].url"
                                            alt="">
                                    </template>
                                    <template v-else>
                                        <img class="img-block img-fluid img-car"
                                            src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                                    </template>
                                    {{-- @if ($car->image && sizeof($car->image) > 0)
                            <img class="img-block img-fluid" src="{{ $car->image[0]['url'] }}"
                                alt="">
                        @else
                            <img class="img-block img-fluid"
                                src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                        @endif --}}
                                    {{-- <template v-if="item.product_files_return.length > 0">
                                        <img class="img-block img-fluid img-car" :src="item.product_files_return[0].url"
                                            alt="">
                                    </template>
                                    <template v-else>
                                        <img class="img-block img-fluid img-car"
                                            src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                                    </template> --}}

                                    {{-- <template>
                                        <img class="img-block img-fluid img-car"
                                            src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                                    </template>  --}}
                                </div>
                                <div class="ps-3 text-start text-block">
                                    <p class="fs-lg fw-semibold mb-2 text-primary">
                                        @{{ item.license_plate_return }}
                                    </p>
                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fa fa-palette"></i>
                                        @{{ item.color_name_return}}
                                    </p>

                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fa-sharp fa-solid fa-car-battery"></i>
                                        @{{ item.engine_return}}
                                        
                                    </p>

                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fas fa-oil-can"></i>
                                        @{{ item.chassis_return}}
                                        
                                    </p>
                                    {{-- <p class="fs-sm text-muted mb-0">
                                        <i class="far fa-clock pe-1"></i>
                                        <span class="pe-3">รับ
                                            {{ get_date_time_by_format($rental->pickup_date, 'H:i') }} น.</span>
                                        <i class="far fa-clock pe-1"></i>
                                        <span>คืน {{ get_date_time_by_format($rental->return_date, 'H:i') }} น.</span>
                                    </p> --}}
                                    @if(!isset($view))
                                    <button type="button" class="btn btn-sm rounded-pill btn-primary btn-ssm mt-2"
                                        v-on:click="edit(index)">
                                        <template><i class="far fa-edit m-sm-1"></i></template>
                                        @endif
                                </div>
                                @if(!isset($view))
                                <a class="btn-delete-row" href="javascript:void(0)" v-on:click="remove(index)"><i
                                        class="fa-solid fa-x me-1" style="color: #E04F1A"></i></a>
                                        @endif
                            </div>

                        </div>
                    </div>
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][brand_name]'" v-bind:value="item.brand_name_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][class_name]'" v-bind:value="item.class_name_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][license_plate]'" v-bind:value="item.license_plate_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][color_name]'" v-bind:value="item.color_name_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][engine]'" v-bind:value="item.engine_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][chassis]'" v-bind:value="item.chassis_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][remark]'" v-bind:value="item.remark_return">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][transfer_type]'" v-bind:value="1">
                    <input type="hidden" v-bind:name="'product_transport_return['+ index+ '][id]'" v-bind:value="item.id">
                </template>
            </template>
        </div>

        <div class="row">
            <div class="col-md-12 text-end">
                @if(!isset($view))
                <button type="button" class="btn btn-primary" onclick="addProductReturn()">{{ __('lang.add') }}</button>
                @endif
            </div>
        </div>
        @include('admin.short-term-rental-driver.modals.product-transport-return')
    </div>
</div>
