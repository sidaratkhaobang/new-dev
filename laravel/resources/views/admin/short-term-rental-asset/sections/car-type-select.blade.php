<h4>{{ __('short_term_rentals.car_detail') }}</h4>
<hr>
<section>
    <div class="row gx-3 gy-3 mb-3">
        @foreach ($brand_list as $index => $brand)
            <div class="col-12 col-sm-3 col-md-2 col-lg-2">
                <x-forms.radio-block id="car_brand_id_{{ $index }}" name="car_brand_id" value="{{ $brand->id }}"
                                     selected="{{ 'all' }}">
                    <span class="block-title">{{ $brand->name . ' (' . intval($brand->car_sum) . ')' }}</span>
                    <div class="block-img-wrap">
                        @if (sizeof($brand->image) > 0)
                            <img src="{{ $brand->image[0]['url'] }}" class="block-img">
                        @else
                            <img src="{{ asset('images/car-sample/car-placeholder.png') }}" class="block-img">
                        @endif
                    </div>
                </x-forms.radio-block>
            </div>
        @endforeach
    </div>
</section>
