{{-- <x-forms.submit-group :optionals="['url' => $url, 'view' => empty($view) ? null : $view]" /> --}}
<div class="row push me-1">
    <div class="col-sm-12 text-end">
        @if (isset($url))
            <a class="btn btn-outline-secondary btn-custom-size" href="{{ route($url) }}">{{ __('lang.back') }}</a>
        @endif
        @if (!isset($view))

            @if ($d->status == TaxRenewalStatusEnum::PREPARE_DOCUMENT)
                <button type="button"
                    class="btn btn-primary btn-custom-size btn-save-form ">{{ __('tax_renewals.save_draft') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-tax-renewal "
                    data-status="{{ TaxRenewalStatusEnum::WAITING_SEND_TAX }}">{{ __('tax_renewals.save_prepare_info') }}</button>
            @elseif($d->status == TaxRenewalStatusEnum::WAITING_SEND_TAX)
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-tax-renewal "
                    data-status="{{ TaxRenewalStatusEnum::RENEWING }}">{{ __('tax_renewals.save_send_tax') }}</button>
            @elseif($d->status == TaxRenewalStatusEnum::RENEWING)
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-tax-renewal "
                    data-status="{{ TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK }}">{{ __('tax_renewals.save_after_renew') }}</button>
            @elseif($d->status == TaxRenewalStatusEnum::WAITING_TAX_REGISTER_BOOK)
                <button type="button"
                    class="btn btn-primary btn-custom-size btn-save-form ">{{ __('tax_renewals.save_draft') }}</button>
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-tax-renewal "
                    data-status="{{ TaxRenewalStatusEnum::SUCCESS }}">{{ __('tax_renewals.save_success') }}</button>
            @endif
        @endif
    </div>
</div>
