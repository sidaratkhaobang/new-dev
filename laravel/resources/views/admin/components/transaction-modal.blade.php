<div class="modal fade" id="transaction" role="dialog" style="overflow:hidden;" aria-labelledby="transaction">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">{{ __('purchase_requisitions.transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- @if (isset($d->audits)) --}}
                <br>
                <div class="mb-5">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">

                                <th>{{ __('lang.datetime') }}</th>
                                <th>{{ __('lang.full_name') }}</th>
                                <th>{{ __('lang.role_name') }}</th>
                                <th>{{ __('lang.branch_name') }}</th>
                                <th>{{ __('lang.action') }}</th>
                                <th>{{ __('lang.remark') }}</th>
                            </thead>
                            <tbody>
                                {{-- @if (isset($transaction))
                                @foreach ($transaction as $index => $trans)
                                    <tr>
                                        <td>{{ get_thai_date_format($audit->created_at, 'd/m/Y H:i') }}</td>
                                        <td>{{ $trans->user ? $trans->user->name : '' }}</td>
                                        <td>{{ $trans->user ? $trans->user->role->name : '' }}</td>
                                        <td>{{ $trans->user ? $trans->user->branch->name : '' }}</td>
                                        <td>
                                            @if (isset($trans->getModified()['status']) && $trans->getModified()['status']['new'] == 'CONFIRM' && strcmp($trans->event, 'updated') === 0)
                                                {{ 'อนุมัติ ' }}
                                           
                                            @elseif (isset($trans->getModified()['status']) &&
                                                    $trans->getModified()['status']['new'] == 'REJECT' &&
                                                    strcmp($trans->event, 'updated') === 0)
                                                {{ 'ไม่อนุมัติ ' }}
                                         
                                            @elseif (strcmp($trans->event, 'updated') === 0)
                                                {{ 'แก้ไข ' }}
                                            
                                            @elseif (strcmp($trans->event, 'created') === 0)
                                                {{ 'สร้าง' }}
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif --}}

                                @if (isset($approve_line_logs))
                                    @foreach ($approve_line_logs as $index => $item)
                                        <tr>
                                            <td>{{ $item['approved_date'] ? get_thai_date_format($item['approved_date'], 'j F Y H:i' . ' น.') : '' }}
                                            </td>
                                            <td>{{ $item['user_name'] ?? '' }}</td>
                                            <td>{{ $item['role_name'] ?? '' }}</td>
                                            <td>{{ $item['branch_name'] ?? '' }}</td>
                                            <td> {{ $item['status'] ? __('lang.status_' . $item['status']) : '' }}</td>
                                            <td>{{ $item['reason'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
