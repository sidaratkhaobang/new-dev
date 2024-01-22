<table>
    <tbody>
        <tr>
            <td style="text-align: left; width: 10%;">วันที่ต้องการให้ส่งมอบรถ</td>
            <td style="text-align: left;"><p style="margin: 0px;">ส่งมอบวันที่ &nbsp; {{ ($lt_rental->actual_delivery_date) ? get_thai_date_format($lt_rental->actual_delivery_date, 'd/m/Y') : '-' }}</p></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;"><u>เงื่อนไขการขอเช่ารถเพิ่มเติม</u></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; line-height: 12px;"><span> 1. กรณีผู้ขอเช่าต้องการยกเลิกการขอเช่ารถ
                    ผู้ขอเช่าต้องชำระค่าปรับในอัตราร้อยละ 30
                    ของราคารถที่ขอเช่ามาข้างต้น  ทั้งหมดภายใน7วันหลังจากวันที่ผผู้ขอเช่าได้แจ้งยกเลิกการขอเช่า</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; line-height: 12px;"><span>2.
                    เพื่อเป็นหลักประกันในการเข้าทำสัญญาระหว่างกันผู้ขอเช่าจึงได้ทำการวางเงินประกันการเช่าไว้ดังนี้
                </span>
                <br>
                &nbsp;
                <span style="width: 10px; font-size:16px;"><img src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                        style="width:10px; height:10px;"
                        alt="">&nbsp;วางเงินประกันการเช่าไว้เป็นจำนวน.......................................................บาท</span>
                <br>&nbsp;
                <span style="width: 10px; font-size:16px;"><img src="{{ base_path('storage/logo-pdf/no-check.png') }}"
                        style="width:10px; height:10px;" alt="">&nbsp;ไม่มีการวางเงินประกันการเช่า</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; line-height: 12px;"><span> 3.
                    ให้ถือว่าแบบฟอร์มขอเช่ารถฉบับเป็นส่วนหนึ่งของสัญญาเช่ารถยนต์ที่จะทำขึ้นระหว่างผู้ขอเช่ากับผู้ให้เช่าต่อไป</span>
            </td>
        </tr>
        <tr>
            <td style="text-align: right; width: 100%; line-height: 12px;" colspan="2">
                ผู้ขอเช่า.................................................................................................. <br>
                วันที่........................../................................/..........................................
            </td>
        </tr>
        <tr>
            <td style="text-align: left; vertical-align: top;">เอกสารประกอบการทำสัญญา: </td>
            <td style="text-align: left; line-height: 12px;"><span> 1) หนังสือรับรองบริษัทอายุไม่เกิน 3 เดือนพร้อมกรรมการลงนามรับรองและประทับตราบริษัท</span> <br>
            <span>2) บัตรประชาชนพร้อมทะเบียนบ้าน ของกรรมการผู้มีอำนาจลงนาม และพยาน 1 คน</span>  <br>
        <span>3) หนังสือมอบอำนาจ(ฉบับจริง)ให้กระทำการแทนบริษัทฯ(กรณีกรรมการบริษัทไม่ได้เป็นผู้ลงนาม)</span></td>
        </tr>
    </tbody>
</table>
