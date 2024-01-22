<div class="block-header mt-1 @if(!isset($is_toggle)) pb-0 @endif {{ $block_header_class ?? '' }}">
    <h3 class="block-title {{ $block_title_class ?? '' }}">
        <i class=" me-2 {{ $block_icon_class ?? 'icon-document' }}"></i> {{ $text ?? '' }}
        @if(isset($block_step) && $block_step == true)
            @if(isset($block_step_success) && $block_step_success == true)
                <span class="ms-2 block_step_success d-flex justify-content-center align-items-center">
                <span class="d-flex align-items-center" style=" font-size: 12px;">
                    <img class="ms-1 me-1" src="{{asset('images/icons/vector.png')}}">
                    @if(isset($block_step_text))
                        <span class="ms-1 me-1">
                            {{$block_step_text ?? ''}}
                        </span>
                    @endif
                </span>
            </span>
            @else
                <span class="ms-2 block_step d-flex justify-content-center align-items-center">
                <span style=" font-size: 12px;">
                    @if(isset($block_step_text))
                        {{$block_step_text ?? ''}}
                    @endif
                </span>
            </span>
            @endif

        @endif
    </h3>
    <div class="block-options {{ $block_option_class ?? '' }}">
        @yield('block_options' . ($block_option_id ?? ''))
        @if (isset($is_toggle) && $is_toggle == true)
            <div class="block-options-item ms-2">
                <a class="block-option-toggle {{ $block_toggle_class ?? '' }}" data-toggle="block-option"
                   data-action="content_toggle"></a>
            </div>
        @endif
    </div>
</div>
@push('custom_styles')
    <style>
        .block_step {

            width: 108px;
            /*display: flex;*/
            height: 24px;
            /*justify-content: center;*/
            /*align-items: center;*/
            /*gap: 8px;*/
            color: var(--genaral-white, #FFF);
            border-radius: 100px;
            background: var(--neutral-body-fonts-01, #27364B);
        }

        .block_step_success {
            width: 108px;
            /*display: flex;*/
            height: 24px;
            /*justify-content: center;*/
            /*align-items: center;*/
            /*gap: 8px;*/
            color: var(--genaral-white, #FFF);
            border-radius: 100px;
            background: var(--action-color-info-02, #4D82F3);
        }
    </style>
@endpush
