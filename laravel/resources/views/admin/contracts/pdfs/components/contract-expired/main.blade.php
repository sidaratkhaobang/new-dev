<table class="table-border main-table mt-2">
    <tbody>
        <tr class="lh-30">
            <td colspan="2" class="text-right">วันที่ {{ get_date_time_by_format($today, 'd/m/y') }}</td>
        </tr>
        <tr class="lh-30">
            <td colspan="2">เรื่อง แจ้งรถครบอายุสัญญาเช่าเดือน {{ get_thai_month($month) }}</td>
        </tr>
        <tr class="lh-30">
            <td colspan="2">เรียน {{ $customer->name }}</td>
        </tr>
        <tr>
            <td colspan="2">
                @php echo str_repeat('&nbsp;', 10) @endphp ตามที่ท่านได้เช่ารถยนต์ไว้กับบริษัทฯนั้น บริษัทฯ
                ใคร่ขอแจ้งรายละเอียดรถยนต์ที่จะครบสัญญาเช่าในเดือน {{ get_thai_month($month) }} {{ $year }} ให้ท่านทราบ (รายละเอียดตามเอกสารแนบ)
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="table-border border-all">
                    <thead>
                        <tr>
                            <td>ที่</td>
                            <td>ยึี่ห้อ / รุ่น</td>
                            <td>ทะเบียน</td>
                            <td>หมดสัญญา</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contract->contractline as $key => $line)
                        <tr>
                            <td>{{ $key + 1}}</td>
                            <td>{{ $line->car?->carClass?->full_name }}</td>
                            <td>{{ $line->car?->license_plate }}</td>
                            <td>{{ ($line->return_date) ? get_date_time_by_format($line->return_date, 'd/m/y') : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                @php echo str_repeat('&nbsp;', 10) @endphp ทั้งนี้ ขอให้ท่านหรือ หน่วยงานต้นสังกัด โปรดแจ้งบริษัทฯ ว่ามีความประสงค์ที่จะ
                <u>ส่งคืน</u> หรือ <u>เช่ารถยนต์ใหม่</u> ให้ คุณแสงสีย์ แดงชาติ / ฝ่ายการคลาด ทราบ (โทร.02-859-7802)
                เพื่อที่บริษัทฯ จะได้ดำเนินการตามประสงค์ของท่านต่อไป
            </td>
        </tr>
        <tr>
            <td colspan="2">@php echo str_repeat('&nbsp;', 10) @endphp จึงเรียนมาเพื่อโปรดดำเนินการ</td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td class="text-center" width="50%">ขอแสดงความนับถือ</td>
        </tr>
        <tr class="lh-30"></tr>
        <tr>
            <td width="50%"></td>
            <td class="text-center" width="50%">(นายสุรนาท องนิธิวัฒน์)</td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td class="text-center" width="50%">ผู้ช่วยผู้อำนวยการ/หัวหน้าฝ่ายบริหารความเสี่ยง</td>
        </tr>
    </tbody>
</table>

