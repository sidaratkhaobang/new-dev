@if (in_array($rental_service_type, [
    ServiceTypeEnum::SELF_DRIVE,
    ServiceTypeEnum::MINI_COACH,
    ServiceTypeEnum::LIMOUSINE,
]))
    <p class="font-line">เงื่อนไขการชำระเงิน</p>
    <p style="color: #E04F1A; line-height: 3px;">1. กรณีชำระเงินก่อนใช้บริการ</p>
    <p class="line-mt">ไม่มีเงินค้ำประกัน มีคำสั่งเช่าเป็นลายลักษณ์อักษรตามแบบฟอร์ม
        ชำระเงินโดยการโอนเข้าบัญชีบริษัทก่อนวันเดินทาง <br>
        ธนาคารไทยพาณิชย์ <br>
        บริษัท ทรู ลีสซิ่ง จำกัด เลขที่ 106-201360-5 สาขาถนนรัชดาภิเษก3 (ทรูทาวเวอร์)</p>
    <p style="color: #E04F1A; line-height: 3px;">2. กรณีวางบิล</p>
    <p class="line-mt">ไม่มีเงินค้ำประกัน วางบิลเก็บเงินเครดิต 15 วัน
        มีคำสั่งเช่าเป็นลายลักษณ์อักษรตามแบบฟอร์ม</p>
@endif

@if (sizeof($quotation_form) > 0)
<p class="font-mt">ข้อกำหนดและเงื่อนไข</p>
    @foreach ($quotation_form as $index => $item)
        <p class="line-mt">{{ $index + 1 }}. {{ $item->name }} </p>
            @if (sizeof($item->sub_quotation_form_checklist) > 0)
                @foreach ($item->sub_quotation_form_checklist as $key_checklist => $item_checklist)
                <p class="line-mt">&nbsp;&nbsp;- {{ $item_checklist->quotation_form_checklist_name }} </p>
                @endforeach
            @endif
    @endforeach
@endif
