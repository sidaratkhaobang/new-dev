<h4>{{ __('long_term_rentals.car_table') }}</h4>
<hr>
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 30px"></th>
            <th style="width: 2px;">#</th>
            <th>{{ __('long_term_rentals.car_class') }}</th>
            <th>{{ __('long_term_rentals.car_color') }}</th>
            <th>{{ __('long_term_rentals.car_amount') }}</th>
        </thead>
        <tbody>
            @php
                $total_car = 0;
            @endphp
            @if (sizeof($car_list) > 0)
                @foreach ($car_list as $car_index => $car)
                    <tr>
                        <td class="text-center toggle-table" style="width: 30px">
                            <i class="fa fa-angle-right text-muted"></i>
                        </td>
                        <td>{{ $car_index + 1 }}</td>
                        <td>{{ $car->car_class_text }}</td>
                        <td>{{ $car->car_color_text }}</td>
                        <td>{{ $car->amount_car }} {{ __('long_term_rentals.car_unit') }}</td>
                    </tr>
                    <tr style="display: none;">
                        <td></td>
                        <td class="td-table" colspan="5">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                    <th style="width: 2px;">#</th>
                                    <th>{{ __('long_term_rentals.accessories') }}</th>
                                    <th>{{ __('long_term_rentals.amount_accessory') }}</th>
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
                        $total_car += $car->amount_car;
                    @endphp
                @endforeach
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ __('long_term_rentals.summary_car_detail') }}</th>
                    <th></th>
                    <th>{{ $total_car }} {{ __('long_term_rentals.car_unit') }}</th>
                </tr>
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">"
                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
