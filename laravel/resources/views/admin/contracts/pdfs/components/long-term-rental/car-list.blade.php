<table class="main-table mt-2">
    <tbody>
        <tr>
            <td class="tetx-center">เอกสารแนบท้ายสัญญา</td>
        </tr>
        <tr>
            <td class="text-center">รายละเอียดและจำนวนรถยนต์ที่เช่ากำหนดระยะเวลาการเช่า และรายละเอียดของอัตราค่าเช่า</td>
        </tr>
        <tr>
            <td>
                <table class="table-border border-all">
                    <tbody>
                        <tr>
                            <td>ลำดับที่</td>
                            <td>รถยนต์/รุ่น</td>
                            <td>ซีซี</td>
                            <td>หมายเลขตัวถัง</td>
                            <td>เลขทะเบียน</td>
                            <td>ค่าเช่า/เดือน
                                <br>บาท (ไม่รวม VAT)
                            </td>
                            <td>วันที่เริ่มให้เช่า</td>
                            <td>วันสิ้นสุดการเช่า</td>
                        </tr>
                        @foreach ($car_list as $index => $car)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $car->carClass?->full_name }}</td>
                            <td>{{ $car->cc }}</td>
                            <td>{{ $car->chassis_no }}</td>
                            <td>{{ $car->license_plate }}</td>
                            <td></td>
                            <td>{{ ($contract->date_send_contract) ? get_date_time_by_format($contract->date_send_contract, 'd/m/y') : '' }}</td>
                            <td>{{ ($contract->date_return_contract) ? get_date_time_by_format($contract->date_return_contract, 'd/m/y') : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>