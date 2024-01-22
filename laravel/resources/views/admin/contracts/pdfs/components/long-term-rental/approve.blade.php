<table class="main-table mt-2" style="page-break-after: always">
    <tbody>
        <tr>
            <td>@php echo str_repeat('&nbsp;', 5) @endphp 
                สัญญาฉบับนี้ทำขึ้นสองฉบับมีข้อความถูกต้องตรงกัน โดยคู่สัญญาทั้งสองฝ่ายได้อ่านและเข้าใจข้อความในสัญญานี้โดยตลอด 
                และเห็นว่าถูกต้องตรงตามเจตนา เพื่อเป็นหลักฐานในการนี้คู่สัญญาทั้งสองฝ่ายโดยผู้มีอำนาจกระทำการจึงได้ลงลายมือชื่อ 
                และประทับตราสำคัญบริษัท(ถ้ามี) และต่างยึดถือสัญญานี้ไว้ฝ่ายละหนึ่งฉบับ</td>
        </tr>
        
        <tr style="line-height:50px;">
            <td class="text-center"><b>บริษัท ทรู ลีสซิ่ง จำกัด 
                @php echo str_repeat('&nbsp;', 5) @endphp 
                (ผู้ให้เช่า)</b> 
            </td>
        </tr>
        @foreach ($signer_host_list as $signer_host)
            <tr style="line-height:50px;">
                <td class="text-center">ลงชื่อ
                    @php echo str_repeat('.', 120) @endphp 
                    ({{ $signer_host->signer_type }})
                </td>
            </tr>
            <tr >
                <td class="text-center">({{ $signer_host->name }})</td>
            </tr>
        @endforeach

        <tr style="line-height:50px;">
            <td class="text-center"><b>
                {{ $lt_rental->customer_name }}
                @php echo str_repeat('&nbsp;', 5) @endphp 
                (ผู้เช่า)</b> 
            </td>
        </tr>
        @foreach ($signer_renter_list as $signer_renter)
            <tr style="line-height:50px;">
                <td class="text-center">ลงชื่อ
                    @php echo str_repeat('.', 120) @endphp 
                    ({{ $signer_renter->signer_type }})
                </td>
            </tr>
            <tr >
                <td class="text-center">({{ $signer_renter->name }})</td>
            </tr>
        @endforeach
    </tbody>
</table>