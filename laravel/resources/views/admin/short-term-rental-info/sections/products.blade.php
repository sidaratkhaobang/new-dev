<h4>{{ __('short_term_rentals.product_detail') }}</h4>
<hr>
<div class="mb-4" id="products" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('products.name') }}</th>
                    <th>{{ __('products.price') }}</th>
                    <th class="sticky-col text-center bg-body-light">{{ __('lang.tools') }}</th>
                </tr>
              </thead>
            <tbody v-if="product_list.length > 0">
                <tr v-for="(item, index) in product_list">
                   <td>@{{ index + 1}}</td>
                   <td>@{{ item.name }}</td>
                   <td>@{{ item.price_format }}</td>
                   <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action-vue')
                    </td>
                    <input type="hidden" v-bind:name="'products['+ index + '][id]'"
                        id="product_item_id" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'products['+ index + '][price]'"
                        id="product_item_price" v-bind:value="item.price">
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="4">" {{ __('lang.no_data') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openProductModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
</div>
@include('admin.short-term-rental-info.modals.products')
