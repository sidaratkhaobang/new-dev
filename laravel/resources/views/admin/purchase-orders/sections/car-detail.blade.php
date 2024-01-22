<h4>{{ __('purchase_orders.purchase_requisition_car_detail') }}</h4>
<hr>
<div class="mb-5">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('purchase_orders.model') }}</th>
                <th>{{ __('purchase_orders.color') }}</th>
                <th>{{ __('purchase_orders.amount') }}</th>
            </thead>
            <tbody>
                @php
                    $total_cars = 0;
                @endphp
                @if (sizeof($purchase_requisition_cars) > 0)
                    @foreach ($purchase_requisition_cars as $index => $purchase_requisition_car)
                    @php
                       $total_cars +=  $purchase_requisition_car->amount;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $purchase_requisition_car->model_full_name . ' - ' . $purchase_requisition_car->model }}</td>
                        <td>{{ $purchase_requisition_car->color }}</td>
                        <td>{{ $purchase_requisition_car->amount }} {{ __('purchase_orders.car_unit') }}</td>
                        <input type="hidden" name="cars[amount][{{ $purchase_requisition_car->id }}]" value="{{ $purchase_requisition_car->amount }}">
                    </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th>{{ __('purchase_orders.summary_car_detail') }}</th>
                        <th></th>
                        <th>{{ $total_cars }} {{ __('purchase_orders.car_unit') }}</th>
                    </tr>
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="4">“ {{ __('lang.no_list').__('purchase_orders.purchase_requisition_car_detail') }} “</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

