<table style="margin-top: 100px; margin-bottom: 10px;">
    <tbody>
        <tr>
            <td style="width:30%;">
                <span style="display: inline-block; margin-left: 40px;">ที่ PCD. _____/_________</span>
            </td>
            <td style="width:15%;">
            </td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td style="width:80%; text-align:right;">
            </td>
            <td>
                <span style="display: inline-block;">วันที่ {{ get_thai_date_format(null, 'j F Y') }}</span>
            </td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td style="width:10%; text-align:left;">
                <span style="display: inline-block;">เรื่อง</span>

            </td>
            <td style="text-align:left;">
                <span style="display: inline-block;">ขอรับสมุดคู่มือจดทะเบียนรถยนต์และชุดโอนกรรมสิทธิ์</span>
            </td>
        </tr>
        <tr>
            <td style="width:10%; text-align:left;">
                <span style="display: inline-block;">เรียน</span>
            </td>
            <td style="text-align:left;">
                <span style="display: inline-block;">{{ $leasing_name }}</span>
            </td>
        </tr>
    </tbody>
</table>

<span style="display: inline-block; text-indent: 70px; margin-top: 20px;">
    เนื่องด้วยทาง บริษัท ทรู ลีสซิ่ง จํากัด ได้กําลังดําเนินการ ปิดบัญชีรถยนต์ ที่เช่าซื้อกับ
    {{ $leasing_name }} จํานวนทั้งหมด {{ $car_total }} คัน ( ตามเอกสารแนบ )
</span>
<span style="display: inline-block; text-indent: 70px; margin-top: 20px;">
    บริษัทฯ จึงมีความประสงค์จะขอรับสมุดคู่มือจดทะเบียนรถยนต์ และชุดโอนกรรมสิทธิ์ เพื่อที่จะไปดําเนินการโอนกรรมสิทธิ์เอง
    และขอยกเว้นค่ามัดจําเล่มกับค่าจัดเตรียมเอกสารทั้งหมดด้วย
</span>
<span style="display: inline-block; margin-top: 20px; margin-left: 90px;">
    จึงเรียนมาเพื่อโปรดทราบ และขอขอบคุณอย่างสูง
</span><br>
<br><br>
<table>
    <tbody>
        <tr>
            <td colspan="8" rowspan="2" style="text-align: center; vertical-align: top; height: 100px;">
                <div style="float: right; margin-right:80px;">
                    ขอแสดงความนับถือ
                    <br><br><br>.............................................................<br>
                    (นายอรุชา นิลมณี)
                    <br> ............../............../..............
                </div>
            </td>
        </tr>
    </tbody>
</table>
<br><br><br>
<table>
    <tbody>
        <tr>
            <td style="text-align:left;">
                <span style="display: inline-block;">ต้องการข้อมูลเพิ่มเติมกรุณาติดต่อ</span>

            </td>
        </tr>
        <tr>
            <td style="text-align:left;">
                <span style="display: inline-block;">คุณศรัญญา เอี่ยมทอง โทรศัพท์ 02-859-7967</span>
            </td>
        </tr>
        <tr>
            <td style="width:10%; text-align:left;">
                <span style="display: inline-block;">คุณวิชุดา กงแก้ว โทรศัพท์ 02-859-7974</span>
            </td>
        </tr>
    </tbody>
</table>

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
