<h4>{{ __('long_term_rentals.dealer_price') }}</h4>
<hr>
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th>{{ __('long_term_rentals.car_class_select') }}</th>
            <th>{{ __('long_term_rentals.car_color') }}</th>
            <th>{{ __('long_term_rentals.car_amount') }}</th>
            <th class="text-end">{{ __('long_term_rentals.order_price') }}</th>
        </thead>
        <tbody>
            @if (sizeof($compare_price_list) > 0)
                @foreach ($compare_price_list as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->model_full_name }}</td>
                        <td>{{ $item->color }}</td>
                        <td>{{ $item->amount }}</td>
                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“
                        {{ __('lang.no_list') . __('long_term_rentals.dealer_price') }} “</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
