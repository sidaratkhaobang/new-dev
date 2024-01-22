<table>
    <thead>
    <tr>
        <th>{{ __('purchase_orders.model') }}</th>
        <th>{{ __('purchase_orders.color') }}</th>
        <th>{{ __('purchase_orders.purchase_order_no') }}</th>
        <th>#</th>
        <th>{{ __('import_cars.engine_no') }}</th>
        <th>{{ __('import_cars.chassis_no') }}</th>
        <th>{{ __('import_cars.delivery_ready_date') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($po_lines as $index => $po_line)
    @for($i=0;$i<$po_line->amount;$i++)
        <tr>
            @if($i == 0)
            <td style="width: 400px;">{{ $po_line->class_name }} - {{ $po_line->name }}  (สี{{ $po_line->color_name }})</td>
            <td style="width: 200px;">{{ $po_line->color_name }}</td>
            <td style="width: 200px;">{{ $po_line->po_worksheet_no }}</td>
            @else
            <td style="width: 400px;"></td>
            <td style="width: 200px;"></td>
            <td style="width: 200px;"></td>
            @endif
            <td style="width: 100px; text-align:left;">{{ $i+1 }}</td>
            <td style="width: 200px;"></td>
            <td style="width: 200px;"></td>
            <td style="width: 200px;"></td>
        </tr>
    @endfor
    @endforeach
    </tbody>
</table>