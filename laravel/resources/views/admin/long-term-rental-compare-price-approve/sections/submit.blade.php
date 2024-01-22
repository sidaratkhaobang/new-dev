<div class="row push">
    <div class="text-end">     
        <a class="btn btn-secondary" href="{{ route('admin.long-term-rental.compare-price.index') }}" >{{ __('lang.back') }}</a>
        @if ($d->comparison_price_status == \App\Enums\ComparisonPriceStatusEnum::DRAFT)
            <a class="btn btn-primary btn-save-form">{{ __('lang.save_draft') }}</a>  
        @endif
        <a class="btn btn-info btn-confirm-status">{{ __('lang.save') }}</a>
    </div>
</div>