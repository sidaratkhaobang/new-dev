{{--Header Section--}}
<style>
    .double-line {
        border-bottom: 2px double #000;
        /* Adjust color and size as needed */
        padding: 20px;
        /* Add padding for spacing */
    }

    .add-border {
        border: 2px solid #000;
    }
</style>

@if(!empty($finance_data))
    @foreach($finance_data as $key => $value)
        @if(!empty($value))
            @foreach($value as $key_data => $value_data)
                @php
                    $sum_car_price = 0;
                @endphp
                <table>
                    <thead>
                    <tr>
                        <th>
                            วันที่
                        </th>
                        <th>
                            {{date('d/m/Y')}}
                        </th>
                    </tr>
                    <tr>
                        <th>
                            เรียน
                        </th>
                        <th colspan="2">
                            {{$key_data ?? null}}
                        </th>
                        <th style="text-align: right">
                            จาก
                        </th>
                        <th>
                            :
                        </th>
                        <th>

                        </th>
                    </tr>
                    <tr>
                        <th>
                            เรื่อง
                        </th>
                        <th colspan="2">
                            แจ้งวันจ่ายเงินและชื่อไฟแนนซ์ แจ้งวันวางบิลและชำระเงิน
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6">
                            ฝ่ายจัดซื้อฯ ขอเรียนให้ทราบว่า รายการรถยนต์ดังกล่าว {{count($value_data)}} คัน ({{$key}})
                            ทางบริษัทฯ
                            จัดไฟแนนซ์กับ
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6">
                            บริษัท ลีสซิ่งไอซีบีซี (ไทย) จำกัด วางบิล 5/5/2565 จ่ายเงิน 13/5/2565 ติดต่อคุณอุบลทิพย์
                            (ออย) โทร. 02-876-7339
                        </th>
                    </tr>
                    </thead>
                    @if(!empty($value_data))
                        <thead>
                        <tr>
                            <th style="border: 1px solid black;">
                                ลำดับที่
                            </th>
                            <th style="border: 1px solid black;">
                                เลขที่ PO
                            </th>
                            <th style="border: 1px solid black;">
                                ชื่อผู้ขาย
                            </th>
                            <th style="border: 1px solid black;">
                                รุ่นรถ
                            </th>
                            <th style="border: 1px solid black;">
                                CC
                            </th>
                            <th style="border: 1px solid black;">
                                สี
                            </th>
                            <th style="border: 1px solid black;">
                                หมายเลขเครื่อง
                            </th>
                            <th style="border: 1px solid black;">
                                หมายเลขตัวถัง
                            </th>
                            <th style="border: 1px solid black;">
                                วันที่รับรถ
                            </th>
                            <th style="border: 1px solid black;">
                                ราคารวม vat
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($value_data as $key_table => $value_table)
                            @php
                                $sum_car_price = $sum_car_price+$value_table?->car_price_total;
                            @endphp
                            <tr>
                                <th style="border: 1px solid black;">
                                    {{$key_table+1}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->po_no}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->seller_name}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->car_modal}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->engine_size}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->car_color}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->chassis_no}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->engine_no}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{$value_table?->delivery_date}}
                                </th>
                                <th style="border: 1px solid black;">
                                    {{number_format($value_table?->car_price_total ?? 0)}}
                                </th>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="9">

                            </td>
                            <td style="border-bottom: 1px double black;">
                                {{number_format($sum_car_price ?? 0)}}
                            </td>
                        </tr>
                        </tbody>
                    @endif
                </table>
            @endforeach
        @endif
    @endforeach
@endif