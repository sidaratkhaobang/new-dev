<div class="modal fade" id="approve-history-modal" aria-labelledby="approve-history-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approve-history-modal-label">{{ __('install_equipment_pos.history_approve') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('config_approves.approver') }}</th>
                                <th>{{ __('config_approves.approve_datetime') }}</th>
                                <th>{{ __('lang.status') }}</th>
                            </thead>
                            <tbody>
                                @if (isset($approve_line_logs))
                                @foreach ($approve_line_logs as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['user_name'] ?? '' }}</td>
                                        <td>{{ $item['approved_date'] ? get_thai_date_format($item['approved_date'], 'j F Y') : '' }}</td>
                                        <td>
                                            {{ $item['status'] ? __('install_equipment_pos.status_' . $item['status'] ) : '' }}
                                            {{ $item['reason'] ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
