<table class="table-border"> 
    <thead>
        <tr>
            <td colspan="5" class="text-left">โปรดส่งมอบสินค้าตามรายการต่อไปนี้</td>
        </tr>
        <tr class="border-tr">
            <th style="width:2%;" class="text-center">ลำดับ</th>
            <th style="width:50%;" class="text-center">รายการ</th>
            <th style="text-align: left;">จำนวน</th>
            <th style="text-align: center; width:10%;">ราคา/หน่วย</th>
            <th style="text-align: center;">จำนวนเงิน</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchase_order_lines as $index => $item)
            <tr style="vertical-align: top;">
                <td style="width:2%;" class="text-center">{{ $index + 1 }}</td>
                <td style="width:50%;" class="text-left">{{ $item->name }} </td>
                <td class="text-center">{{ $item->amount }}</td>
                <td class="text-right">{{ number_format($item->price_per_unit, 2) }}</td>
                <td class="text-right">{{ number_format($item->price_per_unit * $item->amount, 2) }}</td>
            </tr>
            <tr style="vertical-align: top;">
                <td style="width:2%;" class="text-center"></td>
                <td style="width:50%;" class="text-left"> สี &nbsp;{{ $item->color }}</td>
                <td class="text-center"></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
            </tr>
            @if (sizeof($item->accessories) > 0)
                <tr style="vertical-align: top;">
                    <td style="width:2%;" class="text-center"></td>
                    <th style="width:50%;" class="text-left">รายการอุปกรณ์เพิ่มเติม</th>
                    <td class="text-center"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>
                @foreach ($item->accessories as $a_index => $a_item)
                    <tr style="vertical-align: top;">
                        <td style="width:2%;" class="text-center"></td>
                        <td style="width:50%;" class="text-left">
                            {{ $a_index + 1 }}. {{ $a_item->accessory ? $a_item->accessory->name : '' }}
                        </td>
                        <td class="text-center">{{ $a_item->amount }}</td>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                    </tr>
                @endforeach
            @endif
            @if (!$loop->last)
                <br>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr style="border-top:1px solid #000000">
            <td style="width:2%;"></td>
            <td style="width:50%;"></td>
            <td class="text-center"></td>
            <td class="text-right">รวมเป็นเงิน</td>
            <td class="text-right">{{ number_format($sum_subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="width:2%;"></td>
            <td style="width:50%;"></td>
            <td class="text-center"></td>
            <td class="text-right">ส่วนลด</td>
            <td class="text-right">{{ number_format($purchase_order->discount, 2) }}</td>
        </tr>
        <tr>
            <td style="width:2%;"></td>
            <td style="width:50%;"></td>
            <td class="text-center"></td>
            <td class="text-right">ภาษีมูลค่าเพ่ิม</td>
            <td class="text-right">{{ number_format($purchase_order->vat, 2) }}</td>
        </tr>
        <tr class="border-tr">
            <td style="width:2%;"></td>
            <td class="text-center" style="width:50%;">{{ bahtText($purchase_order->total) }}</td>
            <td class="text-center"></td>
            <td class="text-right">รวมเป็นเงินทั้งสิ้น</td>
            <td class="text-right">{{ number_format($purchase_order->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2">กําหนดส่งสินค้า &nbsp;&nbsp;
                {{ $purchase_order->time_of_delivery ? $purchase_order->time_of_delivery : ' - ' }}</td>
            <td class="text-center"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td colspan="2">เงื่อนไขการชําระเงิน &nbsp;&nbsp;
                {{ $purchase_order->payment_condition ? __('purchase_orders.payment_' . $purchase_order->payment_condition) : ' - ' }}
            </td>
            <td class="text-center"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td colspan="2">
                ติดต่อ &nbsp;&nbsp;
                {{ $purchase_requisition->creditor && $purchase_requisition->creditor->contact_name ? $purchase_requisition->creditor->contact_name : ' - ' }}
                @if ($purchase_requisition->creditor && $purchase_requisition->creditor->tel)
                    &nbsp;&nbsp; / เบอร์โทร &nbsp;&nbsp;
                    {{ $purchase_requisition->creditor->tel }}
                @endif
            </td>
            <td class="text-right">ผู้สั่งซื้อ&nbsp;&nbsp;</td>
            <td colspan="2" style="border-bottom: 1px solid #444444"></td>
        </tr>
        <tr>
            <td colspan="2">
                ผู้ใช้รถ &nbsp;&nbsp;
                {{ $purchase_requisition->reference && $purchase_requisition->reference->customer_name
                    ? $purchase_requisition->reference->customer_name
                    : ' - ' }}
            </td>
            <td></td>
            <td colspan="2" class="text-center">
                {{ $purchase_requisition->createdBy ? '(' . $purchase_requisition->createdBy->name . ')' : null }}
            </td>
        </tr>
    </tfoot>
</table>
