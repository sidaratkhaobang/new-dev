<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row">
            <div class="text-end">
                <a class="btn btn-outline-secondary btn-custom-size" href="{{ route('admin.check-credit-approves.index') }}">{{ __('lang.back') }}</a>
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <button type="button" class="btn btn-primary btn-save-form-custom btn-custom-size ms-1">{{ __('check_credit.form.btn-save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
