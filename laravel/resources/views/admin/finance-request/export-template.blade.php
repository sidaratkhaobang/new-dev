{{--Header Section--}}
<table>
    <tbody>
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
        <th>

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
        <td>
            เรื่อง
        </td>
        <td colspan="4">
            แจ้งวันวางบิลและชำระเงิน จำนวน {{count($finance_data)}} คัน (Lot 105/2565 ตัวรถ+อุปกรณ์
            /ค่าอุปกรณ์ใช้เงินบริษัทจ่ายประมาณเดือนพฤษภาคม,มิถุนายน)
        </td>
    </tr>
    <tr>
        <td colspan="5">
            (เฉพาะตัวรถ) บริษัท ลีสซิ่งไอซีบีซี (ไทย) จำกัด (HP) วางบิล 5/5/2565 จ่ายเงิน 13/5/2565 ติดต่อคุณอุบลทิพย์
            (ออย) โทร. 02-876-7339
        </td>
    </tr>
    </tbody>
    <thead>
    <tr style="border: 1px solid black;">
        <th rowspan="2" style="border: 1px solid black;">ลำดับที่</th>
        <th rowspan="2" style="border: 1px solid black;">เลขที่ PO</th>
        <th rowspan="2" style="border: 1px solid black;">ชื่อผู้ขาย</th>
        <th rowspan="2" style="border: 1px solid black;">รุ่นรถ</th>
        <th rowspan="2" style="border: 1px solid black;">CC</th>
        <th rowspan="2" style="border: 1px solid black;">สี</th>
        <th rowspan="2" style="border: 1px solid black;">หมายเลขเครื่อง</th>
        <th rowspan="2" style="border: 1px solid black;">หมายเลขตัวถัง</th>
        <th rowspan="2" style="border: 1px solid black;">วันที่รับรถ</th>
        <th rowspan="2" style="border: 1px solid black;">ราคารวม VAT</th>
        @if(!empty($header_suplier))
            @foreach($header_suplier as $key_header => $value_header)
                <th colspan="3" style="border: 1px solid black;">{{$value_header?->name}}</th>
            @endforeach
        @endif
        <th rowspan="2" style="border: 1px solid black;">
            อุปกรณ์รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            อุปกรณ์+รถ รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ลูกค้า
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            หมายเหตุการซื้อรถ
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            วันนัดส่งมอบรถ
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ค่าเช่าไม่รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            จำนวนงวด
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ทุนประกันตัวรถ
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ทุนประกันอุปกรณ์+GPS
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ทุนประกันรวม
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ค่าเบี้ยประกันประเภท 1 รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            พรบ.รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            พรบ.รวม VAT
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            รวมค่าเบี้ย
        </th>
        <th rowspan="2" style="border: 1px solid black;">
            ค่าจดทะเบียนรถยนต์
        </th>
    </tr>
    @if(!empty($header_suplier))
        <tr>
            @foreach($header_suplier as $key_header => $value_header)
                <th scope="col" style="border: 1px solid black;">PO No.</th>
                <th scope="col" style="border: 1px solid black;">ราคา</th>
                <th style="border: 1px solid black;">รายการ</th>
            @endforeach
        </tr>
    @endif

    </thead>
    <tbody>
    @foreach ($finance_data as $index => $d)
        <tr>
            <td style="border: 1px solid black;">
                {{++$index}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->po_no}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->insurance_lot?->creditor?->name}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->car_model}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->car_cc}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->car_color}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->car?->engine_no}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->car?->chassis_no}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->delivery_date}}
            </td>
            <td style="border: 1px solid black;">
                {{number_format($d?->po?->total ?? 0)}}

            </td>
            @if(!empty($header_suplier))
                @foreach($header_suplier as $key_header => $value_header)
                    @if(array_key_exists($value_header->supplier_id,$d?->install_equipment))
                        @if(!empty($d?->install_equipment[$value_header->supplier_id]))
                            @foreach($d?->install_equipment[$value_header->supplier_id] as $key => $value)
                                <td style="border: 1px solid black;">
                                    {{$value->worksheet_no}}
                                </td>
                                <td style="border: 1px solid black;">
                                    {{$value->accessory_total_price}}
                                </td>
                                <td style="border: 1px solid black;">
                                    {{$value->accessory_name_all}}
                                </td>
                            @endforeach
                        @else
                            <td style="border: 1px solid black;">

                            </td>
                            <td style="border: 1px solid black;">

                            </td>
                            <td style="border: 1px solid black;">

                            </td>
                        @endif
                    @else
                        <td style="border: 1px solid black;">

                        </td>
                        <td style="border: 1px solid black;">

                        </td>
                        <td style="border: 1px solid black;">

                        </td>
                    @endif
                @endforeach
            @endif
            <td style="border: 1px solid black;">
                {{number_format($d?->accessory_total_with_vat)}}
            </td>
            <td style="border: 1px solid black;">
                {{number_format($d?->price_car_with_accessory)}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->customer_data?->customer?->name}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->po?->remark}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->delivery_date}}
            </td>
            <td style="border: 1px solid black;">
                {{$d?->rental_price}}
            </td>
            <td style="border: 1px solid black;">
                7
            </td>
            <td style="border: 1px solid black;">
                {{$d?->sum_insured_car ?? 0}}
            </td>
            <td style="border: 1px solid black;background-color: yellow">
                {{$d?->sum_insured_accessory ?? '-'}}
            </td>
            <td style="border: 1px solid black;background-color: yellow">
                {{$d?->insurance_total ?? '-'}}
            </td>
            <td style="border: 1px solid black;background-color: #fbd4b4">
                {{$d?->premium ?? '-'}}
            </td>
            <td style="border: 1px solid black;color: red;background-color: yellow">
                {{$d?->tax ?? '-'}}
            </td>
            <td style="border: 1px solid black; color: red;background-color: yellow">
                {{$d?->cmi_total ?? '-'}}
            </td>
            <td style="border: 1px solid black;background-color: yellow">
                {{$d?->premium_total ?? '-'}}
            </td>
            <td style="border: 1px solid black;background-color: yellow">
                0
            </td>
        </tr>
    @endforeach
    @include('admin.finance-request.section-exports.summary-table',['table_summary_data' => $table_summary_data])
    <tr></tr>
    <tr></tr>

    @if(!empty($table_summary_car_price_data['car_model_summary']))
        @foreach($table_summary_car_price_data['car_model_summary'] as $key => $value)
            <tr>
                <td>{{$value['car_model'] ?? '-'}}</td>
                <td></td>
                <td style="text-align: right">:</td>
                <td @if($loop->last) style="border-bottom: 1px solid black" @endif>{{number_format($value['total_price'] ?? 0)}}</td>
            </tr>
        @endforeach
    @endif

    <tr>
        <td></td>
        <td>ราคารวมเฉพาะตัวรถ</td>
        <td style="text-align: right">:</td>
        <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['po_price']) ?? 0}}</td>
    </tr>
    <tr></tr>

    @if(!empty($table_summary_car_price_data['accessory_summary']))
        @foreach($table_summary_car_price_data['accessory_summary'] as $key => $value)
            <tr>
                <td>{{$value['supplier_name'] ?? '-'}}</td>
                <td></td>
                <td style="text-align: right">:</td>
                <td>{{number_format($value['total_price'] ?? 0)}}</td>
            </tr>
        @endforeach
    @endif
    <tr>
        <td></td>
        <td>ราคารวมเฉพาะอุปกรณ์</td>
        <td style="text-align: right">:</td>
        <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['accessory_total_with_vat'] ?? 0) }}</td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td style="text-align: right">:</td>
        <td style="border-bottom: 1px double black;">{{number_format($table_summary_car_price_data['price_car_with_accessory'] ?? 0)}}</td>
    </tr>
    </tbody>
</table>
