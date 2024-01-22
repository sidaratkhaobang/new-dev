<table class="table-collapse">
    <thead style="display: table-header-group">
        @php
            $span = $lt_rental_month->count();
            $span_line = $lt_rental_lines->count();
        @endphp

        <tr style="line-height: 14px;">
            <th style="width:1px;" rowspan="3">ลำดับ</th>
            <th rowspan="3" style="width:45%;">ยี่ห้อ/รุ่น</th>
            <th colspan="{{ $span }}">ค่าเช่า/บาท/คัน/เดือน</th>
            @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                <th colspan="{{ $span }}">Purchase Options</th>
            @endif
            <th rowspan="3">หมายเหตุ</th>
        </tr>
        <tr style="line-height: 14px;">
            @if ($d->check_vat == STATUS_ACTIVE)
                <th style="color: #E04F1A;" colspan="{{ $span }}" class="text-center" id="include_vat">
                    ราคารวมภาษีมูลค่าเพิ่ม
                </th>
            @else
                <th style="color: #E04F1A;" colspan="{{ $span }}" class="text-center" id="exclude_vat">
                    ราคาไม่รวมภาษีมูลค่าเพิ่ม
                </th>
            @endif
            @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                <th style="color: #E04F1A;" colspan="{{ $span }}">บาท/คัน(รวมVAT.)</th>
            @endif
        </tr>
        <tr style="line-height: 14px;">
            @if (sizeof($lt_rental_month) > 0)
                @foreach ($lt_rental_month as $i => $item_month)
                    <th>{{ $item_month->month }} เดือน</th>
                @endforeach
            @endif
            @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                @if (sizeof($lt_rental_month) > 0)
                    @foreach ($lt_rental_month as $i => $item_month)
                        <th>{{ $item_month->month }} เดือน</th>
                    @endforeach
                @endif
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($lt_rental_lines as $car_index => $car_item)
            {{-- @if ($span <= 1) --}}
            <tr style="border-bottom: none;">
                <td style="vertical-align: top;border-bottom: none;">
                    {{ $car_index + 1 }}
                </td>
                <td class="text-left" style="vertical-align: top;border-bottom: none;">
                    <p style="font-weight: bold; margin-top: 0px; margin-bottom: 0px; line-height: 14px;">
                        {{ $car_item->car_class_text }} สี{{ $car_item->car_color_text }}
                    </p>
                </td>
                {{-- <td style="vertical-align: top;border-bottom: none;"> --}}
                {{-- @dd($lt_rental_line_month) --}}
                @if (sizeof($lt_rental_line_month) > 0)
                    @foreach ($lt_rental_line_month as $item_line_month)
                        @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                            <td style="vertical-align: top;border-bottom: none;">
                                @if ($d->check_vat == STATUS_ACTIVE)
                                    {{ number_format($item_line_month->total_price, 2) }}
                                @else
                                    {{ number_format($item_line_month->subtotal_price, 2) }}
                                @endif
                        @endif
                        </td>
                    @endforeach
                @endif
                {{-- </td> --}}
                @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                    {{-- <td style="vertical-align: top;border-bottom: none;"> --}}
                    @if (sizeof($lt_rental_line_month) > 0)
                        @foreach ($lt_rental_line_month as $item_line_month)
                            @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                                <td style="vertical-align: top;border-bottom: none;">
                                    {{ number_format($item_line_month->total_purchase_options, 2) }}
                                </td>
                            @endif
                        @endforeach
                    @endif

                    {{-- </td> --}}
                @endif
                <td style="text-align: left; vertical-align: top; color: #E04F1A;border-bottom: none;">
                    - {{ $car_item->remark_quotation }}
                </td>
                {{-- </tr> --}}
                {{-- @else --}}
                {{-- <tr style="border-bottom: none;">
                    <td style="vertical-align: top;border-bottom: none;">
                        {{ $car_index + 1 }}
                    </td>
                    <td class="text-left" style="vertical-align: top;border-bottom: none;">
                        <p style="font-weight: bold; margin-top: 0px; margin-bottom: 0px; line-height: 14px;">
                            {{ $car_item->car_class_text }} สี{{ $car_item->car_color_text }}
                        </p>
                    </td>

                    @if (sizeof($lt_rental_line_month) > 0)
                        @foreach ($lt_rental_line_month as $item_line_month)
                            <td style="vertical-align: top;border-bottom: none;">
                                @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                                    @if ($d->check_vat == STATUS_ACTIVE)
                                        {{ number_format($item_line_month->total_price, 2) }}
                                    @else
                                        {{ number_format($item_line_month->subtotal_price, 2) }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    @endif

                    @if ($lt_rental->purchase_option_check == STATUS_ACTIVE)
                        @if (sizeof($lt_rental_line_month) > 0)
                            @foreach ($lt_rental_line_month as $item_line_month)
                                <td style="vertical-align: top;border-bottom: none;">
                                    @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0)
                                        {{ number_format($item_line_month->total_purchase_options, 2) }}
                                    @endif
                                </td>
                            @endforeach
                        @endif
                    @endif
                    <td style="text-align: left; vertical-align: top; color: #E04F1A;border-bottom: none;">
                        - {{ $car_item->remark_quotation }}
                    </td> --}}

                {{-- @endif --}}
                @if (sizeof($lt_rental_accessory) > 0)
                    @php
                        $count = count($lt_rental_accessory);
                        // dd($count);
                        $index = 0;
                        $count_index = 0;
                    @endphp
                    @foreach ($lt_rental_accessory as $accessory_index => $accessory_item)
                        @if ($accessory_item['car_index'] == $car_index)
            <tr class="">
                <td class="table-no-border" style="vertical-align: top;">
                </td>
                <td class="table-no-border text-left" style=";vertical-align: top;">
                    @if ($count_index == 0)
                        <span style="color: #E04F1A;">อุปกรณ์เสริมที่มากับรถ</span>
                    @endif
                    @php
                        $index++;
                        $count_index++;
                    @endphp
                    <p style="margin-top: -10px; margin-bottom: 0px;">
                        {{ $index }}. {{ $accessory_item['accessory_text'] }}</p>
                </td>

                @foreach($lt_rental_month as $item)
                <td class="table-no-border" style="vertical-align: top;">
                </td>
                @endforeach

                @foreach($lt_rental_month as $item)
                <td class="table-no-border" style="vertical-align: top;">
                </td>
                @endforeach
                <td class="table-no-border" style="vertical-align: top;">
                </td>
                {{-- <td class="table-no-border" style="vertical-align: top;">
                </td> --}}
        @endif
        </tr>
        @endforeach
        @endif
        {{-- </tr> --}}
        {{--                        <tr> --}}
        {{--                            <td style="vertical-align: top;"> --}}
        {{--                                {{ $car_index + 1 }} --}}
        {{--                            </td> --}}
        {{--                            <td style="text-align: left;"> --}}
        {{--                                <p style="font-weight: bold; margin-top: 0px; margin-bottom: 0px; line-height: 14px;"> --}}
        {{--                                    {{ $car_item->car_class_text }} สี{{$car_item->car_color_text}}</p> --}}
        {{--                                @if (sizeof($lt_rental_accessory) > 0) --}}
        {{--                                    @php --}}
        {{--                                        $count = count($lt_rental_accessory); --}}
        {{--                                        $index = 0; --}}
        {{--                                        $count_index = 0; --}}
        {{--                                    @endphp --}}

        {{--                                    @foreach ($lt_rental_accessory as $accessory_index => $accessory_item) --}}
        {{--                                        @if ($accessory_item['car_index'] == $car_index) --}}
        {{--                                            @if ($count_index == 0) --}}
        {{--                                                <span style="color: #E04F1A;">อุปกรณ์เสริมที่มากับรถ</span> --}}
        {{--                                            @endif --}}
        {{--                                            @php --}}
        {{--                                                $index++; --}}
        {{--                                                $count_index++; --}}
        {{--                                            @endphp --}}
        {{--                                            <p style="margin-top: -10px; margin-bottom: 0px;"> --}}
        {{--                                                {{ $index }}. {{ $accessory_item['accessory_text'] }}</p> --}}
        {{--                                        @endif --}}
        {{--                                    @endforeach --}}
        {{--                                @endif --}}
        {{--                            </td> --}}
        {{--                            @if (sizeof($lt_rental_line_month) > 0) --}}
        {{--                                @foreach ($lt_rental_line_month as $item_line_month) --}}
        {{--                                    @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0) --}}
        {{--                                        <td style="vertical-align: top;">@if ($d->check_vat == STATUS_ACTIVE) --}}
        {{--                                                {{ number_format($item_line_month->total_price, 2)  }} --}}
        {{--                                            @else --}}
        {{--                                                {{ number_format($item_line_month->subtotal_price, 2) }} --}}
        {{--                                            @endif</td> --}}
        {{--                                    @endif --}}
        {{--                                @endforeach --}}
        {{--                            @endif --}}
        {{--                            @if ($lt_rental->purchase_option_check == STATUS_ACTIVE) --}}
        {{--                                @if (sizeof($lt_rental_line_month) > 0) --}}
        {{--                                    @foreach ($lt_rental_line_month as $item_line_month) --}}
        {{--                                        @if (strcmp($item_line_month->lt_rental_line_id, $car_item->id) == 0) --}}
        {{--                                            <td style="vertical-align: top;">{{ number_format($item_line_month->total_purchase_options, 2) }} --}}
        {{--                                            </td> --}}
        {{--                                        @endif --}}
        {{--                                    @endforeach --}}
        {{--                                @endif --}}
        {{--                            @endif --}}
        {{--                            <td style="text-align: left; vertical-align: top; color: #E04F1A;">- {{$car_item->remark_quotation}}</td> --}}
        {{--                             @if ($loop->first) --}}
        {{--                            <td rowspan="{{$span_line}}" style="text-align: left; vertical-align: top; color: #E04F1A;"> - {{ $lt_rental->quotation_remark }}</td> --}}
        {{--                            @endif --}}
        {{--                        </tr> --}}
        </tr>
        @endforeach
    </tbody>
</table>
