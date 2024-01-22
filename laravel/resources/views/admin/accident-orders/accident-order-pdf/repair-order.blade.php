@php
    $accident_repair_line_price_count = $accident_repair_line_price->count();
    if ($accident_repair_line_price_count > 4) {
        $accident_repair_line_price_count_first = ceil($accident_repair_line_price_count / 2);
        $accident_repair_line_price_count_second = $accident_repair_line_price_count - $accident_repair_line_price_count_first;
    } else {
        $accident_repair_line_price_count_first = $accident_repair_line_price_count;
        $accident_repair_line_price_count_second = $accident_repair_line_price_count;
    }
    // dd($accident_repair_line_price_count)
    // $span_line = $lt_rental_lines->count();
@endphp
<table style="page-break-before: always;" class="table-payment table-collapse">
    <tbody>
        <tr>
            <td>ยี่ห้อ/รุ่น</td>
            <td colspan="6">{{ $car->carClass && $car->carClass->full_name ? $car->carClass->full_name : '-' }}</td>
            <td rowspan="2">ทะเบียน</td>
            <td rowspan="2" colspan="2">{{ $car->license_plate ? $car->license_plate : '-' }}</td>
        </tr>
        <tr>
            <td>ผู้เช่า</td>
            <td colspan="6">{{ $rental ? $rental->customer_name : null }}</td>
        </tr>
        <tr>
            <td>วันที่เกิดเหตุ</td>
            <td colspan="6">วันที่เกิดเหตุ :
                {{ $accident->accident_date ? get_date_time_by_format($accident->accident_date, 'd/m/Y') : null }} /
                วันที่รับแจ้งเหตุ :
                {{ $accident->report_date ? get_date_time_by_format($accident->report_date, 'd/m/Y') . ' เวลา ' . get_date_time_by_format($accident->report_date, 'H:i') . ' น.' : null }}
            </td>
            <td>บ.ประกันภัย</td>
            <td colspan="2">{{ $vmi && $vmi->insurer ? $vmi->insurer->insurance_name_th : null }}</td>
        </tr>
        <tr>
            <td>สถานที่เกิดเหตุ</td>
            <td colspan="6">{{ $accident->accident_place }}</td>
            <td>จดทะเบียน</td>
            <td colspan="2">{{ $car->registered_date }}</td>
        </tr>
        <tr>
            <td>ลักษณะเกิดเหตุ</td>
            <td colspan="6">{{ $accident->accident_description }}</td>
            <td>เริ่มสัญญา</td>
            <td colspan="2">{{ $contract && $contract->date_send_contract ? $contract->date_send_contract : '' }}
            </td>
        </tr>
        <tr>
            <td>ความเสียหายเบื้องต้น</td>
            <td colspan="2">0</td>
            <td>{{ __('accident_informs.mistake_' . $accident->wrong_type) }}</td>
            <td colspan="2" style="width: 170px;">ผลตรวจวัดระดับแอลกอฮอล์</td>
            <td>0 มก.%</td>
            <td>สิ้นสุดสัญญา</td>
            <td colspan="2">
                {{ $contract && $contract->date_return_contract ? $contract->date_return_contract : '' }}</td>
        </tr>
        <tr>
            <td>การจัดซ่อม</td>
            <td colspan="6"></td>
            <td>อายุรถ</td>
            <td colspan="2">{{ $car_age }}</td>
        </tr>
        <tr>
            <td>ทุนประกันภัย</td>
            <td>รถยนต์</td>
            <td colspan="2"></td>
            <td colspan="2">70% ของทุนประกัน</td>
            <td></td>
            <td>เลขรับแจ้ง/เคลม</td>
            <td colspan="2">{{ $accident->worksheet_no }}</td>
        </tr>
        <tr>
            <td>ทุนประกันภัย</td>
            <td>ตู้ เย็น-แห้ง</td>
            <td>เครน</td>
            <td></td>
            <td colspan="2">70% ของทุนประกัน</td>
            <td></td>
            <td>ความคุ้มครอง</td>
            <td colspan="2">{{ __('accident_informs.responsible_' . $accident->responsible) }}</td>
        </tr>
        <tr>
            <td rowspan="6">อู่<br>ศูนย์บริการ<br>เสนอราคา<br>ค่าแรง+ค่าอะไหล่</td>
            <td colspan="5">ค่าแรง+ค่าอะไหล่</td>
            <td>0.00</td>
            <td rowspan="2"><span style="font-size: 20px; font-weight: normal;">ซ่อม</span></td>
            <td rowspan="2"><span style="font-size: 20px; font-weight: normal;">Total Loss</span><br><span
                    style="font-size: 10px;">ประกันจ่ายทุนคืนเต็มจำนวน</span></td>
            <td rowspan="2"><span style="font-size: 20px; font-weight: normal;">PTL</span><br><span
                    style="font-size: 10px;">รับค่าซ่อม/ขายซากเอง</span></td>

        </tr>
        <tr>
            <td colspan="5">อะไหล่ ใหม่ เปลี่ยน</td>
            <td>0.00</td>
        </tr>
        <tr>
            <td colspan="5">TLS ตัดรายการอะไหล่ออก เช่น อะไหล่ที่ ซ่อมได้, ใส่เดิมได้</td>
            <td>0.00</td>
            <td rowspan="4" colspan="3"><br><br>คุณวุฒิภัทร อชิรวราชัย Specialist - RMD ผู้ตรวจสอบ <br>
                ......../......../........</td>
        </tr>
        <tr>
            <td colspan="5">อะไหล่ไม่มีส่วนลด (Net)</td>
            <td>0.00</td>
        </tr>
        <tr>
            <td colspan="5">ซ่อมเครื่องยนต์ เกียร์ ส่งให้ศูนย์ฯ เป็นผู้ดำเนินการ</td>
            <td>0.00</td>
        </tr>
        <tr>
            <td colspan="5">ราคาอะไหล่ที่เสียหายจริงต้องเปลี่ยน</td>
            <td>0.00</td>
        </tr>
        {{-- </tbody>
</table>
<table style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"
    class="table-collapse-modify">
    <tbody> --}}
        <tr>
            <td colspan="3" style="border-right: 1px solid black;">ผู้ดำเนินการ</td>
            <td>ราคา อะไหล่</td>
            <td>ส่วนลด %</td>
            <td style="border-right: 1px solid black;">ส่วนลดที่ได้</td>
            <td style="border-right: 1px solid black;">รวม</td>
            <td colspan="3"
                @if (count($accident_repair_line_price) > 4) @if (count($accident_repair_line_price) % 2 == 0) rowspan="{{ $accident_repair_line_price_count_first + 2 }} 
            @else rowspan="{{ $accident_repair_line_price_count_first + 1 }} @endif
            @else rowspan="{{ $accident_repair_line_price_count_first + 1 }} @endif">
                <br><br><br>คุณสุรนาท
                องนิธิวัฒน์ / คุณปัญญดา นีละคุปต์<br>Assistant
                Director /
                Accident Manager<br>RMD ผู้อนุมัติ ค่าซ่อม น้อยกว่า 200,000 <br> ......../......../........
            </td>
        </tr>
        @foreach ($accident_repair_line_price as $index => $line_price)
            @if ($index < $accident_repair_line_price_count_first)
                @if (count($accident_repair_line_price) > 1)
                    <tr>
                        <td colspan="3" style="border-right: 1px solid black; text-align:left;">
                            {{ $line_price->supplier }}</td>
                        <td>{{ $line_price->spare_parts }}</td>
                        <td>@if($line_price->discount_spare_parts > 0){{ number_format(($line_price->discount_spare_parts / $line_price->spare_parts) * 100, 2) }} % @else 0% @endif</td>
                        <td>{{ $line_price->discount_spare_parts }}</td>
                        <td>{{ number_format($line_price->spare_parts - $line_price->discount_spare_parts,2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="3" style="border-right: 1px solid black; text-align:left;">
                            {{ $line_price->supplier }}</td>
                        <td>{{ $line_price->spare_parts }}</td>
                        <td>@if($line_price->discount_spare_parts > 0){{ number_format(($line_price->discount_spare_parts / $line_price->spare_parts) * 100, 2) }} % @else 0% @endif</td>
                        <td>{{ $line_price->discount_spare_parts }}</td>
                        <td>{{ number_format($line_price->spare_parts - $line_price->discount_spare_parts,2) }}</td>
                        {{-- <td colspan="3" rowspan="{{ $accident_repair_line_price_count_first + 1 }}">
                            <br><br><br>คุณพงษ์พิทยา
                            สมุทรกลิน<br>General
                            Manager<br>ผู้อนุมัติ ค่าซ่อม มากกว่า 200,000 , TTL , PTL <br> ......../......../........
                        </td> --}}
                    </tr>
                @endif
            @elseif($index >= $accident_repair_line_price_count_second)
                <tr>
                    @if (count($accident_repair_line_price) > 1)
                    <td colspan="3" style="border-right: 1px solid black; text-align:left;">
                        {{ $line_price->supplier }}</td>
                    <td>{{ $line_price->spare_parts }}</td>
                    <td>@if($line_price->discount_spare_parts > 0){{ number_format(($line_price->discount_spare_parts / $line_price->spare_parts) * 100, 2) }} % @else 0% @endif</td>
                    <td>{{ $line_price->discount_spare_parts }}</td>
                    <td>{{ number_format($line_price->spare_parts - $line_price->discount_spare_parts,2) }}</td>
                    @endif
                    @if ($index - 1 == $accident_repair_line_price_count_second)
                        <td colspan="3" rowspan="{{ $accident_repair_line_price_count_first + 1 }}">
                            <br><br><br>คุณพงษ์พิทยา
                            สมุทรกลิน<br>General
                            Manager<br>ผู้อนุมัติ ค่าซ่อม มากกว่า 200,000 , TTL , PTL <br> ......../......../........
                        </td>
                    @endif
                </tr>
            @endif


        @endforeach
        <tr>
            <td colspan="6" style="border-right: 1px solid black;">รวม </td>
            <td>{{number_format($total_spare_parts,2)}}</td>

            @if (count($accident_repair_line_price) < 5)
                <td colspan="3" rowspan="2"><br><br><br>คุณพงษ์พิทยา
                    สมุทรกลิน<br>General
                    Manager<br>ผู้อนุมัติ ค่าซ่อม มากกว่า 200,000 , TTL , PTL <br> ......../......../........
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="6" style="border-right: 1px solid black; ">VAT 7% </td>
            <td>{{number_format(($total_spare_parts*7)/100,2)}}</td>
        </tr>
        <tr>
            <td colspan="6" style="border-right: 1px solid black; ">รวม ราคาค่าซ่อม เบื้องต้น </td>
            <td>{{number_format(($total_spare_parts*107)/100,2)}}</td>
            <td colspan="3">ผู้เข้าร่วม ตรวจ ร่วม 3 ฝ่าย</td>
        </tr>
        <tr>
            <td colspan="6" style="border-right: 1px solid black; ">ราคาอะไหล่รอตรวจสอบ </td>
            <td>0.00</td>
            <td>TLS</td>
            <td>ประกันภัย</td>
            <td>ลูกค้า/ผู้ขับขี่</td>
        </tr>
        <tr>
            <td colspan="6" style="border-right: 1px solid black; ">สรุปราคาค่าซ่อมสุทธิ </td>
            <td></td>
            <td>วุฒิภัทร/สุชาติ</td>
            <td>ธงชัย</td>
            <td>ไม่เข้าร่วม</td>
        </tr>
        <tr>
            <td colspan="7" rowspan="9"></td>
            <td colspan="2">วันที่ : อู่เสนอราคา</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : ตรวจ 3 ฝ่าย (VROOM)</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : ประกันอนุมัติ ซ่อม</td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2">TLS กำหนด วันซ่อม 45 วัน</td>
            <td>วันที่เริ่มประกันอนุมัติซ่อม</td>
            <td></td>
        </tr>
        <tr>
            <td>กำหนดวันที่อู่ ซ่อมเสร็จ</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : อู่ ซ่อมเสร็จ</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">อู่นำรถซ่อมเสร็จ ส่งเช็คที่ TOYOTA</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : QA ตรวจผ่าน</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : ประกันอนุมัติซ่อม - อู่ ซ่อมเสร็จ</td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2" colspan="7" style="text-align:left; vertical-align: top;">หมายเหตุ: </td>
            <td colspan="2">วันที่ : ประกันอนุมัติซ่อม - QA ตรวจผ่าน</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">วันที่ : รถเกิดอุบัติเหตุ - QA ตรวจผ่าน</td>
            <td></td>
        </tr>
        <tr>
            <td>ผู้นำเสนอ</td>
            <td colspan="5">ผู้ร่วมตรวจสอบ Risk Management / TLS</td>
            <td colspan="4">ฝ่ายตรวจสอบงานซ่อม (QA)</td>
        </tr>
        <tr>
            <td rowspan="2" style="text-align: center; vertical-align: bottom; height: 100px;">สุนิสา สายกลับ<br>
                ......../......../........</td>
            <td rowspan="2" style="text-align: center; vertical-align: bottom; height: 100px;">คุณวุฒิภัทร
                อชิรวราชัย <br>
                Specialist <br>
                ......../......../........</td>
            <td colspan="4" rowspan="2" style="text-align: center; vertical-align: bottom; height: 100px;">
                <br><br><br>คุณสุรนาท องนิธิวัฒน์ / คุณปัญญดา นีละคุปต์<br>Assistant
                Director /
                Accident Manager <br> อนุมัติ ......../......../........
            </td>
            <td colspan="4">( ) ผ่านการตวจสอบงานซ่อม</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; vertical-align: bottom; height: 100px;">มานะ พุ่มแจ้<br>QA
                ผู้ตรวจ <br>
                ......../......../........</td>
            <td colspan="2" style="text-align: center; vertical-align: bottom; height: 100px;">ชาย บริราช<br>
                Assistant Director, QA, Mainternance
                ......../......../........</td>
        </tr>
    </tbody>
</table>

{{-- <thead>
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
    </tbody> --}}
