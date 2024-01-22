<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.driver')" :step="5" :success="$success" :optionals="['block_icon_class' => __('short_term_rentals.step_icon.driver'), 'is_toggle' => $istoggle, 'showstep' => $showstep]" />

    @if($success)
    <div class="block-content pt-0 font-size-sm">
        <div class="row g-3">
            @foreach($cars as $keyCar => $valueCar)
            <div class="col-sm-12 col-lg-6">
                <div class="block mb-0">
                    <div class="block-content px-3 py-2">
                        <div class="d-flex">
                            <img src="{{ $valueCar->image_url }}" alt="CarImageDefault" class="card-image">
                            <div class="flex-grow-1 ms-3">
                                <span class="d-block">
                                    <b>{{ $valueCar?->class_full_name ?? '' }}</b>
                                </span>
                                <span class="d-block">
                                    {{ $valueCar?->license_plate ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <hr class="my-2">
                        @if(sizeof($valueCar->product_additionals) > 0)
                        <p class="mb-2">
                            <b>ข้อมูลออฟชั่นเสริม</b>
                        </p>
                        <div class="row mb-2">
                            <div class="col-sm-6 col-lg-6">
                                รายการ (จำนวน)
                            </div>
                            <div class="col-sm-6 col-lg-6 text-end">
                                ราคารวม
                            </div>
                        </div>
                        <div class="row">
                            @foreach($valueCar->product_additionals as $keyProduct => $valueProduct)
                            <div class="col-sm-6 col-lg-6">
                                <b>{{ $valueProduct['product_additional_name'] ?? '' }}
                                    ({{ intval($valueProduct['amount']) ?? '0'}})</b>
                            </div>
                            <div class="col-sm-6 col-lg-6 text-end">
                                <b>{{ price_format($valueProduct['subtotal'], true) ?? '0' }}</b>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="mt-3 mb-2 text-center">
                            ไม่มีข้อมูลออฟชั่นเสริม
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>