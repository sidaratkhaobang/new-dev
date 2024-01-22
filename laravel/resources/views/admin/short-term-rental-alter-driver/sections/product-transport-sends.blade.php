<h4>{{ __('short_term_rentals.product_send') }}</h4>
<hr>
<div class="mb-5" id="product-transports" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <div class="row push mb-4">
            {{-- <template v-if="product_transport_list.length > 0">

                @{{test}}
            </template> --}}
            <template v-if="product_transport_list.length > 0">
                <template v-for="(item, index) in product_transport_list">
                    <div class="col-sm-4">
                        <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                            <div
                                class="block-car block-content block-content-full d-flex justify-content-around block-car-content">
                                <div class="item item-block " >
                                    <span class="fs-base text-dark" >
                                        @{{ item.brand_name }}
                                    </span>
                                    <span class="text-muted">
                                        @{{ item.class_name }}
                                    </span>
                                    <template v-if="item.product_files.length > 0">
                                        <img class="img-block img-fluid img-car" :src="item.product_files[0].url"
                                            alt="">
                                    </template>
                                    <template v-else>
                                        <img class="img-block img-fluid img-car"
                                            src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                                    </template>
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

                                    {{-- <div v-if="item.product_files">
                                        <div v-for="(product_files, index) in item.product_files">
                                            <div v-if="product_files.saved">
                                                <a target="_blank" v-bind:href="product_files.url"><i
                                                        class="fa fa-download text-primary"></i>
                                                        {{ __('lang.view_file') }}</a>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="ps-3 text-start text-block mt-4">
                                    <p class="fs-lg fw-semibold mb-2 text-primary">
                                        @{{ item.license_plate }}
                                    </p>
                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fa fa-palette"></i>
                                        @{{ item.color_name}}
                                        
                                    </p>
                                    
                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fa-sharp fa-solid fa-car-battery"></i>
                                        @{{ item.engine}}
                                        
                                    </p>

                                    <p class="fs-sm text-muted mb-0">
                                        <i class="fas fa-oil-can"></i>
                                        @{{ item.chassis}}
                                        
                                    </p>
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
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][brand_name]'" v-bind:value="item.brand_name">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][class_name]'" v-bind:value="item.class_name">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][license_plate]'" v-bind:value="item.license_plate">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][color_name]'" v-bind:value="item.color_name">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][engine]'" v-bind:value="item.engine">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][chassis]'" v-bind:value="item.chassis">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][remark]'" v-bind:value="item.remark">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][transfer_type]'" v-bind:value="2">
                    <input type="hidden" v-bind:name="'product_transport['+ index+ '][id]'" v-bind:value="item.id">
                </template>
            </template>
        </div>

        <div class="row">
            <div class="col-md-12 text-end">
                @if(!isset($view))
                <button type="button" class="btn btn-primary" onclick="addProduct()">{{ __('lang.add') }}</button>
                @endif
            </div>
        </div>
        @include('admin.short-term-rental-driver.modals.product-transport-send')
    </div>
</div>
