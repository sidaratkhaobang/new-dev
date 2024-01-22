<div class="row push">
    <div class="col-sm-12 text-end">
        <a class="btn btn-secondary"
            href="{{ route('admin.replacement-car-approves.index') }}">{{ __('lang.back') }}</a>
        @if ($approve_line_owner)
            @if (strcmp($d->status, ReplacementCarStatusEnum::PENDING_REVIEW) === 0)
                @can(Actions::Manage . '_' . Resources::ReplacementCarApprove)
                    <button type="button" class="btn btn-danger btn-disapprove-status"
                        data-status="{{ ReplacementCarStatusEnum::REJECT }}">{{ __('long_term_rentals.reject') }}</button>
                    <button type="button" class="btn btn-primary btn-approve-status"
                        data-status="CONFIRM">{{ __('long_term_rentals.approved') }}</button>
                @endcan
            @endif
        @endif
    </div>
</div>
