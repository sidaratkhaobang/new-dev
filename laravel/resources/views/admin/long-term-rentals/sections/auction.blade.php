<div class="block {{ __('block.styles') }}" id="auction_data_show"
    @if (strcmp($rental_type, AuctionStatusEnum::AUCTION) == 0) style="display: block;" @else style="display: none;" @endif>
    @include('admin.components.block-header', [
        'text' => __('long_term_rentals.auction_table'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-6">
                <x-forms.radio-inline id="won_auction" :value="$d->won_auction" :list="$won_auction_list" :label="__('long_term_rentals.won_auction')" />
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="auction_submit_date" :value="$d->auction_submit_date" :label="__('long_term_rentals.auction_submit_date')"
                            :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="need_pay_auction" :value="$d->need_pay_auction" :list="$need_pay_auction_list"
                            :label="__('long_term_rentals.need_pay_auction')" />
                    </div>
                    <div class="col-sm-3" id="need_auction_file"
                        @if (strcmp($d->need_pay_auction, BOOL_TRUE) == 0) style="display: block;" @else style="display: none;" @endif>
                        @if (isset($view))
                            <x-forms.view-image :id="'payment_form'" :label="__('long_term_rentals.payment_form')" :list="$payment_forms" />
                        @else
                            <x-forms.upload-image :id="'payment_form'" :label="__('long_term_rentals.payment_form')" />
                        @endif
                    </div>
                </div>
                <div id="won_show"
                    @if (in_array($d->won_auction, [AuctionResultEnum::WAITING, AuctionResultEnum::WON])) style="display: block;" @else style="display: none;" @endif>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="auction_winning_date" :value="$d->auction_winning_date" :label="__('long_term_rentals.auction_winning_date')"
                                :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="bidder_price" :value="$d->bidder_price" :label="__('long_term_rentals.bidder_price')"
                                :optionals="['type' => 'number', 'min' => '1', 'oninput' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="bidder_name" :value="$d->bidder_name" :label="__('long_term_rentals.bidder_name')" />
                        </div>
                    </div>
                </div>
                <div id="lose_show"
                    @if (strcmp($d->won_auction, AuctionResultEnum::LOSE) == 0) style="display: block;" @else style="display: none;" @endif>
                    <div class="row push mb-4">
                        <div class="col-sm-3 @if ($d->won_auction == AuctionResultEnum::LOSE && !empty($d->won_auction)) d-none @endif">
                            <x-forms.select-option id="reject_reason_id" :value="$d->reject_reason_id" :list="$auction_reject_list"
                                :label="__('long_term_rentals.reject_reason')" />
                        </div>
                        <div class="col-sm-3 @if ($d->won_auction == AuctionResultEnum::LOSE && !empty($d->won_auction)) d-none @endif">
                            <x-forms.input-new-line id="reject_reason_description" :value="$d->reject_reason_description"
                                :label="__('long_term_rentals.reject_reason_description')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
