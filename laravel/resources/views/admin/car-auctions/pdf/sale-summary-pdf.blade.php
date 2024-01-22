@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 200px 30px 30px 30px;
            size: landscape;
        }

        header {
            top: -180px;
            height: 80px;
            bottom: 0px;
        }

        footer {
            bottom: 50px;
        }

        table {
            width: 100% !important;
        }

        thead {
            display: table-header-group;
        }

        .table-collapse {
            border-collapse: collapse;
        }

        .table-collapse td,
        .table-collapse th {
            border: 1px solid black;
        }

        td th {
            font-size: 8px;
        }

        .footer-text-l {
            position: fixed;
            float: left;
            line-height: 14px;
        }

        .footer-text-r {
            position: fixed;
            float: right;
            line-height: 14px;
        }

        .footer {
            margin-right: 100px;
            margin-left: 100px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }



    </style>
@endpush
<header>
    <div class="text-left" style="line-height: 8px; position: fixed;">
        <p style="font-size: 36px; font-weight: bold;">แอพเพิล ออโต้ ออคชั่น (ไทยแลนด์) จำกัด</p>
        <p style="font-size: 24px; font-weight: bold;">1658 ถนนบางนาตราด กม 4 แขวงบางนา กรุงเทพฯ 10260</p>
        <p style="font-size: 24px; font-weight: bold;">โทร 02-399-2299 แฟกซ์ 02-399-2244</p>
    </div>
</header>
@section('content')
    <div class="content">
        <p style="font-size: 20px;">รายการรถยนต์ที่ขายได้</p>
        <table class="table-collapse">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Lot</th>
                    <th>Customer Name</th>
                    <th style="width:20%;">Address</th>
                    <th>Province RFG</th>
                    <th>Reg. No.</th>
                    <th>Year</th>
                    <th>Color</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Grade</th>
                    <th>Sold Price(Inc.Vat)</th>
                    <th>Sold Price(Non.Vat)</th>
                    <th>Vat</th>
                    <th>ค่าภาษี</th>
                    <th>ค่าอื่นๆ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($car_auction as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->lot_date }}</td>
                        <td style="vertical-align: top;">{{ $item->customer }}</td>
                        <td style="vertical-align: top;">ตั้งอยู่เลขที่ 63 หมู่ที่ 7 ถนนรังสิต–นครนายก คลอง 16 ตำบลองครักษ์ อำเภอองครักษ์ จังหวัดนครนายก 26120 โทรศัพท์ 0–2649–5000 โทรสาร 0–3739–5542</td>
                        <td style="vertical-align: top;">-</td>
                        <td style="vertical-align: top;">{{ $item->license_plate }}</td>
                        <td style="vertical-align: top;">{{ $item->car_class_year }}</td>
                        <td style="vertical-align: top;">{{ $item->car_color_name }}</td>
                        <td style="vertical-align: top;">{{ $item->car_brand_name }}</td>
                        <td style="vertical-align: top;">{{ $item->car_class_name }}</td>
                        <td class="text-right">{{ $item->auto_grate }}</td>
                        <td class="text-right">{{ $item->sold_price_total }}</td>
                        <td class="text-right">{{ $item->sold_price_vat }}</td>
                        <td class="text-right">{{ $item->vat }}</td>
                        <td class="text-right">{{ $item->tax }}</td>
                        <td class="text-right">{{ $item->other }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="footer">
            <div class="text-center">
                <p>กรรมการประมูลรถ</p>
            </div>
            <div class="footer-text-l">
                <p>1.___________________________________</p>
                <div class="text-center" style="margin-top: -20px; margin-bottom: 10px;">
                    <p>( นายอรชา นิลมณี)</p>
                    <p>วันที่............/............/............</p>
                </div>
                <p>2.___________________________________</p>
                <div class="text-center" style="margin-top: -20px; margin-bottom: 10px;">
                    <p>( นายชาย บริราช)</p>
                    <p>วันที่............/............/............</p>
                </div>
                <p>3.___________________________________</p>
                <div class="text-center" style="margin-top: -20px; margin-bottom: 10px;">
                    <p>( นายชวาล ต.ศิริสัฒนา)</p>
                    <p>วันที่............/............/............</p>
                </div>
            </div>
            <div class="footer-text-r">
                <p>4.___________________________________</p>
                <div class="text-center" style="margin-top: -20px; margin-bottom: 10px;">
                    <p>( นายทินวรรธน์ บุญเจริญ)</p>
                    <p>วันที่............/............/............</p>
                </div>
                <p>5.___________________________________</p>
                <div class="text-center" style="margin-top: -20px; margin-bottom: 10px;">
                    <p>( นายสุรนาท องนิธิวัฒน์)</p>
                    <p>วันที่............/............/............</p>
                </div>
            </div>
        </div>
    </div>
@endsection
