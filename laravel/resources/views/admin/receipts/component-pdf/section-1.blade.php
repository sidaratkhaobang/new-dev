<table class="table-border">
    <thead>
        <tr class="border-tr">
            <th style="width:3%;" class="text-left">ลำดับ</th>
            <th class="text-left">อ้างอิงเอกสารเลขที่</th>
            <th class="text-left">วันที่</th>
            <th class="text-left" colspan="2">รายการ</th>
            <th class="text-right">จำนวนเงิน(บาท)</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-tr-bottom">
            <td style="width:3%;" class="text-left">001</td>
            <td class="text-left"></td>
            <td class="text-left">{{ ($d->created_at) ? get_thai_date_format($d->created_at, 'd/m/Y') : '-' }}</td>
            <td class="text-left" colspan="2">{{ __('receipts.receipt_type_' . $d->receipt_type) }}</td>
            <td class="text-right">{{ number_format($d->subtotal, 2) }}</td>
        </tr>
        <tr style="line-height: 13px;">
            <th rowspan="3" colspan="4"></th>
            <th class="text-left" style="padding-left: 50px;">รวมมูลค่า</th>
            <td class="text-right">{{ number_format($d->subtotal, 2) }}</td>
        </tr>
        <tr style="line-height: 13px;">
            <th class="text-left" style="padding-left: 50px;">ภาษีมูลค่าเพิ่ม 7%</th>
            <td class="text-right">{{ number_format($d->vat, 2) }}</td>
        </tr>
        <tr style="line-height: 13px;">
            <th class="text-left" style="padding-left: 50px;">ภาษีหัก ณ ที่จ่าย</th>
            <td class="text-right">{{ number_format($d->withholding_tax, 2) }}</td>
        </tr>
        <tr class="border-tr-bottom" style="line-height: 13px;">
            <td colspan="4" class="text-left">({{ bahtText($d->total) }})</td>
            <th class="text-left" style="padding-left: 50px;">จำนวนเงินรวม</th>
            <td class="text-right">{{ number_format($d->total, 2) }}</td>
        </tr>
    </tbody>
</table>
