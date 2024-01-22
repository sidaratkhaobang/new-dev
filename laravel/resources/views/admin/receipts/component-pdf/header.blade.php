<header>
    <div class="header-text-l">
        <p class="font-size-16">บริษัท ทรู ลีสซิ่ง จํากัด </p>
        <p class="font-size-16" style="line-height: 12px; margin-top : -10px;">{{ $branch_address }}
        </p>
        <p class="font-size-16">หมายเลขประจำตัวผู้เสียภาษี : {{ $branch_tax_no }}</p>
        <p class="font-size-16">สาขาที่ออกใบกำกับภาษี : {{ $branch_name }}</p>
        <p class="font-size-16">รหัสลูกค้า : {{ $receipt->customer_code }}</p>
        <p class="font-size-16">ชื่อ/ที่อยู่ลูกค้า : (ตามทะเบียนภาษีมูลค่าเพิ่ม)</p>
        <p class="font-size-16" style="line-height: 12px; margin-top : -10px;">{{ $receipt->customer_name }}</p>
        <p class="font-size-16" style="line-height: 12px; margin-top : -10px;">{{ $receipt->customer_address }}</p>
        <p class="font-size-16">หมายเลขประจำตัวผู้เสียภาษี : {{ $receipt->customer_tax_no }}</p>
    </div>
    <div class="header-text-r">
        <p style="font-size: 20px; margin-top: 70px;">{{ $title_header }}</p>
        <p class="text-left" style="font-size: 16px;">เลขที่ : {{ $receipt->worksheet_no }}</p>
        <p class="text-left" style="font-size: 16px;">วันที่ :
            {{ $receipt->created_at ? get_thai_date_format($receipt->created_at, 'd/m/Y') : '-' }}
        </p>
    </div>
</header>
