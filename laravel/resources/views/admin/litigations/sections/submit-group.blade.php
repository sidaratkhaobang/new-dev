<div class="row">
    <div class="text-end">
            <button type="button" class="btn btn-outline-secondary btn-custom-size me-2" onclick="window.history.back();" >{{ __('lang.back') }}</button>
        @if(!isset($view))
            @can(Actions::Manage . '_' . Resources::Litigation)
            @if (!in_array($d->status, [LitigationStatusEnum::PENDING_REVIEW, LitigationStatusEnum::COMPLETE]))
                <button type="button" class="btn btn-danger btn-custom-size btn-close-litigation me-2" >{{ __('litigations.close') }}</button>
                <button type="button" class="btn btn-info btn-custom-size btn-save-draft-litigation me-2" >{{ __('lang.save_draft') }}</button>
            @endif
            @if (!in_array($d->status, [ LitigationStatusEnum::COMPLETE]))
                <button type="button" class="btn btn-primary btn-custom-size btn-save-form-litigation" >{{ __('lang.save') }}</button>
            @endif
            @endcan
        @endif
    </div>
</div>
