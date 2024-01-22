<div class="col-sm-4 mb-4">
    <x-forms.radio-inline id="purchase_option_check" :value="$d->purchase_option_check == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_DEFAULT" :list="$listStatus" :label="__('long_term_rentals.need_wreck_price')"
        :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
</div>

{{-- <div class="col-sm-1 mb-3">
    {{ __('long_term_rentals.need_wreck_price') }}
</div>
<div class="col-sm-2 mb-4">
    <input type="radio" id="purchase_option" class="form-check-input radio" name="purchase_option"
        value="1" @if (1 == 1) checked @endif> ต้องการ &emsp;
    <input type="radio" id="purchase_option" class="form-check-input radio" name="purchase_option"
        value="0" @if (1 == 0) checked @endif> ไม่ต้องการ
</div> --}}
<h4 id="wreck_topic">{{ __('long_term_rentals.wreck_price') }}</h4>
<hr id="wreck_hr">
<div class="table-wrap" id="wreck_table">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th>{{ __('long_term_rentals.car_class_select') }}</th>
            @foreach ($lt_rental_month as $index_month => $month)
                <th @if (isset($view)) class="text-end" @endif>{{ __('long_term_rentals.wreck_fee') }}
                    {{ $month->month }} {{ __('long_term_rentals.month') }}<br>{{__('long_term_rentals.include_vat')}}</th>
            @endforeach
        </thead>
        <tbody>
            @if (sizeof($lt_rental_list) > 0)
                @foreach ($lt_rental_list as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->model_full_name }}</td>

                        @foreach ($item->months as $index => $item2)
                            <td class="text-end">
                                @if (!isset($view))
                                    <input type="text" class="form-control number-format"
                                        id="purchase_options[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        name="purchase_options[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        placeholder="" value="{{ number_format($item2['total_purchase_options'],2) }}">
                                @else
                                    {{ number_format($item2['total_purchase_options'], 2) }}
                                    <input type="hidden" class="form-control"
                                        id="purchase_options[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        name="purchase_options[{{ $item->lt_rental_line_id }}][{{ $item2['lt_rental_month_id'] }}]"
                                        placeholder="" value="{{ $item2['total_purchase_options'] }}">
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr class="text-end">
                    <th></th>
                    <th colspan="1">สรุปราคารวมทั้งหมด</th>
                    @foreach ($lt_rental_month as $index_month => $month)
                        <th id="sum_purchase_options[{{ $month->id }}]"></th>
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
        function sumPriceOption() {
            var lt_rental_list = @json($lt_rental_list);
            var sum = [];
            var lt_rental_month = @json($lt_rental_month);
            lt_rental_month.forEach(function(list) {
                sum = 0;
                lt_rental_list.forEach(function(list2) {
                    var price = $('#purchase_options\\[' + list2.lt_rental_line_id + '\\]\\[' + list.id +
                        '\\]').val();
                    var price = parseFloat(price.replace(/,/g, ''));
                    if (price.length === 0) {
                        price = 0;
                    }
                    sum += isNaN(parseFloat(price)) ? '0.00' : parseFloat(price) ;
                });
                $('#sum_purchase_options\\[' + list.id + '\\]').html(parseFloat(sum).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });
        }
        sumPriceOption();
        $(document).keyup(function(event) {
            sumPriceOption();
        });
    </script>
@endpush
