<table class="table-page">
    <thead>
        <tr></tr>
    </thead>
    <tbody>
        <tr>
            <td>PR No. {{ $purchase_requisition->pr_no }}</td>
            <td>วันที่ {{ get_thai_date_format($purchase_requisition->created_at, 'd/m/Y') }}</td>
            <td colspan="2">ชื่อลูกค้า {{ isset($rental_data['customer_name']) ? $rental_data['customer_name'] : ' - ' }}</td>
        </tr>
        <tr>
            <td>เลขที่ใบขอเช่า {{ isset($rental_data['worksheet_no']) ? $rental_data['worksheet_no'] : ' - ' }}</td>
            <td>วันที่ใบขอเช่า
                {{ isset($rental_data['request_date']) ? get_thai_date_format($rental_data['request_date'], 'd/m/Y') : ' - ' }}</td>
            <td>จำนวน/ฉบับ {{ count($rental_images_files) + count($quotation_files) }}</td>
            <td>จำนวน/คัน {{ $total_dealer_car_amount }}</td>
        </tr>
        <tr>
            @php
                $span = $purchase_order_dealer_list->count();
            @endphp
            <td colspan="10">
                <table>
                    <thead class="border-tr">
                        <tr>
                            <th colspan="4">List of dealer name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase_order_dealer_list as $index => $item)
                            <tr>
                                <td>รายที่ {{ $index + 1 }}</td>
                                <td class="text-left">{{ $item->creditor_text }}</td>
                                <td>วันที่ {{ get_thai_date_format($purchase_requisition->created_at, 'd/m/Y') }}</td>
                                <td>จำนวน/ฉบับ {{ count($quotation_files) }}</td>
                            </tr>
                        @endforeach
                        {{-- <br> --}}
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="10">
                <table class="table-border">
                    <thead>
                        <tr class="border-tr">
                            <th colspan="{{ $span + 5 }}">ตารางเปรียบเทียบราคาและเงื่อนไข</th>
                        </tr>
                        <tr class="border-tr-bottom">
                            <th style="border-bottom: 1px solid #010101!important;">ลำดับ</th>
                            <th style="border-bottom: 1px solid #010101!important;">รายการ</th>
                            <th class="text-right" style="border-bottom: 1px solid #010101;">จำนวน/คัน</th>
                            @if (sizeof($purchase_order_dealer_list) > 0)
                                @foreach ($purchase_order_dealer_list as $i => $d)
                                    <th class="text-right" style="border-bottom: 1px solid #010101;">รายที่ {{ $i + 1 }}</th>
                                @endforeach
                            @endif
                            <th class="text-right" style="border-bottom: 1px solid #010101;">ส่วนต่าง</th>
                            <th class="text-right" style="border-bottom: 1px solid #010101;">กำหนดส่งมอบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase_requisition_cars as $car_index => $car)
                            @php
                                $prev_price = 0;
                                $next_price = 0;
                            @endphp
                            <tr>
                                <td class="text-center" style="width:1%;">{{ $car_index + 1 }}</td>
                                <td style="width:40%;">{{ $car->model_full_name. ' - ' . $car->model }}</td>
                                <td class="text-right">{{ $car->amount }}</td>
                                @foreach ($purchase_order_dealer_list as $purchase_order_dealer_index => $purchase_order_dealer)
                                    @php
                                        if($purchase_order_dealer_index == 0) {
                                            $prev_price = $purchase_order_dealer->dealer_price_list[$car_index]->car_price * $car->amount;
                                        }
                                        if($purchase_order_dealer_index == 1) {
                                            $next_price = $purchase_order_dealer->dealer_price_list[$car_index]->car_price * $car->amount;
                                        }
                                    @endphp
                                    <td class="text-right">
                                        {{ number_format($purchase_order_dealer->dealer_price_list[$car_index]->car_price * $car->amount, 2) }}
                                    </td>
                                @endforeach
                                <td class="text-right">
                                    {{ number_format(abs($next_price - $prev_price), 2) }}
                                </td>
                                <td class="text-right">{{ $purchase_requisition->require_date ? get_thai_date_format($purchase_requisition->require_date, 'd/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                        {{-- <br> --}}
                    </tbody>
                    <tfoot>
                        <tr class="border-tr">
                            <th class="text-right" colspan="2">รวม</th>
                            <th class="text-right">{{ $total_car_amount }}</th>
                            <th colspan="{{ $span + 2 }}"></th>
                        </tr>
                        <tr>
                            <td colspan="3">สรุปพิจารณาสั่งซื้อจาก
                                {{ ($purchase_order->creditor) ? $purchase_order->creditor->name : ' - '}}
                            </td>
                            <td colspan="{{ $span }}"></td>
                            <td colspan="2">จำนวน/คัน {{ $total_dealer_car_amount }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">หมายเหตุ
                                {{ ($purchase_requisition->contract_refer) ? 'สัญญาเลขที่ '. $purchase_requisition->contract_refer : ' - '}}</td>
                            <td colspan="{{ $span }}"></td>
                            <td colspan="2">ลว {{ get_thai_date_format($purchase_order->created_at, 'd/m/Y') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </tbody>
</table>
