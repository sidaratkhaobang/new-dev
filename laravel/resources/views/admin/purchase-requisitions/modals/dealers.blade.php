<div class="modal fade" id="modal-purchase-order-dealer" role="dialog" style="overflow:hidden;"
    aria-labelledby="modal-purchase-order-dealer">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchase-order-dealer-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('purchase_orders.dealer_data') }}</h4>
                <hr>
                <div class="row push mb-5">
                    <div class="col-sm-12">
                        <x-forms.select-option id="creditor_id_field" :value="null" :list="null"
                            :label="__('purchase_orders.dealer')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true, 'required' => true]" />
                    </div>
                </div>
                <h4 class="fw-light text-gray-darker">{{ __('purchase_orders.car_price_per_one') }}</h4>
                <hr>
                <div class="table-wrap mb-5">
                    <table class="table table-striped">
                        <thead class="bg-body-dark">
                            <th>#</th>
                            <th>{{ __('purchase_orders.model') }}</th>
                            <th>{{ __('purchase_orders.color') }}</th>
                            <th>{{ __('purchase_orders.car_price') }}</th>
                            <th class="text-end">{{ __('purchase_orders.vat') }}</th>
                            <th class="text-end">{{ __('purchase_orders.vat_exclude') }}</th>
                        </thead>
                        <tbody>
                            @if (sizeof($purchase_requisition_car_list) > 0)
                                @foreach ($purchase_requisition_car_list as $index => $purchase_requisition_car)
                                    <tr id="{{$purchase_requisition_car->id}}">
                                        <td>{{ $index + 1 }}</td>
                                        <td style="width: 30%;white-space: normal;">
                                            <x-forms.tooltip :title="$purchase_requisition_car->model_full_name .
                                                ' - ' .
                                                $purchase_requisition_car->model" :limit="50">
                                            </x-forms.tooltip>
                                        </td>
                                        <td style="width: 20%;white-space: normal;">
                                            {{ $purchase_requisition_car->color }}
                                        </td>
                                        <td style="width: 20%;white-space: normal;">
                                            <input type="number" class="form-control"
                                                id="{{ $purchase_requisition_car->id }}_price_field"
                                                name="{{ $purchase_requisition_car->id }}_price_field" placeholder=""
                                                maxlength="10" required min="0">
                                        </td>
                                        <td class="text-end">
                                            <p id="{{ $purchase_requisition_car->id }}_vat">-</p>
                                        </td>
                                        <td class="text-end">
                                            <p id="{{ $purchase_requisition_car->id }}_vat_exclude_price">-</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">"
                                        {{ __('lang.no_list') . __('purchase_orders.purchase_requisition_car_detail') }}
                                        "</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <h4>{{ __('purchase_orders.upload_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'dealer_files'" :label="__('purchase_orders.attach_files')" />
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" id="modal-delete-item" class="btn btn-danger" style="display: none;"
                    onclick="deletePurchaseOrderDealer()" data-bs-dismiss="modal">{{ __('lang.delete') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="savePurchaseOrderDealer()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>