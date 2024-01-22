<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row">
            <div class="text-end">
                <a class="btn btn-outline-secondary btn-custom-size me-1" href="{{ route('admin.contracts.index') }}">{{ __('กลับ') }}</a>
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <button type="button" class="btn btn-primary btn-save-form-custom btn-custom-size">{{ __('บันทึก') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
