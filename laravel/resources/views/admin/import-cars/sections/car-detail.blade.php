<h4>{{ __('purchase_orders.purchase_requisition_car_detail') }}</h4>
<hr>

<div class="mb-5">
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('purchase_orders.model') }}</th>
                <th>{{ __('purchase_orders.color') }}</th>
                <th>{{ __('purchase_orders.price') }}</th>
                <th>{{ __('purchase_orders.amount') }}</th>
                <th>{{ __('purchase_orders.total_price') }}</th>
            </thead>
            <tbody>
                @php
                    $total_cars = 0;
                    $sum_total_price = 0;
                    $total_price = 0;

                @endphp
                @if (sizeof($purchase_requisition_cars) > 0)
                    @foreach ($purchase_requisition_cars as $index => $purchase_requisition_car)
                        @php
                            $total_cars += $purchase_requisition_car->amount;
                            $total_price = $purchase_requisition_car->total ;
                            $sum_total_price += $total_price;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $purchase_requisition_car->class_name }} - {{ $purchase_requisition_car->name }}</td>
                            <td>{{ $purchase_requisition_car->color_name }}</td>
                            <td>{{ number_format($purchase_requisition_car->total/$purchase_requisition_car->amount,2) }}</td>
                            <td>{{ $purchase_requisition_car->amount }} {{ __('purchase_orders.car_unit') }}</td>
                            <td>{{ number_format($total_price,2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ __('purchase_orders.summary_car_detail') }}</th>
                        <th>{{ $total_cars }} {{ __('purchase_orders.car_unit') }}</th>
                        <th>{{ number_format($sum_total_price,2) }}</th>
                    </tr>
                @else
                    <tr class="table-empty">
                        <td class="text-center" colspan="6">“
                            {{ __('lang.no_list') . __('purchase_orders.purchase_requisition_car_detail') }} “</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
