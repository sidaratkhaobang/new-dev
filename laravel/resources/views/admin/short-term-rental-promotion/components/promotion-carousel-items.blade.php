@foreach($promotions->chunk(4) as $index => $chunk)
<div class="carousel-item @if ($loop->first) active @endif">
    <div class="row g-3">
        @foreach($chunk as $index2 => $promotion)
        <div class="col-12 col-sm-4 col-md-3 col-lg-3" style="font-size: 14px;" >
            <x-forms.checkbox-block id="promotion_id_{{ $index2 }}" name="promotion_id" value="{{ $promotion->id }}" selected="{{ null }}" >
                <span class="block-title" >{{ $promotion->name }}</span>
                <hr class="mb-2 mt-2" >
                @if(!empty($promotion->promotion_code))
                <div>
                    <span class="block-step block-step--success ps-3 pe-3" >
                        <span class="block-step-text" style="line-height: 16px;" >Code : {{ $promotion->promotion_code }}</span>
                    </span>
                </div>
                @endif
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <b>เงื่อนไขโปรโมชัน</b>
                    </div>
                </div>
                <div class="row gx-0 mt-2">
                    <div class="col-6">
                        <p class="m-0" >วันที่เริ่มใช้งาน</p>
                        <b>{{ date('d/m/Y', strtotime($promotion->start_date)) }}</b>
                    </div>
                    <div class="col-6">
                        <p class="m-0" >วันที่สิ้นสุดการใช้งาน</p>
                        <b>{{ date('d/m/Y', strtotime($promotion->end_date)) }}</b>
                    </div>
                </div>
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >ประเภทการลด</p>
                        <b>{{ __('promotions.discount_type_' . $promotion->discount_type) }}</b>
                    </div>
                </div>
                @if(in_array($promotion->discount_type, [DiscountTypeEnum::PERCENT, DiscountTypeEnum::AMOUNT, DiscountTypeEnum::FIXED_PRICE]))
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >จำนวนที่ลด</p>
                        <b>{{ price_format_human($promotion->discount_amount) }} {{ __('promotions.discount_type_unit_' . $promotion->discount_type) }}</b>
                    </div>
                </div>
                @endif
        
                @if(in_array($promotion->discount_type, [DiscountTypeEnum::FREE_CAR_CLASS]))
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >รุ่นรถที่แถม</p>
                        <b>{{ $promotion->getFreeCarClassName() }}</b>
                    </div>
                </div>
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >จำนวนวัน/ชั่วโมงที่ลด</p>
                        <b>{{ price_format_human($promotion->discount_amount) }} {{ __('promotions.discount_type_unit_' . $promotion->discount_type) }}</b>
                    </div>
                </div>
                @endif
        
                @if(!empty($promotion->condition))
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >เงื่อนไขโปรโมชัน</p>
                        <div>
                            {{ $promotion->condition }}
                        </div>
                    </div>
                </div>
                @endif
            </x-forms.checkbox-block>
        </div>
        @endforeach
    </div>
</div>
@endforeach