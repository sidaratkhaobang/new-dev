
<div class="row">
    <div class="text-end">
            <button type="button" class="btn btn-outline-secondary btn-custom-size me-2" onclick="window.history.back();" >{{ __('lang.back') }}</button>
        @if(!is_view())
            @if (!in_array($d->status, [CompensationStatusEnum::PENDING_REVIEW, CompensationStatusEnum::COMPLETE]))
                <button type="button" class="btn btn-info btn-custom-size btn-save-draft-litigation me-2" >{{ __('lang.save_draft') }}</button>
            @endif
            @if (!in_array($d->status, [ CompensationStatusEnum::COMPLETE]))
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form" >{{ __('lang.save') }}</button>
            @endif
        @endcan
    </div>
</div>
