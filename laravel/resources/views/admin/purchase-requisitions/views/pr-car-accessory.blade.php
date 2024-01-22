<h4>{{ __('purchase_requisitions.data_car_table') }}</h4>
<hr>
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 30px"></th>
            <th style="width: 2px;">#</th>
            <th>{{ __('purchase_requisitions.car_class') }}</th>
            <th>{{ __('purchase_requisitions.car_color') }}</th>
            <th>{{ __('purchase_requisitions.car_amount') }}</th>
        </thead>
        <tbody>
            @php
                $total_car = 0;
            @endphp
            @if (sizeof($pr_car_list) > 0)
                @foreach ($pr_car_list as $car_index => $pr_car)
                    <tr>
                        <td class="text-center toggle-table" style="width: 30px">
                            <i class="fa fa-angle-right text-muted"></i>
                        </td>
                        <td>{{ $car_index + 1 }}</td>
                        <td style="white-space: normal;">
                            <x-forms.tooltip :title="$pr_car->car_class_text" :limit="60"></x-forms.tooltip>
                        </td>
                        <td>{{ $pr_car->car_color_text }}</td>
                        <td>{{ $pr_car->amount_car }} {{ __('purchase_requisitions.car_unit') }}</td>
                    </tr>
                    <tr style="display: none;">
                        <td></td>
                        <td class="td-table" colspan="5">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <th style="width: 2px;">#</th>
                                    <th>{{ __('purchase_requisitions.accessories') }}</th>
                                    <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                                    <th>{{ __('purchase_requisitions.remark') }}</th>
                                </thead>
                                <tbody>
                                    @if (sizeof($car_accessory) > 0)
                                        @php
                                            $index = 0;
                                        @endphp
                                        @foreach ($car_accessory as $accessories)
                                            @if ($accessories['car_index'] == $car_index)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $accessories['accessory_text'] }}</td>
                                                    <td>{{ $accessories['amount_accessory'] }}</td>
                                                    <td>{{ $accessories['remark_accessory'] }}</td>
                                                </tr>
                                                @php
                                                    $index++;
                                                @endphp
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    @php
                        $total_car += $pr_car->amount_car;
                    @endphp
                @endforeach
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ __('purchase_requisitions.summary_car_detail') }}</th>
                    <th></th>
                    <th>{{ $total_car }} {{ __('purchase_requisitions.car_unit') }}</th>
                </tr>
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">"
                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
