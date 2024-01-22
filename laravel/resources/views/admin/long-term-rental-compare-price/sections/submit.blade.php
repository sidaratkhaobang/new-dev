<div class="row push">
    <div class="text-end">     
        <a class="btn btn-secondary" href="{{ route('admin.long-term-rental.compare-price.index') }}" >{{ __('lang.back') }}</a>
        @if (in_array($d->comparison_price_status, [
            \App\Enums\ComparisonPriceStatusEnum::DRAFT,
            \App\Enums\ComparisonPriceStatusEnum::REJECT
        ]))
        @if(!isset($view))
            <a class="btn btn-primary btn-draft-status">{{ __('lang.save_draft') }}</a>  
            @endif
        @endif
        @if(!isset($view))
        <a class="btn btn-info btn-confirm-status">{{ __('lang.save') }}</a>
        @endif
    </div>
</div>