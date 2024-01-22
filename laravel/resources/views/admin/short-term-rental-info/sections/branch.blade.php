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
                <p class="m-0 pt-2" >รายการแพ็กเกจ</p>

                <div class="d-flex flex-row mb-3" style="cursor: pointer;">
                    <div id="to_left" class="svg-container" data-interval="false"
                         data-bs-target="#carousel-products" data-bs-slide="prev">
                        <img src="{{ asset('images/btn_arrow_left.png') }}" >
                    </div>
                    <div id="to_right" class="svg-container ms-3" data-interval="false"
                         data-bs-target="#carousel-products" data-bs-slide="next">
                         <img src="{{ asset('images/btn_arrow_right.png') }}" >
                    </div>
                </div>
            </div>
            <div id="carousel-products" class="carousel slide" data-ride="carousel" data-interval="0" >
                <div class="carousel-inner"></div>
            </div>
        </div>
        <x-forms.hidden id="product_id_selected" :value="$d->product_id"/>
    </div>
</div>
