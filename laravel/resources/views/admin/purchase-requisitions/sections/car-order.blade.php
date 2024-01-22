<h4>{{ __('purchase_orders.order_car_detail') }}</h4>
<hr>
<div class="row push">
    <div class="col-sm-12">
        <x-forms.select-option id="ordered_creditor_id" :value="null" :list="null" :label="__('purchase_orders.dealer')"/>
    </div>
</div>
<div class="mb-5">
    <div class="table-wrap">
        <table class="table table-vcenter table-hover">
            <thead class="bg-body-dark">
                <th class="text-center">
                    <div class="form-check d-inline-block">
                      <input class="form-check-input" type="checkbox" value="" id="check-all" name="check-all">
                      <label class="form-check-label" for="check-all"></label>
                    </div>
                </th>
                <th style="width: 2px;">#</th>
                <th style="width: 10px;">{{ __('purchase_orders.model') }}</th>
                <th style="width: 100px;">{{ __('purchase_orders.color') }}</th>
                <th style="width: 100px;">{{ __('purchase_orders.amount') }}</th>
                <th style="width: 100px;" class="text-end">{{ __('purchase_orders.vat') }}</th>
                <th style="width: 100px;"class="text-end">{{ __('purchase_orders.total_price') }}</th>
            </thead>
            <tbody>
                @php
                    $total_cars = 0;
                @endphp
                @if (sizeof($purchase_requisition_car_list) > 0)
                    @foreach ($purchase_requisition_car_list as $index => $purchase_requisition_car)
                    @php
                       $total_cars +=  $purchase_requisition_car->amount;
                    @endphp
                    <tr id="tr_{{ $purchase_requisition_car->id }}" >
                        <td class="text-center" style="width:1%;white-space: normal;">
                            <div class="form-check d-inline-block">
                              <input class="form-check-input form-check-input-each" type="checkbox" value="" id="row_{{ $purchase_requisition_car->id }}" name="row_{{ $purchase_requisition_car->id }}" >
                              <label class="form-check-label" for="row_{{ $purchase_requisition_car->id }}"></label>
                            </div>
                        </td>
                        <td style="width: 2%;white-space: normal;">{{ $index + 1 }}</td>
                        <td style="white-space: normal;">
                            <x-forms.tooltip
                                :title="$purchase_requisition_car->model_full_name . ' - ' . $purchase_requisition_car->model" 
                                :limit="50"></x-forms.tooltip>
                        </td>
                        <input type="hidden" value="{{ $purchase_requisition_car->model_full_name . ' - ' . $purchase_requisition_car->model }}" id="selected_cars[{{ $purchase_requisition_car->id }}][name]" 
                            name="selected_cars[{{ $purchase_requisition_car->id }}][name]">
                        <td style="width: 100px; white-space: normal;">{{ $purchase_requisition_car->color }}</td>
                        <td style="width: 30%;">
                            <div class="row hstack">
                                <div class="col-sm-6">
                                    <input type="number" class="form-control input-number-car-amount" 
                                    id="{{ $purchase_requisition_car->id }}_amount" 
                                    name="selected_cars[{{ $purchase_requisition_car->id }}][car_amount]" 
                                    data-id="{{ $purchase_requisition_car->id }}"
                                    placeholder="" maxlength="10" required 
                                    min="0" max="{{ $purchase_requisition_car->amount }}" >
                                </div>
                                <div class="col-sm-6">
                                    / {{ $purchase_requisition_car->amount }}
                                </div>
                            </div>
                        </td>
                        <td style="width: 100px;" class="vat-total text-end" id="vat_total_{{ $purchase_requisition_car->id }}">-</td>
                        <input type="hidden" value="" id="selected_cars[{{ $purchase_requisition_car->id }}][vat]" 
                            name="selected_cars[{{ $purchase_requisition_car->id }}][vat]">
                        <td style="width: 100px;" class="price-total text-end" id="price_total_{{ $purchase_requisition_car->id }}">-</td>
                        <input type="hidden" value="" id="selected_cars[{{ $purchase_requisition_car->id }}][price]"
                            name="selected_cars[{{ $purchase_requisition_car->id }}][price]">
                        <input type="hidden" value="{{ $purchase_requisition_car->model_id }}" id="selected_cars[{{ $purchase_requisition_car->id }}][car_class_id]" 
                            name="selected_cars[{{ $purchase_requisition_car->id }}][car_class_id]">
                    </tr>
                    @endforeach
                    <tr>
                        <th></th>
                        <th></th>
                        <th style="width: 30%">{{ __('purchase_orders.summary_price') }}</th>
                        <th></th>
                        <th><span id="summary-car-amount">-</span>/ {{ $total_cars }}</th>
                        <th class="summary-vat-total text-end">-</th>
                        <th class="summary-price-total text-end">-</th>
                        <input type="hidden" value="" id="summary_vat_total" name="summary_vat_total" >
                        <input type="hidden" value="" id="summary_price_total" name="summary_price_total" >
                    </tr>
                @else
                <tr class="table-empty">
                    <td class="text-center" colspan="4">" {{ __('lang.no_list').__('purchase_orders.purchase_requisition_car_detail') }} "</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

