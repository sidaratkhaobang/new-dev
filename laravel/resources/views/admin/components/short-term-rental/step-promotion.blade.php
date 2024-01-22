<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.promotion')" :step="6" :success="$success" :optionals="['block_icon_class' => __('short_term_rentals.step_icon.promotion'), 'is_toggle' => $istoggle, 'showstep' => $showstep]" />

    @if($success)
    <div class="block-content pt-0">
        <div class="row">
            @if(sizeof($promotions) > 0)
            <div class="col-sm-12 col-lg-6">
                <p class="mb-2">โปรโมชั่นที่ใช้</p>
                @foreach($promotions as $promotion)
                <div class="block mb-3 font-size-sm">
                    <div class="block-content px-3 py-3">
                        <b>{{ $promotion->promotion_name }}</b>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="col-sm-12 col-lg-6">
                <p class="mb-0">ไม่มีโปรโมชั่นที่ใช้</p>
            </div>
            @endif

            @if(sizeof($coupons) > 0)
            <div class="col-sm-12 col-lg-6">
                <p class="mb-2">Voucher ที่ใช้</p>
                @foreach($coupons as $coupon)
                <div class="block mb-3 font-size-sm">
                    <div class="block-content px-3 py-2">
                        <p class="mb-0">เลขที่ Voucher : {{ $coupon->voucher_code }}</p>
                        <p class="mb-0"><b>{{ $coupon->promotion_name }}</b></p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="col-sm-12 col-lg-6">
                <p class="mb-0">ไม่มี Voucher ที่ใช้</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>