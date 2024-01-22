<br>
<h4>{{ __('quotations.rental_table') }}</h4>
<div class="col-sm-4 mb-4">
    <x-forms.radio-inline id="check_vat" :value="$d->check_vat == STATUS_ACTIVE ? STATUS_ACTIVE : STATUS_DEFAULT" :list="$listStatus" :label="__('quotations.show_vat')"
        :optionals="['required' => true, 'input_class' => 'col-sm-6 input-pd']" />
</div>
<div class="table-wrap">
    <table class="table table-bordered">
        <thead>
            @php
                $span = $lt_rental_month->count();
                $span_line = $lt_rental_lines->count();
            @endphp

            <tr>
                <th style="width:1px;" rowspan="3" class="text-center">ลำดับ</th>
                <th rowspan="3" style="width:30%;" class="text-center">ยี่ห้อ/รุ่น</th>
                <th rowspan="3" class="text-center">ทั้งหมด</th>
                <th colspan="{{ $span }}" class="text-center">ค่าเช่า/บาท/คัน/เดือน</th>

                @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                    <th colspan="{{ $span }}" class="text-center">Purchase Options</th>
                @endif
                <th rowspan="3" class="text-center">หมายเหตุ</th>

            </tr>
            <tr>
                {{-- @if($d->check_vat == STATUS_ACTIVE) --}}
                    <th style="color: #E04F1A;" colspan="{{ $span }}" class="text-center" id="include_vat">ราคารวมภาษีมูลค่าเพิ่ม
                    </th>
                {{-- @else --}}
                <th style="color: #E04F1A;" colspan="{{ $span }}" class="text-center" id="exclude_vat">ราคาไม่รวมภาษีมูลค่าเพิ่ม
                </th>
                {{-- @endif --}}
               
                @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                    <th style="color: #E04F1A;" colspan="{{ $span }}" class="text-center">บาท/คัน(รวมVAT.)</th>
                @endif
            </tr>
            <tr>
                @if (sizeof($lt_rental_month) > 0)
                    @foreach ($lt_rental_month as $i => $item_month)
                        <th class="text-center">{{ $item_month->month }} เดือน</th>
                    @endforeach
                @endif
                @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                    @if (sizeof($lt_rental_month) > 0)
                        @foreach ($lt_rental_month as $i => $item_month)
                            <th class="text-center">{{ $item_month->month }} เดือน</th>
                        @endforeach
                    @endif
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($lt_rental_lines as $car_index => $car_item)
                <tr>
                    <td>
                        {{ $car_index + 1 }}
                    </td>
                    <td style="text-align: left;">
                        {{ $car_item->car_class_text }}
                    </td>
                    <td style="text-align: left;">
                        {{ $car_item->amount }}
                    </td>
                    @if (sizeof($lt_rental_line_month) > 0)
                        @foreach ($lt_rental_line_month as $index => $item_line_month)
                            @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                                {{-- <td id="price{{$index}}" >{{ number_format($item_line_month->subtotal_price, 2) }}</td> --}}
                                <td id="price{{$index}}" ></td>
                            @endif
                        @endforeach
                    @endif
                    @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                    @if (sizeof($lt_rental_line_month) > 0)
                        @foreach ($lt_rental_line_month as $item_line_month)
                            @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                                <td>
                                    {{ number_format($item_line_month->total_purchase_options, 2) }}
                                </td>
                            @endif
                        @endforeach
                    @endif
                    @endif
                    @if($loop->first)
                    <td rowspan="{{$span_line}}" style="text-align: left; vertical-align: top; color: #E04F1A;"> - {{ $lt_rental->quotation_remark }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
