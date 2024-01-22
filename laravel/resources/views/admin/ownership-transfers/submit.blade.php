{{-- <x-forms.submit-group :optionals="['url' => $url, 'view' => empty($view) ? null : $view]" /> --}}
<div class="row push me-1">
    <div class="col-sm-12 text-end">
        @if (isset($url))
            <a class="btn btn-outline-secondary btn-custom-size" href="{{ route($url) }}">{{ __('lang.back') }}</a>
        @endif
        @if (!isset($view))
            @if (in_array($d->status, [
                    OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER,
                    OwnershipTransferStatusEnum::TRANSFERING,
                ]))
                <button type="button"
                    class="btn btn-primary btn-custom-size btn-save-form ">{{ __('ownership_transfers.save_draft') }}</button>
            @elseif (in_array($d->status, [OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER]))
                <button type="button"
                    class="btn btn-primary btn-custom-size btn-save-form ">{{ __('ownership_transfers.save_send_transfer') }}</button>
            @endif


            @if (in_array($d->status, [OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER]))
                <button type="button" class="btn btn-info btn-custom-size btn-save-form-ownership "
                    data-status="{{ OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER }}">{{ __('ownership_transfers.save_ownership') }}</button>
            @elseif (in_array($d->status, [OwnershipTransferStatusEnum::TRANSFERING, OwnershipTransferStatusEnum::TRANSFERED]))
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-ownership"
                    data-status="{{ OwnershipTransferStatusEnum::TRANSFERED }}">{{ __('ownership_transfers.save_transfer_success') }}</button>
            @endif
        @endif
    </div>
</div>
