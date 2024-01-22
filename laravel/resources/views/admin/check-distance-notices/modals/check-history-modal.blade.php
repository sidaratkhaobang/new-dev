<div class="modal fade" id="modal-repair-history" aria-labelledby="modal-repair-history" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repair-history-modal-label">ประวัติเช็กระยะ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
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
                                @if (sizeof($check_history_list) > 0)
                                    @foreach ($check_history_list as $index => $d)
                                        <tr>
                                            <td>{{ $d->repair_date ? get_thai_date_format($d->repair_date, 'd/m/Y') : null }}
                                            </td>
                                            <td>{{ __('repairs.repair_type_' . $d->repair_type) }}</td>
                                            <td>{{ $d->mileage }}</td>
                                            <td>{{ $d->remark }}</td>
                                            <td>{{ $d->contact }}</td>
                                            <td>{{ $d->tel }}</td>
                                            <td>{{ $d->contact }}</td>
                                            <td>{{ $d->tel }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="table-empty" id='empty-data'>
                                        <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
                                    </tr>
                                @endif
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
