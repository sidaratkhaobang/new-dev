{{-- <x-forms.submit-group :optionals="['url' => $url, 'view' => empty($view) ? null : $view]" /> --}}
<div class="row push me-1">
    <div class="col-sm-12 text-end">
        @if (isset($url))
            <a class="btn btn-outline-secondary btn-custom-size" href="{{ route($url) }}">{{ __('lang.back') }}</a>
        @endif
        @if (!isset($view))
            <button type="button" class="btn btn-primary btn-custom-size btn-save-form ">{{ __('lang.save') }}</button>
        @endif
    </div>
</div>
