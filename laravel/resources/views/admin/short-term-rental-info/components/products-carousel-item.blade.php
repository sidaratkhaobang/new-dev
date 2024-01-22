@foreach($products as $index => $chunk)
<div class="carousel-item @if ($loop->first) active @endif">
    <div class="row g-3">
        @foreach($chunk as $index2 => $product)
        <div class="col-12 col-sm-4 col-md-3 col-lg-2" style="font-size: 14px;" >
            <x-forms.radio-block id="product_id_{{ $index2 }}" name="product_id" value="{{ $product->id }}" selected="{{ null }}" >
                <span class="block-title" >{{ $product->name }}</span>
                <div class="row gx-0 mt-2">
                    <div class="col-6">
                        <p class="m-0" >เริ่มจองได้</p>
                        <b>{{ $product->start_booking_time }}</b>
                    </div>
                    <div class="col-6">
                        <p class="m-0" >สิ้นสุดการจอง</p>
                        <b>{{ $product->end_booking_time }}</b>
                    </div>
                </div>
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >จองขั้นต่ำล่วงหน้า</p>
                        <b>{{ intval($product->reserve_booking_duration) . " ชม." }}</b>
                    </div>
                </div>
                <div class="row gx-0 mt-2">
                    <div class="col-12">
                        <p class="m-0" >วันที่สามารถจองได้</p>
                    </div>
                    <div class="col-12">
                        <div class="row g-0" >
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_mon,
                                    'name' => 'จ.',
                                ])
                            </div>
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_tue,
                                    'name' => 'อ.',
                                ])
                            </div>
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_wed,
                                    'name' => 'พ.',
                                ])
                            </div>
                        </div>
                        <div class="row g-0" >
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_thu,
                                    'name' => 'พฤ.',
                                ])
                            </div>
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_fri,
                                    'name' => 'ศ.',
                                ])
                            </div>
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_sat,
                                    'name' => 'ส.',
                                ])
                            </div>
                        </div>
                        <div class="row g-0" >
                            <div class="col-4" >
                                @include('admin.short-term-rental-info.components.date-checkbox', [
                                    'day' => $product->booking_day_sun,
                                    'name' => 'อา.',
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mb-2" >
                <div class="text-center w-100">
                    <p class="m-0" >
                        <span style="font-size: 18px;" ><b>{{ number_format($product->standard_price, 2) }}</b></span>
                        <span>บาท</span>
                    </p>
                </div>
            </x-forms.radio-block>
        </div>
        @endforeach
    </div>
</div>
@endforeach