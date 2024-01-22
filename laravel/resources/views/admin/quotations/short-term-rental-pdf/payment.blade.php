<table class="table-payment">
    <thead>
        <tr>
            <th style="text-align: left; border-right: 1px solid black; border-bottom: 1px solid black; top: 0px; padding-left: 20px;"
                colspan="2">
                ใบนำฝากชำระเงินค่าสินค้าหรือบริการ (Bill Payment Pay-In-Slip)</th>
            <th style="text-align: right;" colspan="2">{{ $label_payment }}</th>
        </tr>
        <tr>
            <th style="text-align: right; font-weight: normal; font-size: 14px; line-height: 5px; padding-bottom: 10px; border-bottom: 1px solid black;"
                colspan="4">โปรดเรียกเก็บค่าธรรมเนียมจากผู้ชำระเงิน *
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4">
                <table>
                    <tr>
                        <td style="text-align: left; font-weight: bold; width: 45%;">บริษัท ทรู ลีสซิ่ง
                            จํากัด</td>
                        <td style="text-align: left; padding-left: 20px; width: 50%;">
                            สาขา/branch....................................วันที่/date...................................
                        <td>
                    </tr>
                    <tr>
                        <td style="text-align: left; line-height: 13px; width: 45%;">18 อาคารทรูทาวเวอร์
                            <br>
                            ถนนรัชดาภิเษก แขวงห้วยขวาง
                            กรุงเทพฯ 10310 <br> โทร
                            02-859-7878 &nbsp; Fax - <br> เลขประจำตัวผู้เสียภาษี {{ config('services.tax_id') }}</td>
                        <td style="text-align: left; border: 1px solid black; padding-left: 10px; line-height: 13px; width: 50%;"
                           >
                            ชื่อ/Name : <span style="font-weight: bold;">{{ $d->customer_name }}</span> <br>
                            หมายเลขอ้างอิง 1/Ref.1 : <span style="font-weight: bold;">{{ $d->ref_1 }}</span> <br>
                            หมายเลขอ้างอิง 2/Ref.2 : <span style="font-weight: bold;">{{ $d->ref_2 }}</span> <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: left;">
                เพื่อนำเข้าบัญชี <span style="font-weight: bold;">บริษัท ทรู ลีสซิ่ง จำกัด</span>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; line-height: 10px;" colspan="4">
                <span style="width: 10px; font-size:16px;"><img src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                        style="width:10px; height:10px;" alt="">&nbsp;
                    <img src="{{ base_path('storage/logo-pdf/scb.png') }}" style="width:10px; height:10px;"
                        alt="">
                    &nbsp;บมจ. ธนาคารไทยพานิชย์ เลขที่บัญชี <span style="font-weight: bold;">106-3-00114-8 </span>
                    (20/20 บาท)
                    <span style="color: #E04F1A;">ชำระผ่านช่องทางดิจิทัลแบงค์กิ้ง/ATM</span></span>
            </td>
        </tr>
        <tr>
            <td style="text-align: left; line-height: 13px;" colspan="4">
                <span style="width: 10px; font-size:16px;"><img src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                        style="width:10px; height:10px;" alt="">&nbsp; ธนาคารอื่นๆ ที่ให้บริการรับชำระบิล Biller
                    ID : <span style="font-weight: bold;">011553500894905</span>&nbsp; <img
                        src="{{ base_path('storage/logo-pdf/bank.png') }}" style="width:28%; height:13px;"
                        alt="">&nbsp;<span style="color: #E04F1A;">(รับชำระด้วยเงินสดเท่านั้น)</span>
                    <br>(ค่าธรรมเนียมไม่เกิน 5 บาท/รายการในช่องทางอเล็กทรอนิคส์ และไม่เกิน 20
                    บาท/รายการในช่องทางสาขา)</span>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table class="table-collapse">
                    <thead>
                        <tr>
                            <th style="width: 30%;">รับเฉพาะเงินสดเท่านั้น</th>
                            <th style="font-weight: normal;">จำนวนเงิน(บาท)/Amount(Baht)</th>
                            <th style="text-align: right;">{{ number_format($d->total, 2) }}</th>
                            <th style="font-weight: normal;">สำหรับเจ้าหน้าที่ธนาคาร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 30%;">จำนวนเงินเป็นตัวอักษร/Amount in words</td>
                            <td colspan="2" style="font-weight: bold;">{{ bahtText($d->total) }}</td>
                            <td>ผู้รับเงิน....................</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table>
                    <tr>
                        <td style="text-align: left; padding-left:5px;" colspan="2">
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($code, 'C128', 1, 33) }}"
                                alt="barcode" />
                            <p style="line-height: 5px; font-size: 12px;">{{ $code }}</p>
                        </td>
                        <td style="text-align: left;">ชื่อผู้นำฝาก/Deposit by.................... <br>
                            โทรศัพท์/Telephone.......................</td>
                        <td rowspan="2" style="text-align: right;"><img
                                src="data:image/svg;base64, {!! $qrcode !!}"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left; line-height: 13px;">
                            - ท่านสามารถตรวจสอบรายชื่อธนาคารและผู้ให้บริการที่เข้าร่วมได้
                            จากเว็บไซต์ของธนาคารแห่งประเทศไทย <br>
                            - ค่าธรรมเนียมเป็นไปตามเงื่อนไขและข้อกำหนดของแต่ละธนาคาร/ผู้ให้บริการ
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
