<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center;">{{$topic_face_sheet}}</th>
        </tr>
        <tr>
            <th>ลำดับ</th>
            <th>ชื่อผู้ขาย</th>
            <th>รุ่นรถ</th>
            <th>CC</th>
            <th>สีรถ</th>
            <th>หมายเลขเครื่อง</th>
            <th>หมายเลขตัวถัง</th>
            <th>ลูกค้า</th>
            <th>ลักษณะรถ</th>
            <th>LOT</th>
            <th>Leasing</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($registers as $index => $d)
            <tr>
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
                <td>{{ $d->leasing_name }}</td>
                
            </tr>
        @endforeach
    </tbody>
</table>
