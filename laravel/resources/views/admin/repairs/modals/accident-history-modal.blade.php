<div class="modal fade" id="modal-accident-history" aria-labelledby="modal-accident-history"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accident-history-modal-label">{{ __('repairs.accident_history') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('repairs.accident_date') }}</th>
                                <th>{{ __('repairs.accident_detail') }}</th>
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
