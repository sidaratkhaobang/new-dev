<h4>{{ __('purchase_orders.dealer_list') }}</h4>
<hr>
<div class="mb-5" id="purchase-order-dealers">
    <div class="table-responsive db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark bg-body-border-line">
                <tr>
                    <th class="custom-align-center" rowspan="2">#</th>
                    <th class="custom-align-center" rowspan="2">{{ __('purchase_orders.model') }}</th>
                    <th class="custom-align-center" rowspan="2">{{ __('purchase_orders.color') }}</th>
                    <th class="custom-border-right custom-align-center" rowspan="2">{{ __('lang.total') }}</th>
                     @foreach ($purchase_order_dealer_list as $index => $purchase_order_dealer)
                        <th class="custom-border-right text-center custom-align-center" colspan="2">
                            <div class="custom-droppown-action">
                                {{ $purchase_order_dealer->creditor_text }}
                                <div class="btn-group">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm dropdown-toggle" id="dropdown-dropleft-dark"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="far fa-file text-primary"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @if ($purchase_order_dealer->dealer_files && count($purchase_order_dealer->dealer_files) > 0)
                                                @foreach ($purchase_order_dealer->dealer_files as $file_index => $dealer_file)
                                                    <a target="_blank" class="dropdown-item" href="{{ $dealer_file['url'] }}">
                                                        {{ $dealer_file['name'] }}
                                                    </a>
                                                @endforeach
                                            @else
                                                <a class="dropdown-item text-muted" disabled>{{ __('lang.no_attach_file') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                    @endforeach
                </tr>
                @if (sizeof($purchase_order_dealer_list) > 0)
                    <tr>
                        @foreach ($purchase_order_dealer_list as $i => $d)
                            <th class="custom-border-right text-end">{{ __('purchase_orders.price_per_car') }}</th>
                            <th class="custom-border-right text-end">{{ __('purchase_orders.price_per_total') }}</th>
                        @endforeach
                    </tr>
                @endif
            </thead>
            @if (sizeof($purchase_order_dealer_list) > 0)
                <tbody>
                    @foreach ($purchase_requisition_cars as $car_index => $car)
                        <tr>
                            <td>{{ $car_index + 1 }}</td>
                            <td>
                                <p>{{ $car->model_full_name. ' -' }}</p>
                                <p>{{ $car->model }}</p>
                            </td>
                            <td>{{ $car->color }}</td>
                            <td class="custom-border-right">{{ $car->amount }} {{ __('purchase_orders.car_unit') }}</td>
                            @foreach ($purchase_order_dealer_list as $purchase_order_dealer_index => $purchase_order_dealer)
                                <td class="text-end custom-border-right">
                                    {{ number_format($purchase_order_dealer->dealer_price_list[$car_index]->car_price, 2) }}
                                </td>
                                <td class="text-end custom-border-right">
                                    {{ number_format($purchase_order_dealer->dealer_price_list[$car_index]->car_price * $car->amount, 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th colspan="3">สรุปราคารวมทั้งหมด</th>
                        @foreach ($purchase_order_dealer_list as $purchase_order_dealer_index => $purchase_order_dealer)
                            <th class="text-end" colspan="2">
                                {{ number_format($purchase_order_dealer->total, 2) }}
                            </th>
                        @endforeach
                    </tr>
                </tbody>
            @else
                <tbody>
                    <tr class="table-empty">
                        <td class="text-center" colspan="5">" {{ __('lang.no_list').__('purchase_orders.dealer_list') }} "</td>
                    </tr>
                </tbody>
            @endif
        </table>
    </div>
</div>
