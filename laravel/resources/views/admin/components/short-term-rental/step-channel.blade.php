<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.channel')" :step="2" :success="$success"
                                :optionals="['block_icon_class' => __('short_term_rentals.step_icon.info'), 'is_toggle' => $istoggle, 'showstep' => $showstep]"
    />
    @isset($success)
        <div class="block-content pt-0">
            @php
                $data_info_arr = [
                    'order_channel' => $data_info?->order_channel ?: null,
                    'payment_channel' => $data_info?->payment_channel?: null,
                    'type_package' => $data_info?->type_package?: null,
                ];
            @endphp
            <div class="row ">
                @foreach($data_info_arr as $key => $value)
                    @php
                        $style= 'border-right: 1px solid #CBD4E1;';
                        if($loop->last){
                            $style= 'border-right: none;';
                        }
                    @endphp
                    <div class="col-sm-12 col-lg-4 text-center" style="{{ $style }}">
                        <p class="mt-2 mb-3">{{ __('short_term_rentals.step.' . $key) }}</p>
                        @if(!empty($value))
                            <p class="mb-2"><b>{{ __('short_term_rentals.order_channel_'.$value) }}</b></p>
                        @else
                            -
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endisset
</div>
