<x-blocks.block :title="__('short_term_rentals.product_send')" :optionals="['is_toggle' => false]">
    <x-slot name="options">
        <button type="button" class="btn btn-primary" onclick="addProduct()"><i
                class="icon-add-circle"></i>{{ __('lang.add') }}</button>
    </x-slot>
    <div id="car-options">
        <div id="product-transports" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap" style="border: none;overflow-x: hidden;">
                <div class="row push mb-4 " style="padding-left: 16px;padding-right: 16px;">
                    <template v-if="product_transport_list.length > 0">
                        <template v-for="(item, index) in product_transport_list">
                            @include('admin.short-term-rental-driver.sections.product-list')
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][brand_name]'"
                                   v-bind:value="item.brand_name">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][class_name]'"
                                   v-bind:value="item.class_name">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][license_plate]'"
                                   v-bind:value="item.license_plate">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][color_name]'"
                                   v-bind:value="item.color_name">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][engine]'"
                                   v-bind:value="item.engine">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][chassis]'"
                                   v-bind:value="item.chassis">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][remark]'" value="">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][transfer_type]'"
                                   v-bind:value="2">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][id]'"
                                   v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][product_type]'"
                                   v-bind:value="item.product_type">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][width]'"
                                   v-bind:value="item.width_m">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][long]'"
                                   v-bind:value="item.long_m">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][height]'"
                                   v-bind:value="item.height_m">
                            <input type="hidden" v-bind:name="'product_transport['+ index+ '][weight]'"
                                   v-bind:value="item.weight_m">
                        </template>
                    </template>
                </div>
                @include('admin.short-term-rental-driver.modals.product-transport-send')
            </div>
        </div>
    </div>
</x-blocks.block>
