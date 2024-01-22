<table class="table-border">
    <thead>
        <tr>
            <th class="text-left">รายการรถยนต์</th>
        </tr>
        <tr class="border-tr">
            <th style="width:1%;" class="text-left">ลำดับ</th>
            <th colspan="3" class="text-left">ยี่ห้อ/รุ่น</th>
            <th style="text-align: left;">
                @if (strcmp($print_type, 'PDF') == 0)
                    จำนวน
                @endif
            </th>
            <th style="text-align: center;">หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pr_car_list as $car_index => $item)
            @php
                $total_car += $item->amount ? $item->amount : 0;
            @endphp
            <tr>
                <td style="width:1%;" class="text-left">{{ $car_index + 1 }}</td>
                <td colspan="3" class="text-left">{{ $item->car_class_text }} <br> สี
                    &nbsp;{{ $item->car_color_text }}</td>
                <td style="text-align: left;">
                    @if (strcmp($print_type, 'PDF') == 0)
                        {{ $item->amount_car }}
                    @endif
                </td>
                <td style="text-align: center;">
                    {{ $purchase_requisition->rental_refer ? 'อ้างอิงใบจอง ' . $purchase_requisition->rental_refer : ' - ' }}
                </td>
            </tr>
            <tr class="border-tr-bottom">
                <td colspan="6">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left">รายการอุปกรณ์</th>
                            </tr>
                            <tr>
                                <th style="width:1%;" class="text-left"></th>
                                <th style="width:50%;" class="text-left">รายการอุปกรณ์เพิ่มเติม</th>
                                <th style="text-align: left;">
                                    @if (strcmp($print_type, 'PDF') == 0)
                                        จำนวน
                                    @endif
                                </th>
                                <th style="text-align: center;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (sizeof($car_accessory) > 0)
                                @php
                                    $index = 0;
                                @endphp
                                @foreach ($car_accessory as $accessories)
                                    @if ($accessories['car_index'] == $car_index)
                                        <tr>
                                            <td style="width:1%;" class="text-left">{{ $index + 1 }}</td>
                                            <td style="width:50%;" class="text-left">
                                                {{ $accessories['accessory_text'] }}
                                            </td>
                                            <td style="text-align: left;">
                                                @if (strcmp($print_type, 'PDF') == 0)
                                                    จำนวน
                                                    {{ $accessories['amount_accessory'] }}
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                {{ $accessories['remark_accessory'] }}
                                            </td>
                                        </tr>
                                        @php
                                            $index++;
                                        @endphp
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6">
                {{ $purchase_requisition->contract_refer ? 'หมายเหตุ สัญญาเลขที่' . $purchase_requisition->contract_refer : '-' }}
            </td>
        </tr>
        @if (strcmp($print_type, 'PDF') == 0)
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
                                        {{ $purchase_requisition->reviewedBy ? $purchase_requisition->reviewedBy->name : null }}
                                    </td>
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
        @endif
    </tbody>
</table>
