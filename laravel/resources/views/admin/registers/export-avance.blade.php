<table>
    <thead>
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
            <th>ใบเสร็จ</th>
            <th>ค่าดำเนินการ</th>
            <th>รวม</th>
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
                <td>{{ number_format($d->receipt_avance,2) }}</td>
                <td>{{ number_format($d->operation_fee_avance,2) }}</td>
                <td>{{ number_format($d->total,2) }}</td>
                {{-- <td>{{ $d->total }}</td> --}}

            </tr>
        @endforeach
        <tr>
            <td colspan="10"></td>
            <td>{{ number_format($receipt_avance_total,2) }}</td>
            <td>{{ number_format($operation_fee_avance_total,2) }}</td>
            <td>{{ number_format($total,2) }}</td>
        </tr>
    </tbody>
</table>
