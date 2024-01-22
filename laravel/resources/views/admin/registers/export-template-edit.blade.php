<table>
    <thead>
        {{-- <tr>
            <th colspan="10" style="text-align: center;">{{$test}}</th>
        </tr> --}}
        <tr>
            <th>REGISTEREDS_ID</th>
            <th>ลำดับ</th>
            <th>ชื่อผู้ขาย</th>
            <th>รุ่นรถ</th>
            <th>CC</th>
            <th>สีรถ</th>
            <th>หมายเลขเครื่องยนต์</th>
            <th>หมายเลขตัวถัง</th>
            <th>ลูกค้า</th>
            <th>ลักษณะรถ</th>
            <th>LOT</th>
            <th>ลักษณะรถตามกรมขนส่ง</th>
            <th>สีที่จดทะเบียน</th>
            <th>วันที่จดทะเบียนเสร็จ</th>
            <th>วันที่ได้รับข้อมูลมาบันทึก</th>
            <th>เลขทะเบียนรถ</th>
            <th>วันที่หมดอายุภาษีรถยนต์</th>
            <th>วันที่ออกใบเสร็จ</th>
            <th>เลขที่ใบเสร็จ</th>
            <th>ค่าภาษี</th>
            <th>ค่าบริการ</th>
            <th>ลิงก์แนบที่อยู่ไฟล์สำเนาทะเบียนรถ</th>
            <th>การได้รับเล่มทะเบียน (yes,no)</th>
            <th>การได้รับป้ายเหล็ก (yes,no)</th>
            <th>การได้รับป้ายภาษี (yes,no)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registers as $index => $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->index }}</td>
                <td>{{ $d->creditor_name }}</td>
                <td>{{ $d->car_class }}</td>
                <td>{{ $d->cc }}</td>
                <td>{{ $d->car_color }}</td>
                <td>{{ $d->engine_no }}</td>
                <td>{{ $d->chassis_no }}</td>
                <td>{{ $d->customer_name }}</td>
                <td>{{ $d->car_characteristic }}</td>
                <td>{{ $d->lot_no }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

            </tr>
        @endforeach
    </tbody>
</table>
