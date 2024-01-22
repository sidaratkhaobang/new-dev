<table>
    <thead>
        <tr style="border: 1px solid black;">
            <th rowspan="2">ที่</th>
            <th rowspan="2">Lot No.</th>
            <th rowspan="2">PO No.</th>
            <th rowspan="2">Dealer</th>
            <th rowspan="2">รุ่นรถ</th>
            <th rowspan="2">ซีซี</th>
            <th rowspan="2">สี</th>
            <th rowspan="2">เลขเครื่อง</th>
            <th rowspan="2">เลขตัวถัง</th>
            <th rowspan="2">วันรับรถ</th>
            <th rowspan="2">
                ราคารถ
            </th>
            <th rowspan="2">
                ราคาอุปกรณ์
            </th>
            <th rowspan="2">
                Finance
            </th>
            <th rowspan="2">
                วันเริ่มสัญญา / วันจ่าย Dealer
            </th>
            <th rowspan="2">
                วันที่ชำระ งวดแรก
            </th>
            <th rowspan="2">
                วันที่ชำระ งวดสุดท้าย
            </th>
            <th rowspan="2">
                Period
            </th>
            <th rowspan="2">
                Rate
            </th>
            <th rowspan="2">
                ค่างวด (รวม ภาษีมูลค่าเพิ่ม)
            </th>
            <th colspan="2">RV</th>
            <th rowspan="2">
                เลขที่สัญญา
            </th>
            <th rowspan="2">
                วันที่ เตรียมข้อมูล
            </th>
            <th>
                อุปกรณ์
            </th>
            <th rowspan="2">
                ผู้เช่า
            </th>
            <th rowspan="2">
                หมายเหตุ การซื้อรถ
            </th>
            <th rowspan="2">
                วันนัด
                ส่งมอบรถ
            </th>
            <th rowspan="2">
                ค่าเช่า (ไม่รวม VAT)
            </th>
            <th rowspan="2">
                ระยะ เวลาเช่า
            </th>
        </tr>
        <tr>
            <th>(รวม ภาษีมูลค่าเพิ่ม)</th>
            <th></th>
            <th>เงินสด</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($finance_data as $index => $d)
        <tr>
            <td>
                {{++$index}}
            </td>
            <td>
                {{$d?->insurance_lot?->lot_no}}
            </td>
            <td>
                {{$d?->purchase_order?->po_no}}
            </td>
            <td>
                {{$d?->purchase_order?->creditor?->name}}
            </td>
            <td>
                {{$d?->car?->carClass?->full_name}}
            </td>
            <td>
                {{$d?->car?->carClass?->engine_size}}
            </td>
            <td>
                {{$d?->car?->carClass?->carColor?->name}}
            </td>
            <td>
                {{$d?->car?->carClass?->engine_no}}
            </td>
            <td>
                {{$d?->car?->carClass?->chassis_no}}
            </td>
            <td>
                {{$d?->pr_line?->delivery_date}}
            </td>
            <td>
                {{$d?->po?->total}}
            </td>
            <td>
                @if($d?->type_car_financing == FinanceCarStatusEnum::CAR_AND_ACCESSORY)
                {{number_format($d?->accessory_total_with_vat ?? 0)}}
                @endif
            </td>
            <td>
                {{$d?->insurance_lot?->creditor?->name}}
            </td>
            <td>
                {{$d?->contract_start_date}}
            </td>
            <td>{{$d?->first_payment_date}}</td>
            <td>{{$d?->contract_end_date}}</td>
            <td>{{$d?->number_installments}}</td>
            <td>{{$d?->interest_rate_percent}}</td>
            <td>{{$d?->amount_installments}}</td>
            <td>{{$d?->rv_car_percent ?? 0}}%</td>
            <td>{{$d?->rv_price ?? 0}}</td>
            <td>{{$d?->contract_no}}</td>
            <td>{{optional($d->created_at)->format('Y-m-d') ?? '-'}}</td>
            <td>
                @if($d?->type_car_financing == FinanceCarStatusEnum::CAR)
                {{number_format($d?->accessory_total_with_vat ?? 0)}}
                @endif
            </td>
            <td>{{$d?->customer_data?->customer?->name}}</td>
            <td>{{$d?->rental_remark}}</td>
            <td>{{$d?->delivery_date}}</td>
            <td>{{$d?->rental_price}}</td>
            <td>{{$d?->rental_duration}}</td>
        </tr>
        @endforeach
    </tbody>
</table>