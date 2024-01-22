@if (!$d->status || in_array($d->status, [TransferCarEnum::CONFIRM_RECEIVE,TransferCarEnum::IN_PROCESS, TransferCarEnum::REJECT_RECEIVE]))
<x-forms.submit-group :optionals="['url' => $url, 'view' => empty($view) ? null : $view]" />
@elseif (in_array($d->status, [TransferCarEnum::WAITING_RECEIVE]))
<div class="row push">
    <div class="col-sm-12 text-end">
        @if (isset($url))
            <a class="btn btn-outline-secondary btn-custom-size"
                href="{{ route($url) }}">{{ __('lang.back') }}</a>
        @endif
        @if (!isset($view))
            @can(Actions::Manage . '_' . Resources::TransferCarReceive)
                <button type="button" class="btn btn-danger btn-not-approve-status"
                    data-id="{{ $d->id }}"
                    data-status="{{ TransferCarEnum::REJECT_RECEIVE }}">{{ __('transfer_cars.not_confirm') }}
                </button>
                <button type="button" class="btn btn-success btn-approve-status"
                    data-id="{{ $d->id }}"
                    data-status="{{ TransferCarEnum::CONFIRM_RECEIVE }}">{{ __('transfer_cars.confirm') }}</button>
            @endcan
        @endif
    </div>
</div>
@else
<div class="row push">
    <div class="col-sm-12 text-end">
        @if (isset($url))
            <a class="btn btn-outline-secondary btn-custom-size"
                href="{{ route($url) }}">{{ __('lang.back') }}</a>
        @endif
    </div>
</div>
@endif
