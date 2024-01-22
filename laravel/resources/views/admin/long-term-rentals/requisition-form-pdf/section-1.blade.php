<table class="table-collapse">
    <thead>
        <tr>
            <th>ลำดับ</th>
            <th style="width:50%;">รายการ</th>
            <th>จำนวน</th>
            <th style="width:40%;">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        <tr>
            <td style="vertical-align: top;">1</td>
            <td style="text-align: left; vertical-align: top;">
                <p style="font-weight: bold; margin-top: 0px; margin-bottom: 0px; line-height: 14px;">
                    {{ $lt_rental_line->car_class_text }} </p>
                @if (sizeof($lt_rental_line->lt_rental_accessory) > 0)
                    @php
                        $count = count($lt_rental_line->lt_rental_accessory);
                        $index = 0;
                        $count_index = 0;
                    @endphp
                    @foreach ($lt_rental_line->lt_rental_accessory as $accessory_index => $accessory_item)
                        @if ($count_index == 0)
                            <u>อุปกรณ์เพิ่มเติม</u>
                        @endif
                        @php
                            $index++;
                            $count_index++;
                        @endphp
                        <p style="margin-top: -10px; margin-bottom: 0px;">
                            {{ $index }}. {{ $accessory_item['accessory_text'] }}</p>
                    @endforeach
                @endif
            </td>
            <td style="vertical-align: top;">
                {{ $lt_rental_line->amount }}
            </td>
            @php
                $total += $lt_rental_line->amount;
            @endphp
            <td style="text-align: left; line-height: 5px;">
                <p>สี : <span style="font-weight: bold;"> {{ $lt_rental_line->car_color_text }}</span></p>
                <p>ระยะเวลาเช่า : <span style="font-weight: bold;"> {{ $lt_rental_line->lt_rental_month }} </span> เดือน
                </p>
                <p>ค่าเช่า/เดือน/คัน : <span style="font-weight: bold;">
                    {{ number_format($lt_rental_line->lt_rental_line_month_price, 0) }}
                    </span> บาท (ไม่รวมภาษีมูลค่าเพิ่ม)
                </p>
                <p>Quotation No : <span style="font-weight: bold;"> {{ $lt_rental->quotation_no }}</span></p>
            </td>
        </tr>
        <tr style="line-height: 10px;">
            <td colspan="2">รวม</td>
            <td>{{ $total }}</td>
            <td style="text-align: left;">คัน</td>
        </tr>
    </tbody>
</table>
<p style="color: #1475c3; margin-top: 0px; font-weight: bold; font-size: 16px;">ติดต่อสอบถาม: คุณดวงดาว พารา อีเมล์: duangdaow_par@truecorp.co.th โทรศัพท์: 02-859-7801 โทรสาร: 02-859-7977</p>
