{{--<header>--}}
<table>
    <tbody>
        <tr>
            <td colspan="6">
                <span>
                    <span style="font-weight: bold">บริษัท ทรู ลีสซิ่ง จำกัด</span>
                    <br>
                    18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก
                    <br>
                    แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพฯ 10310
                    <br>
                    โทร 0-2858-1626 โทรสาร 0-2859-1881
                    <br>
                    เลขประจำตัวผู้เสียภาษี 0115535008949
                </span>
            </td>
            <td colspan="6" class="text-center">
                <u class="font-header">Bill Acceptance</u>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <span>
                    <span style="font-weight: bold"> True Leasing Co.,Ltd </span>
                    <br>
                    18 True Tower, Ratchadaphisek Road.
                    <br>
                    Huai Khwang, Bangkok 10310
                    <br>
                    Tel 0-2858-1626 Fax.0-2859-1881 Tax.ID.0115535008949
                    <br>
                </span>
                <span class="font-sub-header">
                    ใบรับบิล
                </span>
            </td>
            <td colspan="6" class="text-center" style="word-break: break-word; line-height: 0; vertical-align: middle; text-align: center;">
                <p class="font-header">สำหรับเจ้าหน้าที่</p>
                <p class="font-header">
                    บัญชี
                </p>
            </td>
        </tr>
    </tbody>
</table>
<hr>
<div style="width: 50%;float: left;text-align: left">
    <br>
    {{$creditor?->name ?? '-'}}
</div>
<div style="width: 50%;float: right;text-align: right">
    เลขที่ {{$d?->worksheet_no ?? '-'}}
    <br>
    วันที่ {{ optional($d->created_at)->format('Y/m/d') ?? '-' }}
</div>
<div style="clear: both;text-align: center">
    <p>
        <span style="font-weight: bold">บริษัทฯ ได้รับวางบิลของท่านตามรายการข้างล่างนี้</span>
    </p>
</div>
<table class="table">
    <thead>
        <tr>
            <th width="10%">
                ลำดับที่
            </th>
            <th width="10%">
                เลขที่บิล
            </th>
            <th width="10%">
                จำนวนเอกสาร
            </th>
            <th width="5%">

            </th>
            <th width="10%">
                วันที่บิล
            </th>
            <th width="15%">
                จำนวนเงิน
            </th>
            <th>
                หมายเหตุ
            </th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($list))
        @foreach($list as $key => $value)
        <tr class="@if($key % 10 == 0 && $key > 0) page-break @endif">
            <td style="text-align: center">
                {{ ++$key }}
            </td>
            <td style="text-align: center">
                {{ $value ? $value->billing_slip_no ?? '-' : '-' }}
            </td>
            <td style="text-align: center">
                {{ $value ? $value->amount_document ?? '-' : '-' }}
            </td>
            <td>

            </td>
            <td style="text-align: center">
                {{ $value ? optional($value->created_at)->format('Y/m/d') ?? '-' : '-' }}
            </td>
            <td style="text-align: center">
                {{ $value ? $value->amount ?? '-' : '-' }}
            </td>
            <td style="text-align: center">
                {{ $value ? $value->remark ?? '-' : '-' }}
            </td>
        </tr>
        @endforeach

        @else

        @endif
        @if(!empty($repair_bill_id_old))
        <tr>
            <td colspan="6">
                หมายเหตุ : ออกแทนใบรับวางบิล {{$repair_bill_id_old?->worksheet_no}}
            </td>
        </tr>
        @endif


    </tbody>
</table>
<div class="text-center">
    รวมบิล {{count($list)}} ฉบับ รวมยอดเงิน {{number_format($total_bill_price) ?? '0'}} บาท
</div>
<div class="text-start">
    โปรดนำใบรับบิลนี้มารับเงินจากบริษัทฯ ในวันที่ 12/10/2566
</div>
<div>
    <input type="checkbox" style="position: relative;top: 7px;">
    <span style="margin-top: -20px;">
        True Tower ชั้น 19 เฉพาะวันศุกร์ เวลา 13.30 น. ถึง
        16.00 น. โทร.
        02-858-2498
    </span>
</div>

<div>
    <input type="checkbox" style="position: relative;top: 7px;">
    <span style="margin-top: -20px;">
        ธนาคารไทยพาณิชย์ พระราม9 (ชั้น2 อาคาร BELLE หลังเซ็นทรัล พระราม9) วันครบกำหนดวันแรก เวลา 13.00 – 17.30 น. วันอื่นๆ (ยกเว้นวันหยุดธนาคาร) 9.00-17.30 น.(โทร 02-722-2222 กด 0 กด1)
    </span>
</div>
<div>
    <span style="display:block;">
        * แผนกรับวางบิล สาขา ลาดกระบัง เฉพาะวันศุกร์ เวลา 9.00 – 16.00 น. โทร 02-859-7910
    </span>
    <span style="display:block;">
        * แผนกรับวางบิล ชั้น 19 ทรูทาวเวอร์ โทร 02-858-6118,02-858-1974 เฉพาะกรณีแก้ไขบิล
    </span>
    <span style="display:block;">
        * กรุณามารับเงินตามนัดหมาย มิฉะนั่นทางบริษัทฯ สงวนสิทธิ์ที่จะเลื่อนการจ่ายเงินไปในวันที่อื่น
    </span>
    <span style="display:block;">
        * กรณีที่มีการหักภาษี ณ ที่จ่าย บริษัทฯจะออกหนังสือรับรองการหักภาษี ณ ที่จ่าย โดยระบุวันที่หักภาษี
        เป็นวันที่ที่นัดรับเช็คในใบรับวางบิล
    </span>
    <br>

</div>
<div style="text-align: right">
    <span>ผู้รับบิล {{auth()?->user()?->name}} </span>
</div>
<div style="text-align: left;">
    <span style="text-align: left;">ฉบับที่ 3 มีผลบังคับใช้ 01/04/2004</span>
    <span style="text-align: center; margin-left: 150px;">FR-GAC-16-01-01</span>
</div>
{{--</header>--}}