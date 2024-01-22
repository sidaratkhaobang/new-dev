<div class="mb-5" id="product-additionals" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th>#</th>
            <th>{{ __('product_additionals.name') }}</th>
            <th>{{ __('products.price') }}</th>
            <th>{{ __('products.amount') }}</th>
            <th class="text-center">{{ __('products.free') }}</th>
            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="product_additional_list.length > 0">
            <tr v-for="(item, index) in product_additional_list">
                <td>@{{ index + 1 }}</td>
                <td>@{{ item.product_additional_text }}</td>
                <td>@{{ Number(item.price).toLocaleString() }}</td>
                <td>@{{ item.amount }}</td>
                <td class="text-center">
                    <i :class="{ 'fa fa-circle-check text-primary': item.is_free == 1, 'fa fa-circle-xmark text-secondary': item.is_free == 0 }"></i>
                </td>
                <td class="sticky-col text-center">
                    @include('admin.components.dropdown-action-vue')
                </td>
                <input type="hidden" v-bind:name="'product_additionals['+ index+ '][product_additional_id]'"
                       id="product_additional_id" v-bind:value="item.product_additional_id">
                <input type="hidden" v-bind:name="'product_additionals['+ index+ '][price]'" id="price"
                       v-bind:value="item.price">
                <input type="hidden" v-bind:name="'product_additionals['+ index+ '][amount]'" id="amount"
                       v-bind:value="item.amount">
                <input type="hidden" v-bind:name="'product_additionals['+ index+ '][is_free]'" id="is_free"
                       v-bind:value="item.is_free">
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary"
                    onclick="openProductAdditionalModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @include('admin.products.modals.product-additional')
</div>
