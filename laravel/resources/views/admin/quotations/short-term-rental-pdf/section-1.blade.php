<table class="table-collapse">
    <thead>
        <tr style="line-height: 14px;">
            <th style="width:1px;" rowspan="2">ลำดับ</th>
            <th colspan="2" style="width:20%;">วันที่</th>
            <th rowspan="2" style="width:35%; text-align: left;">สินค้า</th>
            <th rowspan="2" style="text-align: right;">จำนวน</th>
            <th rowspan="2" style="width:14%; text-align: center;">ระยะเวลา</th>
            <th rowspan="2" style="text-align: right;">ค่าเช่า/คัน</th>
            <th rowspan="2" style="text-align: right;">ยอดรวม</th>
        </tr>
        <tr>
            <th style="width:10%;">รับ</th>
            <th style="width:10%;">คืน</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
            $count_rental_line = count($rental_lines);
        @endphp

        @if ($rental_lines)
            @php
                $class_border = $count_rental_line > 1 ? 'grid_border' : '';
            @endphp
            @foreach ($rental_lines as $index => $item_line)
                @php
                    $last_item = $loop->last ? 'lastborder' : '';
                @endphp
                <tr style="line-height: 13px;">
                    @if ($loop->first)
                        <td rowspan="{{ $count_rental_line }}" style="vertical-align: top;">{{ $index + 1 }}</td>
                        <td rowspan="{{ $count_rental_line }}" style="vertical-align: top;">
                            {{ $item_line->pickup_date ? get_thai_date_format($item_line->pickup_date, 'd/m/Y') : '-' }}
                            <br>
                            จุดรับรถ: {{ $origin_name }}
                        </td>
                        <td rowspan="{{ $count_rental_line }}" style="vertical-align: top;">
                            {{ $item_line->return_date ? get_thai_date_format($item_line->return_date, 'd/m/Y') : '-' }}
                            <br>
                            จุดคืนรถ: {{ $destination_name }}
                        </td>
                    @endif

                    <td style="width:35%; text-align: left; vertical-align: top;"
                        class="pd-l10 {{ $class_border }} {{ $last_item }}">
                        {{ $item_line->product_name }}
                        @if ($item_line->package_name)
                            ({{ $item_line->package_name }})
                        @endif
                    </td>

                    <td style="text-align: right; vertical-align: top;"
                        class="pd-r10 {{ $class_border }} {{ $last_item }}">
                        {{ $item_line->amount }}
                    </td>

                    <td style="text-align: center; vertical-align: top;"
                        class="pd-r10 {{ $class_border }} {{ $last_item }}">
                        {{ $item_line->rental_date }}
                    </td>

                    <td style="text-align: right; vertical-align: top;"
                        class="{{ $class_border }} {{ $last_item }}">
                        {{ number_format($item_line->total, 2) }}
                    </td>
                    @if ($loop->first)
                        <td style="vertical-align: top; text-align: right;" rowspan="{{ $count_rental_line }}">
                            {{ number_format($d->total, 2) }}
                        </td>
                    @endif
                </tr>
            @endforeach
        @endif
        @if ($rental->discount > 0 || $rental->coupon_discount > 0)
            <tr style="line-height: 13px;">
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: left;">
                    @if ($rental->discount > 0)
                        ส่วนลด Promotion
                    @endif
                    <br>
                    @if ($rental->coupon_discount > 0)
                        ส่วนลด Voucher
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;">
                    @if ($rental->discount > 0)
                        {{ number_format($rental->discount, 2) }}
                    @endif
                    <br>
                    @if ($rental->coupon_discount > 0)
                        {{ number_format($rental->coupon_discount, 2) }}
                    @endif
                </td>
            </tr>
        @endif
        <tr style="line-height: 13px;">
            <th @if (strcmp($d->check_withholding_tax, 1) === 0) rowspan="3" @else rowspan="2" @endif colspan="4"
                style="text-align: left;" class="pd-l10">
                หมายเหตุ <span style="color: #E04F1A;"> {{ $rental->rental_remark }}</span>
            </th>
            <th @if (strcmp($d->check_withholding_tax, 1) === 0) rowspan="3" @else rowspan="2" @endif>

            </th>
            <th @if (strcmp($d->check_withholding_tax, 1) === 0) rowspan="3" @else rowspan="2" @endif>

            </th>
            <th style="text-align: left;">
                รวมเงิน
            </th>
            <td style="text-align: right;">
                {{ number_format($d->subtotal, 2) }}
            </td>
        </tr>
        @if (strcmp($d->check_withholding_tax, 1) === 0)
            <tr style="line-height: 13px;">
                <th style="text-align: left;">ภาษี ณ ที่จ่าย</th>
                <td style="text-align: right;">{{ number_format($d->withholding_tax, 2) }}</td>
            </tr>
        @endif
        <tr style="line-height: 13px;">
            <th style="text-align: left;">ภาษีมูลค่าเพิ่ม 7%</th>
            <td style="text-align: right;">{{ number_format($d->vat, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4">{{ bahtText($d->total) }}</td>
            <td></td>
            <td></td>
            <th style="text-align: left;">จำนวนเงินทั้งสิ้น</th>
            <td style="text-align: right;">{{ number_format($d->total, 2) }}</td>
        </tr>
    </tbody>
</table>
