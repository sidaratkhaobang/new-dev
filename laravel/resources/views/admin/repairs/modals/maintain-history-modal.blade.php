<div class="modal fade" id="modal-maintain-history" aria-labelledby="modal-maintain-history"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintain-history-modal-label">{{ __('repairs.maintain_history') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('repairs.maintain_date') }}</th>
                                <th>{{ __('repairs.maintain_type') }}</th>
                                <th>{{ __('repairs.maintain_mileage') }}</th>
                                <th>{{ __('repairs.maintain_description') }}</th>
                                <th>{{ __('repairs.maintain_contact') }}</th>
                                <th>{{ __('repairs.tel') }}</th>
                                <th>{{ __('repairs.maintain_user') }}</th>
                                <th>{{ __('repairs.maintain_tel') }}</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
            </div>
        </div>
    </div>
</div>
