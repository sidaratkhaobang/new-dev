<table>
    <thead>
        <tr>
            <th>Flag</th>
            <th>Posting Key</th>
            <th>GL or Asset</th>
            <th>Fund Code</th>
            <th>Foreign Amount</th>
            <th>Local Amount</th>
            <th>Cost center</th>
            <th>Allocation</th>
            <th>Transaction Type</th>
            <th>Asset value Date</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Line item Text</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($asset_lot as $index => $item)
            @if ($item->asset_cars)
                @php
                    $sum = 0;
                @endphp
                @foreach ($item->asset_cars as $d)
                    <tr>
                        <td></td>
                        <td>70</td>
                        <td>{{ $d->main_asset }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $d->car_price }}</td>
                        <td></td>
                        <td>{{ $d->lot_no }}</td>
                        <td>100</td>
                        <td>{{ $d->asset_value_date }}</td>
                        <td>1</td>
                        <td></td>
                        <td>{{ $d->line_item_text }}</td>
                    </tr>
                    @php
                        $sum += $d->car_price;
                    @endphp
                @endforeach
            @endif
            @if (!empty($item->arr_add))
                <tr>
                    <td>{{ $item->arr_add['flag'] }}</td>
                    <td>{{ $item->arr_add['posting_key'] }}</td>
                    <td>{{ $item->arr_add['gl'] }}</td>
                    <td>{{ $item->arr_add['fund_code'] }}</td>
                    <td>{{ $item->arr_add['foreign'] }}</td>
                    <td>{{ $sum }}</td>
                    <td>{{ $item->arr_add['cost_center'] }}</td>
                    <td>{{ $item->arr_add['all_locataion'] }}</td>
                    <td>{{ $item->arr_add['transaction'] }}</td>
                    <td>{{ $item->arr_add['asset_date'] }}</td>
                    <td>{{ $item->arr_add['quantity'] }}</td>
                    <td>{{ $item->arr_add['unit'] }}</td>
                    <td>{{ $item->arr_add['line_text'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
