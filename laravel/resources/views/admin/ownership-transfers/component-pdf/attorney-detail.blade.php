<div class="header-text-r" style="margin-bottom: 100px;">
    <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: right;" alt="">
</div>
<table>
    <tbody>
        <tr>
            <td style="width:40%;"></td>
            <td style="width:15%;">
                <span style="display: inline-block; font-weight: bold;">แบบขอหนังสือมอบอำนาจช่วง</span>
            </td>
            <td style="width:30%;">
                <span style="display: inline-block; margin-left: 40px;">เลขที่ PCD. _____/____________</span>
            </td>
            <td style="width:15%;">
            </td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td></td>
            <td style="width:60%; text-align:left; font-weight: bold;">
                <span>บริษัท ทรู ลีสซิ่ง จำกัด</span>
            </td>
            <td></td>
        </tr>
        <tr>
            <td style="width:60%; text-align:right;">
            </td>
            <td>
                <span style="display: inline-block;">วันที่</span>
                <div style="display: inline-block; border-bottom: 1px solid black; width: 200px; text-align: center;">
                    <span style=" text-align:center;">{{ get_thai_date_format(null, 'j F Y') }}</span>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<hr>
<span>รายละเอียด</span><br>
<span>ชื่อ</span>
<div style="display: inline-block; border-bottom: 1px solid black; width: 310px; text-align: center;">
    <span style="text-align:center;">นางสาวศรัญญา เอี่ยมทอง</span>
</div>
<span>ส่วนงาน</span>
<div style="display: inline-block; border-bottom: 1px solid black; width: 306px; text-align: center;">
    <span style="text-align:center;">ทะเบียน</span>
</div><br>
<span>ฝ่าย</span>
<div style="display: inline-block; border-bottom: 1px solid black; width: 240px; text-align: center;">
    <span style="text-align:center;">PCD</span>
</div>
<span>ขอหนังสือมอบอำนาจช่วงของบริษัทฯ จำนวน</span>
<div style="display: inline-block; border-bottom: 1px solid black; width: 130px; text-align: center;">
    <span style="text-align:center;">{{ $car_total }}</span>
</div>
<span>ชุด เพื่อดำเนินการ</span>

@foreach ($leasing_arr as $leasing)
    <div style="display: inline-block; border-bottom: 1px solid black; width: 675px; text-align: left;">
        <span style="text-align:center;">- เพื่อโอนกรรมสิทธิ์จาก {{ $leasing['name'] }} มาเป็น บริษัท ทรู ลีสซิ่ง จำกัด
            {{ $leasing['count'] }} คัน</span>
    </div>
@endforeach

@if (count($leasing_arr) < 5)
    <br>
    @for ($i = 0; $i < 5 - count($leasing_arr); $i++)
        <div style="display: inline-block; border-bottom: 1px solid black; width: 675px; text-align: left;">
        </div><br>
    @endfor
@endif

<span>ของรถยนต์หมายเลขทะเบียน ดังต่อไปนี้</span>
<div style="display: inline-block; border-bottom: 1px solid black; width: 675px; text-align: left;">
    <span style="text-align:center;">(ตามเอกสารแนบ)</span>
</div><br><br>
<div style="display: inline-block; border-bottom: 1px solid black; width: 675px; text-align: left;">
</div><br>
<div style="display: inline-block; border-bottom: 1px solid black; width: 675px; text-align: left;">
</div>

<table>
    <tbody>
        <tr>

            <td colspan="4" rowspan="2" style="text-align: center; vertical-align: top; height: 100px;">
                <br><br><br>ผู้ขอ.............................................................<br>
                (นางสาวศรัญญา เอี่ยมทอง)
                <br> ............../............../..............<br>
                ผู้จัดเอกสาร................................................<br>
            </td>

            <td colspan="4" rowspan="2" style="text-align: center; vertical-align: top; height: 100px;">
                <br><br><br>ผู้อนุมัติ.............................................................<br>
                (นายอรุชา นิลมณี)
                <br> ............../............../..............
            </td>
        </tr>
    </tbody>
</table>

{{-- <div class="page-break table-payment table-collapse"> --}}
<table class="page-break table-collapse">
    <thead>
        <th style="width:25px; text-align: center;">ที่</th>
        <th style="width:10%; text-align: center;">ทะเบียน</th>
        <th style="width:15%; text-align: center;">เลขเครื่อง</th>
        <th style="width:15%; text-align: center;">เลขตัวถัง</th>
        <th style="width:20%; text-align: center;">ยี่ห้อรุ่น</th>
        <th style="width:10%; text-align: center;">วันที่จดทะเบียน</th>
        <th style="width:20%; text-align: center;">ลีสซิ่ง</th>
    </thead>
    <tbody>
        @foreach ($car_arr as $index => $car)
            <tr>
                <td style="text-align: center;">
                    {{ $index + 1 }}
                </td>
                <td style="text-align: center;">
                    {{ $car['license_plate'] }}
                </td>
                <td style="text-align: center;">
                    {{ $car['engine_no'] }}
                </td>
                <td style="text-align: center;">
                    {{ $car['chassis_no'] }}
                </td>
                <td style="text-align: center;">
                    {{ $car['car_class'] }}
                </td>
                <td style="text-align: center;">
                    {{ custom_date_format($car['registered_date']) }}
                </td>
                <td style="text-align: center;">
                    {{ $car['leasing_name'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
