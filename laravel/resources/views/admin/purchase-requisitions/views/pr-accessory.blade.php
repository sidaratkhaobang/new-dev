<h4>{{ __('purchase_requisitions.accessories_table') }}</h4>
<hr>
<div class="table-wrap">
    <table class="table table-striped">
        <thead class="bg-body-dark">
            <th style="width: 2px;">#</th>
            <th>{{ __('purchase_requisitions.accessories') }}</th>
            {{-- <th>{{ __('purchase_requisitions.car_class') }}</th> --}}
            <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
        </thead>
        <tbody>
            @if (sizeof($car_accessory) > 0)
                @foreach ($car_accessory as $index => $accessories)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $accessories['accessory_text'] }}</td>
                        {{-- <td>{{ $accessories['accessory_version_text'] }}</td> --}}
                        <td>{{ $accessories['amount_accessory'] }} {{ __('purchase_requisitions.car_unit') }}</td>
                        <input type="hidden" name="accessories[{{ $index }}][accessory_id]" id="accessory_id"
                            value="{{ $accessories['accessory_id'] }}">
                        <input type="hidden" name="accessories[{{ $index }}][car_index]" id="car_index"
                            value="{{ $accessories['car_index'] }}">
                    </tr>
                @endforeach
            @else
                <tr class="table-empty">
                    <td class="text-center" colspan="5">"
                        {{ __('lang.no_list') . __('purchase_requisitions.accessories_table') }} "</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<br>
