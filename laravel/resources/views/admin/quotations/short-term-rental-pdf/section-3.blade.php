<table style="margin-top: 20px;" >
    <tbody>
        <tr style="text-align: center; margin-bottom: 20px;">
            <td>
                <p style="text-align: center; margin-bottom: 20px;">ผู้อนุมัติซื้อ</p>
            </td>
            <td>
                <p style="text-align: center; margin-bottom: 20px;">ผู้เสนอราคา</p>
            </td>
        </tr>
        <tr style="text-align: center; margin-bottom: 10px;">
            <td>________________________________</td>
            <td>________________________________</td>
        </tr>
        <tr>
            <td>(@php echo str_repeat('&nbsp;', 50) @endphp )</td>
            <td>{{ $rental ? $rental->createdBy?->name : ''}}</td>
        </tr>
        <tr>
            <td>วันที่ .............../.............../...............</td>
            <td>{{ $d?->created_at ?  'วันที่ ' . get_thai_date_format($d->created_at, 'd F Y') : 'วันที่ ........../........../..........' }}</td>
        </tr>
    </tbody>
</table>