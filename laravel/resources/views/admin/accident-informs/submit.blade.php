<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push">
            <div class="col-12 text-end">
                @if (isset($btn_group_sheet))
                <a class="btn btn-outline-secondary btn-custom-size"
                    href="{{ route('admin.accident-inform-sheets.index') }}">{{ __('lang.back') }}</a>
                @else
                <a class="btn btn-outline-secondary btn-custom-size"
                href="{{ route('admin.accident-informs.index') }}">{{ __('lang.back') }}</a>
                @endif
                @if (!isset($view))
                    <button type="button"
                        class="btn btn-primary btn-custom-size btn-save-review ">{{ __('lang.save') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
