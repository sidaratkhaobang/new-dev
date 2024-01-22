<div class="row items-push">
    <div class="col-sm-8">
        <div class="btn-group" role="group">
            @can(Actions::View . '_' . Resources::SellingCar)
            <a type="button" href="{{ route('admin.selling-cars.index') }}"
                class="btn btn-outline-primary btn-group
                    {{ in_array(Route::currentRouteName(), ['admin.selling-cars.index']) ? 'active' : '' }}">
                {{ __('selling_prices.car_sale') }}
            </a>
            @endcan
            @can(Actions::View . '_' . Resources::SellingPrice)
                <a type="button" href="{{ route('admin.selling-prices.index') }}"
                    class="btn btn-outline-primary btn-group
                    {{ in_array(Route::currentRouteName(), ['admin.selling-prices.index']) ? 'active' : '' }}">
                    {{ __('selling_prices.sale_price') }}
                </a>
            @endcan
        </div>
    </div>
</div>
