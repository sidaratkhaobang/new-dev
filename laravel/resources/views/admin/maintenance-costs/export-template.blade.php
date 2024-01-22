<table>
    <thead>
    <tr style="border: 1px solid black;">
        <th rowspan="2">วันที่ออกใบสั่งซ่อม</th>
        <th rowspan="2">เลขที่ใบสั่งซ่อม</th>
        <th rowspan="2">ทะเบียนรถ</th>
        <th rowspan="2">จำนวนเงิน</th>
        <th rowspan="2">Code. (ศูนย์)</th>
        <th rowspan="2">ชื่อศูนย์บริการ</th>
        <th rowspan="2">เลขที่ใบแจ้งหนี้</th>
        <th rowspan="2">จำนวนเงินรวม / ศูนย์</th>
        <th rowspan="2">วันที่ รับใบแจ้งหนี้</th>
        <th rowspan="2">วันที่ กำหนดชำระ</th>
        <td rowspan="2">เลขที่ใบสรุปวางบิล</td>
        <td colspan="3">หมายเหตุ</td>
    </tr>
    <tr>
        <th scope="col">รายการ</th>
        <th scope="col">ลดหนี้ จำนวนเงินรวม (VAT)</th>
        <th>เพิ่มหนี้ จำนวนเงินรวม (VAT)</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($repair_list as $index => $d)
        @if(!empty($d?->repair_order_lines))
            @php
                $total_price = 0;
            @endphp
            @foreach($d?->repair_item as $key => $value)
                @php
                    $total_price = $total_price + $value?->price;
                    // $total_price + ($value?->price * $value?->amount) - ($value?->discount + $value?->add_debt - $value?->reduce_debt);
                @endphp
                <tr>
                    <td>
                        {{$d?->created_at ?? '-'}}
                    </td>
                    <td>
                        {{$d?->worksheet_no ?? '-'}}
                    </td>
                    <td>
                        {{$d?->car?->license_plate ?? '-'}}
                    </td>
                    <td>
                        {{$value?->price}}
                    </td>
                    <td>
                        {{$d?->creditor?->code ?? '-'}}
                    </td>
                    <td>
                        {{$d?->creditor?->name ?? '-'}}
                    </td>
                    <td>
                        {{$d?->invoice_no ?? '-'}}
                    </td>
                    <td>
                        @if($loop->last)
                            {{$total_price}}
                        @endif
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>
                        {{$value?->repair_list?->name ?? '-'}}
                    </td>
                    <td>
                        {{$value?->add_debt ?? '-'}}
                    </td>
                    <td>
                        {{$value?->reduce_debt ?? '-'}}
                    </td>
                </tr>
            @endforeach
        @endif
    @endforeach
    <tr>
        <td>
            รวม
        </td>
        <td>
            {{ number_format($repair_list->sum('total_item')) }} ชุด
        </td>
        <td>

        </td>
        <td>

        </td>
        <td>

        </td>
        <td>

        </td>
        <td>
            ยอดรวมทั้งสิ้น
        </td>
        <td>
            {{ number_format($repair_list->sum('total_price_item')) }}
        </td>
        <td>

        </td>
        <td>

        </td>
        <td>

        </td>
        <td>

        </td>
        <td>
            {{ number_format($repair_list->sum('total_add_debt')) }}
        </td>
        <td>
            {{ number_format($repair_list->sum('total_reduce_debt')) }}
        </td>
    </tr>
    </tbody>
</table>