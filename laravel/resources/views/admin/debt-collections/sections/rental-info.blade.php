<div class="block-content">
    <div class="justify-content-between mb-4">
        <div class="row push">
            <div class="col-sm-3">
                @php
                    $text = '';
                    if ($d->rental) {
                        $text = 'สั้น';
                    } elseif ($d->lt_rental) {
                        $text = 'ยาว';
                    }
                @endphp
                <p class="size-text">{{ __('debt_collections.lt_rental_no') . $text }}</p>
                <p class="grey-text" id="lt_rental_no">{{ $d->worksheet_no ? $d->worksheet_no : null }}</p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.contract_no') }}</p>
                <p class="grey-text" id="contract_no"></p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.contract_start_date') }}</p>
                <p class="grey-text" id="contract_start_date">{{ $d->contract_start_date ? $d->contract_start_date : null }}</p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.contract_end_date') }}</p>
                <p class="grey-text" id="contract_end_date">{{ $d->contract_end_date ? $d->contract_end_date : null }}</p>
            </div>
        </div>
    </div>
</div>
