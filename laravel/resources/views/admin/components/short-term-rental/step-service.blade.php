@push('styles')
    <style>
        .block-car-type {
            border-radius: 6px;
            border: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: var(--genaral-white, #FFF);
            width: 150px;
            min-height: 64px;

        }
        .block-car-image {
            max-width: 47px;
            width: 100%;
            height: 100%
        }
    </style>
@endpush
<div class="block {{ __('block.styles') }} {{ ($show ? '' : 'block-mode-hidden') }}" >
    <x-blocks.block-header-step :title="__('short_term_rentals.step_title.service_type')" :step="1" :success="$success" 
        :optionals="['block_icon_class' => __('short_term_rentals.step_icon.service_type'), 'is_toggle' => $istoggle, 'showstep' => $showstep]" 
    />

    @if($success)
    <div class="block-content pt-0">
        <div class="block-car-type d-flex justify-content-center align-items-center">
            <img src="{{ $service_type_url }}" alt="..." class="block-car-image ms-1 me-1">
            <span class="ms-1 me-1">
                {{$service_type_name}}
            </span>
        </div>
    </div>
    @endif
</div>
