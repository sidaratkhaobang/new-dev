<table class="table-border border-all mb-1">
    <tbody>
        <tr>
            <td colspan="4"><b>Duration Details / ระยะเวลาเช่า</b></td>
        </tr>
        <tr>
            <td style="width:25%">Pick Up Date / วันที่เริ่มเช่า</td>
            <td class="text-center" style="width:25%">{{ ($rental->pickup_date) ? get_date_time_by_format($rental->pickup_date, 'd/m/y') : '' }}</td>
            <td style="width:15%">Time / เวลา</td>
            <td class="text-center">
                {{ ($rental->pickup_date) ? get_date_time_by_format($rental->pickup_date, 'H:i') : '' }}
            </td>
        </tr>
        <tr>
            <td style="width:25%">Return Date / วันที่ครบกำหนด</td>
            <td class="text-center" style="width:25%">{{ ($rental->return_date) ? get_date_time_by_format($rental->return_date, 'd/m/y') : '' }}</td>
            <td style="width:15%">Time / เวลา</td>
            <td class="text-center">
                {{ ($rental->return_date) ? get_date_time_by_format($rental->return_date, 'H:i') : '' }}
            </td>
        </tr>
    </tbody>
</table>