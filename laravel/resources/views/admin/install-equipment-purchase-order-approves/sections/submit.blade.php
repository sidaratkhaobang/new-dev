<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row">
            <div class="col-sm-12 text-end">
                <a class="btn btn-outline-secondary btn-custom-size me-1"
                    href="{{ route('admin.install-equipment-po-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($approve_line_owner)
                    @if (strcmp($d->status, InstallEquipmentPOStatusEnum::PENDING_REVIEW) === 0)
                        @can(Actions::Manage . '_' . Resources::InstallEquipmentPOApprove)
                        <button type="button" class="btn btn-danger btn-disapprove-status me-1"
                            data-status="{{ InstallEquipmentPOStatusEnum::REJECT }}">{{ __('long_term_rentals.reject') }}</button>
                        <button type="button" class="btn btn-primary btn-approve-status me-1"
                            data-status="{{ InstallEquipmentPOStatusEnum::CONFIRM }}">{{ __('long_term_rentals.approved') }}</button>
                        @endcan
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
