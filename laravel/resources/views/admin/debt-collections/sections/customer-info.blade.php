<div class="block-content">
    <div class="justify-content-between mb-4">
        <div class="row push">
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_code') }}</p>
                <p class="grey-text" id="customer_code">{{ $d->customer_code ? $d->customer_code : null }}</p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_name') }}</p>
                <p class="grey-text" id="customer_name">{{ $d->customer_name ? $d->customer_name : null }}</p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_group') }}</p>
                <p class="grey-text" id="customer_group">
                    @foreach ($customer_group as $item_group)
                        @php
                            $last_item = $loop->last ? '' : ', ';
                        @endphp
                        {{ $item_group->name }}{{ $last_item }}
                    @endforeach
                </p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_tax') }}</p>
                <p class="grey-text" id="customer_tax">{{ $d->customer_tax_no ? $d->customer_tax_no : null }}
                </p>
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_tel') }}</p>
                <p class="grey-text" id="customer_tel"></p>
            </div>
            <div class="col-sm-3">
                <p class="size-text">{{ __('debt_collections.customer_phone') }}</p>
                <p class="grey-text" id="customer_phone"></p>
            </div>
            <div class="col-sm-6">
                <p class="size-text">{{ __('debt_collections.customer_address') }}</p>
                <p class="grey-text" id="customer_address">
                    {{ $d->customer_address ? $d->customer_address : null }}</p>
            </div>
        </div>
    </div>
</div>
