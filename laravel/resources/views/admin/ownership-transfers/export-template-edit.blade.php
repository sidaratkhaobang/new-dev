<table>
    <thead>
        {{-- <tr>
            <th colspan="10" style="text-align: center;">{{$test}}</th>
        </tr> --}}
        <tr>
            <th>TRANSFERS_ID</th>
            <th>ลำดับ</th>
            <th>Leasing</th>
            <th>รุ่นรถ</th>
            <th>CC</th>
            <th>สีรถ</th>
            <th>หมายเลขเครื่องยนต์</th>
            <th>หมายเลขตัวถัง</th>
            <th>ทะเบียน</th>
            <th>วันที่รับเล่มคืน</th>
            <th>วันที่ครอบครองรถ</th>
            <th>วันที่ส่งเล่มทะเบียนคืนบัญชี</th>
            <th>วันที่ออกใบเสร็จ</th>
            <th>เลขที่ใบเสร็จ</th>
            <th>ค่าใบเสร็จ</th>
            <th>ค่าบริการ</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($ownership_transfers as $index => $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $index+1 }}</td>
                <td>{{ $d->leasing_name }}</td>
                <td>{{ $d->car_class }}</td>
                <td>{{ $d->cc }}</td>
                <td>{{ $d->car_color }}</td>
                <td>{{ $d->engine_no }}</td>
                <td>{{ $d->chassis_no }}</td>
                <td>{{ $d->license_plate }}</td>
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
