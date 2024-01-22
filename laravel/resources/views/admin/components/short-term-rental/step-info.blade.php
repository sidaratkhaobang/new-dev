<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}">
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.info')" :step="3" :success="$success" :optionals="['block_icon_class' => __('short_term_rentals.step_icon.info'), 'is_toggle' => $istoggle, 'showstep' => $showstep]" />

    @if($success)
    <div class="block-content pt-0">
        @php
        $origin_location = $data_info?->origin?->name;
        if(empty($origin_location)){
        $origin_location = $data_info?->origin_name ?: '-';
        }
        $destination_location = $data_info?->destination?->name;
        if(empty($destination_location)){
        $destination_location = $data_info?->destination_name ?: '-';
        }
        $data_info_arr = [
        'product' => $data_info?->product?->name ?: '-',
        'branch' => $data_info?->branch?->name ?: '-',
        'start_date' => get_thai_date_format($data_info->pickup_date),
        'end_date' => get_thai_date_format($data_info->return_date),
        'origin_location' => $origin_location,
        'destination_location' => $destination_location,
        ];
        @endphp
        <div class="row">
            @foreach($data_info_arr as $key => $value)
            @php
            $style= 'border-right: 1px solid #CBD4E1;';
            if($loop->last){
            $style= 'border-right: none;';
            }
            @endphp
            <div class="col-sm-12 col-lg-2 text-center" style="{{ $style }}">
                <p class="mt-2 mb-3">{{ __('short_term_rentals.step.' . $key) }}</p>
                <p class="mb-2"><b>{{ $value }}</b></p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>