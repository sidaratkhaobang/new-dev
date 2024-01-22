<div class="block {{ __('block.styles') }}">
    <div class="block-content box-padding-bottom block-premium">
        <div class="mb-3 block-header">
            <h4>
                <i class="fa fa-file-lines"></i> {{ __('request_premium.car_premium_detail') }}</h4>
            <button class="btn-apply-all btn-premium-apply-all" type="button">
                {{__('request_premium.apply_all')}}
            </button>
        </div>
        <div class="block-header overflow-auto">
            @include('admin.cmi-components.request-premium-section')
        </div>
    </div>
</div>

