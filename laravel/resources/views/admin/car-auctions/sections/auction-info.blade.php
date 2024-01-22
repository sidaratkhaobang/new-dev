<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('car_auctions.title_auction'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row">
            <div class="col-sm-3">
                <x-forms.date-input id="send_auction_date" :value="$d->send_auction_date" :label="__('car_auctions.send_auction_date')" :optionals="['date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="auction_date_show" :value="$d->auction_date" :label="__('car_auctions.auction_date')" :optionals="['date_enable_time' => true, 'required' => true]" />
                <x-forms.hidden id="auction_date" :value="$d->auction_date" />
            </div>
            @if (in_array($d->status, [
                    CarAuctionStatusEnum::SEND_AUCTION,
                    CarAuctionStatusEnum::PENDING_AUCTION,
                    CarAuctionStatusEnum::SOLD_OUT,
                ]))
                <div class="col-sm-3">
                    <x-forms.date-input id="sale_date_show" :value="$d->sale_date" :label="__('car_auctions.sale_date')" :optionals="['date_enable_time' => true, 'required' => true]" />
                    <x-forms.hidden id="sale_date" :value="$d->sale_date" />
                </div>
            @endif
            <div class="col-sm-3">
                <x-forms.select-option :value="$d->auction_id" id="auction_place" :list="null" :label="__('car_auctions.auction_place')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $auction,
                    ]" />
            </div>
        </div>
    </div>
</div>
