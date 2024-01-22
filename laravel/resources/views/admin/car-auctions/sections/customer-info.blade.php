<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_customer'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_name" :value="$d->customer" :label="__('car_auctions.customer_name')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-9">
                <x-forms.input-new-line id="customer_address" :value="$d->address" :label="__('car_auctions.customer_address')" :optionals="['required' => true]" />
            </div>
        </div>
        @if (in_array($d->status, [CarAuctionStatusEnum::SOLD_OUT]))
            <div class="row">
                <div class="col-sm-3">
                    <x-forms.upload-image :id="'document_sale'" :label="__('car_auctions.document_sale')" />
                </div>
            </div>
        @endif
    </div>
</div>
