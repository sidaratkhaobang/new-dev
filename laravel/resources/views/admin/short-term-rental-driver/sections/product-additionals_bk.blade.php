<h4>{{ __('short_term_rentals.product_additional_detail') }}</h4>
<hr>
<div class="mb-5" id="product-additionals" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('product_additionals.name') }}</th>
                    <th>{{ __('products.amount') }}</th>
                    <th class="text-end">{{ __('products.price') }}</th>
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                </tr>
              </thead>
            <tbody v-if="product_additional_list.length > 0">
                <tr v-for="(item, index) in product_additional_list">
                   <td>@{{ index + 1}}</td>
                   <td>@{{ item.name }}  สำหรับ @{{ item.car_name }}</td>
                   <td>@{{ item.amount }}</td>
                   <td class="text-end">@{{ item.price_format }}</td>
                   <td class="sticky-col text-center">
                       <template v-if="!item.is_from_product && !item.is_from_promotion" >
                            @include('admin.components.dropdown-action-vue')
                        </template>
                    </td>
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][product_additional_id]'"
                        id="_product_additional_item_id" v-bind:value="item.product_additional_id">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][name]'"
                        id="_product_additional_item_name" v-bind:value="item.name">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][amount]'"
                        id="_product_additional_item_amount" v-bind:value="item.amount">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][car_id]'"
                        id="_product_additional_item_car_id" v-bind:value="item.car_id">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][price]'"
                        id="_product_additional_item_price" v-bind:value="item.price">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][is_free]'"
                        id="_product_additional_item_is_free" v-bind:value="item.is_free">
                    <input type="hidden" v-bind:name="'product_additionals['+ index +'][is_from_product]'"
                        id="_product_additional_item_is_from_product" v-bind:value="item.is_from_product">
                        <input type="hidden" v-bind:name="'product_additionals['+ index +'][is_from_promotion]'"
                        id="_product_additional_item_is_from_promotion" v-bind:value="item.is_from_promotion">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="5">" {{ __('lang.no_data') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openProductAdditionalModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
@include('admin.short-term-rental-driver.modals.product-additionals')
