<h4>{{ __('purchase_orders.po_detail') }}</h4>
<hr>
<div class="mb-5">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('purchase_orders.po_no') }}</th>
                <th>{{ __('purchase_orders.car_amount') }}</th>
                <th>{{ __('lang.status') }}</th>
                <th>{{ __('lang.remark') }}</th>
            </thead>
            <tbody>
                @if (sizeof($purchase_order_list) > 0)
                    @foreach ($purchase_order_list as $index => $purchase_order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $purchase_order->po_no }}</td>
                        <td>{{ $purchase_order->purchaseOrderLines?->sum('amount') }}</td>
                        <td>{{ __('purchase_orders.status_'. $purchase_order->status) }}</td>
                        <td>{{ $purchase_order->remark }}</td>
                    </tr>
                    @endforeach
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“ {{ __('lang.no_list').__('purchase_orders.po_detail') }} “</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

