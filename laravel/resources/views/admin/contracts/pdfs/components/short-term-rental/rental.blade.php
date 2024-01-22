
<table class="table-border border-all mb-1">
    <tbody>
        <tr>
            <td colspan="9"><b>Rate Details / อัตราค่าเช่า</b></td>
        </tr>
        <tr>
            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Rental Rate Per Day</td></tr>
                    <tr><td>ค่าเช่าต่อวัน</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Rental Days</td></tr>
                    <tr><td>ระยะเวลาเช่า</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%">{{ $rental->rental_date }}</td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Days</td></tr>
                    <tr><td>วัน</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Total Amount</td></tr>
                    <tr><td>รวม</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%">{{ number_format($rental_bill->total, 2, '.', ',') }}</td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Driver Service</td></tr>
                    <tr><td>ค่าพนักงานขับรถ</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Rental Days</td></tr>
                    <tr><td>ระยะเวลาเช่า</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Days</td></tr>
                    <tr><td>วัน</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Total Amount</td></tr>
                    <tr><td>รวม</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Rate of Pick Up / Delivery service</td></tr>
                    <tr><td>ค่าส่งรถ - ค่ารับรถ</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Number</td></tr>
                    <tr><td>จำนวน</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Per Round</td></tr>
                    <tr><td>เที่ยว</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Total Amount</td></tr>
                    <tr><td>รวม</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Extra Hour Rate Per Hour</td></tr>
                    <tr><td>ค่าใช้จ่ายเพิ่มเติม/ชั่วโมง</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Total Extra Hour</td></tr>
                    <tr><td>จำนวนชั่วโมง</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Hours</td></tr>
                    <tr><td>ชั่วโมง</td></tr>
                </table>
            </td>

            <td style="width:25%">
                <table class="table-clean">
                    <tr><td>Total Amount</td></tr>
                    <tr><td>รวม</td></tr>
                </table>
            </td>
            <td class="text-center" style="width:25%"></td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="text-center" colspan="6"> Gand Total / รวมทั้งสิ้น (Included VAT /  รวมภาษีมูลค่าเพิ่มแล้ว)</td>
            <td></td>
            <td class="text-center" style="width:25%">{{ number_format($rental_bill->total, 2, '.', ',') }}</td>
            <td style="width:15%">
                <table class="table-clean">
                    <tr><td>Baht</td></tr>
                    <tr><td>บาท</td></tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>