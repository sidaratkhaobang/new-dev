@foreach($product_additional_carousel as $index => $chunk)
<div class="carousel-item @if ($loop->first) active @endif">
    <div class="row g-3">
        @foreach($chunk as $index2 => $product)
        <div class="col-3" style="font-size: 14px;">
            <x-forms.radio-block :id="'transport_send_'.$product?->type" name="transport" value="{{ $product?->type }}" selected="{{ null }}">
                <div class="row h-100">
                    <div class="col-6 col-lg-6 col-sm-12 my-auto text-center">
                        <img class="img-block " style="max-width: 73px;width: 100%;max-height: 50px;height: 100%" src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                    </div>
                    <div class="col-6 col-lg-6 col-sm-12 my-auto">
                        <span class="align-middle">
                            {{$product?->title}}
                            <br>
                            {{$product?->sub_title}}
                        </span>
                    </div>
                </div>
            </x-forms.radio-block>
        </div>
        @endforeach
    </div>
</div>
@endforeach