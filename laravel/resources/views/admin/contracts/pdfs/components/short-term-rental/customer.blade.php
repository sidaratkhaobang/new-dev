
<table class="table-border border-all mb-1">
    <tbody>
        <tr>
            <td colspan="2"><b>Renter Information / Vehicle Details / ข้อมูลลลูกค่้า / ข้อมูลรถ</b></td>
        </tr>
        <tr>
            <td style="width:25%">Name of Rental / ชื่อลูกค้า / ผู้เช่า</td>
            <td>{{ $rental->customer_name }}</td>
        </tr>
        <tr>
            <td>Address / ที่อยู่ปัจจุบัน</td>
            <td>{{ $rental->customer_address }}</td>
        </tr>
        <tr>
            <td>Phone Number / เบอร์โทรศัพท์</td>
            <td>{{ $rental->customer_tel }}</td>
        </tr>
        <tr>
            <td>Email / อีเมล</td>
            <td>{{ $rental->customer_email }}</td>
        </tr>
        <tr>
            <td>Vehicle License No. / ทะเบียน</td>
            <td>{{ $car->license_plate }}</td>
        </tr>
        <tr>
            <td>Brand / Model / ยี่ห้อ / รุ่น</td>
            <td>{{ $car->car_class_full_name }}</td>
        </tr>
    </tbody>
</table>