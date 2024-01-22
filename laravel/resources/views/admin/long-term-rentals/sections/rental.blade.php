<label class="text-start col-form-label" for="lt_rental_type_id">{{ __('long_term_rentals.job_type') }}</label>
<div class="row push">
    @foreach ($lt_rental_type_list as $index => $lt_rental_type)
        <div class="col-sm-12 col-lg-3">
            <div class="{{ $d->lt_rental_type_id == $lt_rental_type->id ? 'btn-active' : 'btn-img' }} btn-type btn-{{$lt_rental_type->id}}"
                dusk="btn-{{ $index }}"
                @if (!isset($view)) onclick="getRentalType('{{ $lt_rental_type->id }}')" @endif>
                <div class="row">
                    <div class="col-8 text-start">
                        <p class="container text-start">{{ $lt_rental_type->name }}</p>
                    </div>
                    <div class="col-4 text-end">
                        <div class="text-end radio-center">
                            <input class="form-check-input radio-type btn-radio-{{$lt_rental_type->id}}" type="radio" id="lt_rental_type_id"
                                name="lt_rental_type_id" value="{{ $lt_rental_type->id }}"
                                {{ $d->lt_rental_type_id == $lt_rental_type->id ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="block {{ __('block.styles') }}" id="auction_show"
    @if (in_array($rental_type, [AuctionStatusEnum::NO_AUCTION, AuctionStatusEnum::AUCTION])) style="display: block;" @else style="display: none;" @endif>
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <label class="text-start col-form-label" for="month">
                    {{ __('long_term_rentals.rental_duration') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="month" id="month" class="form-control col-sm-4"
                    value="{{ $month }}" data-role="tagsinput" />
            </div>
            <div class="col-sm-3">
                @if (strcmp($d->status, LongTermRentalStatusEnum::NEW) === 0 || $d->status == null)
                    <x-forms.select-option id="approval_type" :value="$d->approval_type" :list="$approval_type_list" :label="__('long_term_rentals.type')"
                        :optionals="['required' => true]" />
                @else
                    <x-forms.select-option id="approval_type_hidden" :value="$d->approval_type" :list="$approval_type_list"
                        :label="__('long_term_rentals.type')" :optionals="['required' => true]" />
                    <x-forms.hidden id="approval_type" :value="$d->approval_type" />
                @endif
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="offer_date" :value="$d->offer_date" :label="__('long_term_rentals.offer_date')" :optionals="['placeholder' => __('lang.select_date')]" />
            </div>

            <div class="col-sm-3">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('long_term_rentals.remark')" />
            </div>
        </div>

        <div id="no_auction"
            @if (strcmp($rental_type, AuctionStatusEnum::NO_AUCTION) == 0) style="display: block;" @else style="display: none;" @endif>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.date-input id="contract_start_date_no_auction" :value="$d->contract_start_date" :label="__('long_term_rentals.contract_start_date')"
                        :optionals="['placeholder' => __('lang.select_date')]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="contract_end_date_no_auction" :value="$d->contract_end_date" :label="__('long_term_rentals.contract_end_date')"
                        :optionals="['placeholder' => __('lang.select_date')]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.radio-inline id="need_actual_delivery_date_no_auction" :value="$d->need_actual_delivery_date"
                        :list="$status_list" :label="__('long_term_rentals.actual_date_need')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3" id="need_actual_delivery_date_no_auction"
                    @if (strcmp($rental_type, AuctionStatusEnum::NO_AUCTION) == 0 && $d->need_actual_delivery_date == BOOL_TRUE) style="display: block;" @else style="display: none;" @endif>
                    <x-forms.date-input id="actual_delivery_date_no_auction" :value="$d->actual_delivery_date" :label="__('long_term_rentals.actual_delivery_date')"
                        :optionals="['placeholder' => __('lang.select_date'), 'required' => true]" />
                </div>
                <div class="col-sm-3" id="need_actual_delivery_remark_no_auction"
                    @if (strcmp($rental_type, AuctionStatusEnum::NO_AUCTION) == 0 && $d->need_actual_delivery_date == BOOL_FALSE) style="display: block;" @else style="display: none;" @endif>
                    <x-forms.input-new-line id="delivery_date_remark_no_auction" :value="$d->delivery_date_remark" :label="__('long_term_rentals.delivery_date_range')"
                        :optionals="['required' => true]" />
                </div>
            </div>
        </div>
        <div id="auction"
            @if (strcmp($rental_type, AuctionStatusEnum::AUCTION) == 0) style="display: block;" @else style="display: none;" @endif>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.radio-inline id="need_actual_delivery_date_auction" :value="$d->need_actual_delivery_date" :list="$status_list"
                        :label="__('long_term_rentals.actual_date_need')" :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3" id="need_actual_delivery_date_auction"
                    @if (strcmp($rental_type, AuctionStatusEnum::AUCTION) == 0 && $d->need_actual_delivery_date == BOOL_TRUE) style="display: block;" @else style="display: none;" @endif>
                    <x-forms.date-input id="actual_delivery_date_auction" :value="$d->actual_delivery_date" :label="__('long_term_rentals.actual_delivery_date')"
                        :optionals="['placeholder' => __('lang.select_date'), 'required' => true]" />
                </div>
                <div class="col-sm-3" id="need_actual_delivery_remark_auction"
                    @if (strcmp($rental_type, AuctionStatusEnum::AUCTION) == 0 && $d->need_actual_delivery_date == BOOL_FALSE) style="display: block;" @else style="display: none;" @endif>
                    <x-forms.input-new-line id="delivery_date_remark_auction" :value="$d->delivery_date_remark" :label="__('long_term_rentals.delivery_date_range')"
                        :optionals="['required' => true]" />
                </div>
            </div>
        </div>
    </div>
</div>
