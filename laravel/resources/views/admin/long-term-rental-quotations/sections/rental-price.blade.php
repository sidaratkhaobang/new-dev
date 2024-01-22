<h4>{{ __('long_term_rentals.rental_price') }}</h4>
<hr>
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th>{{ __('long_term_rentals.car_class_select') }}</th>
            @foreach ($lt_rental_month as $index_month => $month)
                <th @if (isset($view)) class="text-end" @endif>{{ __('long_term_rentals.rental_fee') }}
                    {{ $month->month }} {{ __('long_term_rentals.month') }}<br>{{__('long_term_rentals.exclude_vat')}}</th>
            @endforeach
        </thead>
        <tbody>
            @if (sizeof($lt_rental_list) > 0)
                @foreach ($lt_rental_list as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->model_full_name }}</td>

                        @foreach ($item->months as $index_x => $item2)
                            <td class="text-end">
                                @if (!isset($view))
                                    <input type="text" class="form-control number-format"
                                        id="price[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        name="price[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        placeholder="" value="{{ number_format($item2['subtotal_price'],2) }}">
                                @else
                                    {{ number_format($item2['subtotal_price'], 2) }}
                                    <input type="hidden" class="form-control"
                                        id="price[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        name="price[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        placeholder="" value="{{ $item2['subtotal_price'] }}">
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr class="text-end">
                    <th></th>
                    <th colspan="1">สรุปราคารวมทั้งหมด</th>
                    @foreach ($lt_rental_month as $index_month => $month)
                        <th id="sum[{{ $month->id }}]"></th>
                    @endforeach
                </tr>
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">“
                        {{ __('lang.no_list') . __('long_term_rentals.rental_price') }} “</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>

@push('scripts')
    <script>
        function sumPrice() {
            var lt_rental_list = @json($lt_rental_list);
            var sum = [];
            var lt_rental_month = @json($lt_rental_month);
            lt_rental_month.forEach(function(list) {
                sum = 0;
                lt_rental_list.forEach(function(list2) {
                    var price = $('#price\\[' + list2.lt_rental_line_id + '\\]\\[' + list.id + '\\]').val();
                    var price = parseFloat(price.replace(/,/g, ''));
                    if (price.length === 0) {
                        price = 0;
                        
                    }
                    sum += isNaN(parseFloat(price)) ? '0.00' : parseFloat(price) ;
                   
                });
                $('#sum\\[' + list.id + '\\]').html(parseFloat(sum).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });
        }
        sumPrice();

        $(document).keyup(function(event) {
            sumPrice();
        });
    </script>
@endpush
