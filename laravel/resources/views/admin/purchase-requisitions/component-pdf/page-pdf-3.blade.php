<table>
    <thead>
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
            <td colspan="4">
                <table>
                    <thead class="border-tr">
                        <tr>
                            <th colspan="4">ตารางเปรียบเทียบราคาและเงื่อนไข</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td valign="top">รายการอุปกรณ์เพิ่มเติม</td>
                            <td class="text-left">
                                @if (sizeof($car_accessory) > 0)
                                    @foreach ($car_accessory as $index => $accessories)
                                        {{ $index + 1 }}.
                                        {{ $accessories['accessory_text'] }}
                                        <br>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <footer>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%;">ผู้ขอซื้อ</th>
                                <th style="width: 50%;">ผู้อนุมัติ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="separated" colspan="1"></td>
                                <td class="separated" colspan="1"></td>
                            </tr>
                            <tr>
                                <td style="text-align: center">
                                    {{ $purchase_requisition->createdBy->name }}</td>
                                <td style="text-align: center">
                                    {{ $purchase_requisition->reviewedBy->name }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center">
                                    {{ $purchase_requisition->createdBy && $purchase_requisition->createdBy->department ? $purchase_requisition->createdBy->department->name : null }}
                                </td>
                                <td style="text-align: center">
                                    {{ $purchase_requisition->reviewedBy && $purchase_requisition->reviewedBy->department ? $purchase_requisition->reviewedBy->department->name : null }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </footer>
            </td>
        </tr>
    </tbody>
</table>
