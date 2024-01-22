<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('short_term_rentals.branch_detail'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="form-group row mb-3">
            <div class="col-sm-3">
                <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list"
                    :label="__('short_term_rentals.branch')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-9">
                <x-forms.select-option id="product_id_filter" :value="$d->product_id" :list="[]"
                    :label="__('short_term_rentals.package')" :optionals="[
                        'ajax' => true,
                        'required' => true,
                        'default_option_label' => $product_name ?? null,
                    ]"/>
            </div>
        </div>
        <div id="block-product" class="form-group row" >
            <div class="d-flex justify-content-between" >
                <p class="m-0 pt-2 mb-2" >รายการแพ็กเกจ</p>
            </div>
            @if($product)
            <div id="carousel-products" class="carousel slide" data-ride="carousel" data-interval="0" >
                @include('admin.short-term-rental-info.components.products-carousel-item', [
                    'products' => [[$product]]
                ])
            </div>
            @endif
        </div>
        <x-forms.hidden id="product_id_selected" :value="$d->product_id"/>
    </div>
</div>
