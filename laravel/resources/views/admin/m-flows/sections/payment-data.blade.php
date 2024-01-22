<x-blocks.block :title="__('m_flows.payment_data')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="payment_date" :value="$d->payment_date" :label="__('m_flows.payment_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.upload-image :id="'payment_file'" :label="__('m_flows.payment_file')" />
        </div>
    </div>
    @if (sizeof($m_flow_list) > 0)
        <h6>{{ __('m_flows.m_flow_list') }}</h6>
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 5%">{{ __('lang.seq') }}</th>
                        <th>{{ __('m_flows.worksheet_no') }}</th>
                        <th>{{ __('m_flows.station_place') }}</th>
                        <th>{{ __('m_flows.offense_date') }}</th>
                        <th>{{ __('m_flows.fee') }}</th>
                        <th>{{ __('m_flows.fine') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($m_flow_list) > 0)
                        @foreach ($m_flow_list as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->worksheet_no }}</td>
                                <td>{{ $item->exprssway_name }}</td>
                                <td>{{ $item->offense_format }}</td>
                                <td>{{ number_format($item->fee, 2, '.', ',') }}</td>
                                <td>{{ number_format($item->fine, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="6">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</x-blocks.block>
