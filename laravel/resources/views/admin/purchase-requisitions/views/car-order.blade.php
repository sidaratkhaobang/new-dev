<h4>{{ __('purchase_orders.order_car_detail') }}</h4>
<hr>
<div class="row push">
    <div class="col-sm-12">
        <x-forms.input-new-line id="ordered_creditor_id" :value="$d->creditor ? $d->creditor->name : '' " :label="__('purchase_orders.dealer')" />
    </div>
</div>
<div class="mb-5">
    <div class="table-wrap">
        <table class="table table-vcenter table-hover">
            <thead class="bg-body-dark">
                <th>#</th>
                <th style="width: 30%">{{ __('purchase_orders.model') }}</th>
                <th>{{ __('purchase_orders.color') }}</th>
                <th>{{ __('purchase_orders.amount') }}</th>
                <th class="text-end">{{ __('purchase_orders.vat') }}</th>
                <th class="text-end">{{ __('purchase_orders.total_price') }}</th>
            </thead>
            <tbody>
                @php
                    $total_required_cars = 0;
                    $total_cars = 0;
                @endphp
                @if (sizeof($purchase_order_lines) > 0)
                    @foreach ($purchase_order_lines as $index => $purchase_order_line)
                        @php
                            $total_required_cars += ($purchase_order_line->amount) ? $purchase_order_line->amount : 0;
                            $total_cars += $purchase_order_line->exac_amount;
                        @endphp
                    <tr id="tr_{{ $purchase_order_line->id }}" >
                        <td>{{ $index + 1 }}</td>
                        <td style="white-space: normal;">{{ $purchase_order_line->name }}</td>
                        <td >{{ $purchase_order_line->color }}</td>
                        <td>
                            <div class="row hstack">
                                <div class="col-5">
                                    <input type="number" class="form-control input-number-car-amount"
                                    id="{{ $purchase_order_line->id }}_amount"
                                    name="selected_cars[{{ $purchase_order_line->id }}][car_amount]"
                                    placeholder="" maxlength="10" required
                                    value="{{ $purchase_order_line->amount }}" >
                                </div>
                                <div class="col-7">
                                    / {{ $purchase_order_line->exac_amount }}
                                </div>
                            </div>
                        </td>
                        <td class="vat-total text-end" id="vat_total_{{ $purchase_order_line->id }}">{{ number_format($purchase_order_line->vat, 2) }}</td>
                        <td class="price-total text-end" id="price_total_{{ $purchase_order_line->id }}">{{ number_format($purchase_order_line->total, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th style="width: 30%">{{ __('purchase_orders.summary_price') }}</th>
                        <th></th>
                        <th><span id="summary-car-amount">{{ $total_required_cars }}</span>/ {{ $total_cars }}</th>
                        <th class="summary-vat-total text-end">{{ number_format($d->vat, 2) }}</th>
                        <th class="summary-price-total text-end">{{ number_format($d->total, 2) }}</th>
                    </tr>
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="6">" {{ __('lang.no_list').__('purchase_orders.purchase_requisition_car_detail') }} "</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

