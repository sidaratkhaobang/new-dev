@if (isset($transfer_logs))
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th>#</th>
            <th>{{ __('car_park_transfers.transfer_type') }}</th>
            <th>{{ __('car_park_transfers.transfer_date_history') }}</th>
            <th>{{ __('car_park_transfers.driver') }}</th>
        </thead>
        <tbody>
            @foreach ($transfer_logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ __('car_park_transfers.transfer_type_' . $log->transfer_type) }}</td>
                    <td>
                        @if ($log->transfer_date)
                            @if ($log->transfer_type == \App\Enums\TransferTypeEnum::IN)
                                <i class="fa-solid fa-right-to-bracket" style="color:#157CF2"></i>
                            @else
                                <i class="fa-solid fa-right-from-bracket" style="color: #E04F1A "></i>
                            @endif
                            {{ get_thai_date_format($log->transfer_date, 'd/m/Y H:i') }}
                        @else
                            {{ null }}
                        @endif
                    </td>
                    <td>
                        {{($log->driver_name) ? $log->driver_name : null}}
                    </td>
                </tr>
            @endforeach

            @if($transfer_logs->isEmpty())
            <tr>
                <td class="text-center" colspan="4">" {{ __('lang.no_list') }} "</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endif
