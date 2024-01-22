@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 140px 25px 10px 25px;
        }

        header {
            top: -120px;
            height: 10px;
            bottom: 0px;
        }

        table {
            width: 100% !important;
        }

        thead {
            display: table-header-group;
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

        .table-collapse {
            border-collapse: collapse;
        }

        .table-collapse td,
        .table-collapse th {
            border: 1px solid black;
        }

        .table-collapse .grid_border {
            border-bottom-color: #FFFFFF;
        }

        .table-collapse .lastborder {
            border-bottom: black;
        }

        tbody {
            line-height: 16px;
            margin: 0;
            padding: 0;
        }

        .height-4 {
            height: 30px;
        }

        .height-max {
            height: 200px;
        }

        .font-xl {
            font-size: 20px;
            font-weight: bold;
        }

        footer {
            bottom: 20px;
        }

        .border-none {
            border: 0px !important;
        }

        .first-half {
            float: left;
            width: 70%;
        }

        .second-half {
            float: right;
            width: 20%;
        }

        .block-x {
            display:
        }

        .text-content {
            margin-left: 5%;
            color: #333333;
        }
    </style>
@endpush

<header>
    <div class="text-center">
        <p style="font-size: 28px; padding:3px;">รายละเอียดการส่งเอกสารใบสั่งซ่อมให้ FOD</p>
    </div>
</header>
@section('content')
    <div class="contet">
        @foreach ($data as $index_1 => $item)
            @if ($item->sum_group > 0)
                <div style="page-break-after: always;">
                    <div class="text-left" style="line-height: 12px;">
                        <p style="font-size: 20px;">{{ $item->name }}</p>
                    </div>
                    <div class="text-left">
                        <p style="font-size: 20px; line-height: 12px;">ใบสั่งซ่อมตัวจริงพร้อมแนบคำขอ</p>
                        <p style="font-size: 20px; line-height: 12px;">จำนวนทั้งหมด {{ $item->sum_group }} ฉบับ</p>
                        <p style="font-size: 20px; line-height: 12px;">ใบสั่งซ่อม Call Center จำนวน
                            {{ $item->sum_by_call_center }} ฉบับ</p>
                        <p style="font-size: 20px; line-height: 12px;">ใบสั่งซ่อม TLS จำนวน {{ $item->sum_by_tls }} ฉบับ</p>
                    </div>
                    @foreach ($item->sub_group as $key_sub => $sub_group)
                        <div class="text-left" style="line-height: 12px;">
                            <p style="font-size: 20px;">วันที่ส่งเอกสาร {{ get_thai_date_format($key_sub, 'd F Y') }}</p>
                        </div>
                        <table class="table-collapse">
                            <thead>
                                <tr style="line-height: 14px;">
                                    <th style="width:5%;">ลำดับ</th>
                                    <th style="width:20%;">ทะเบียน</th>
                                    <th style="width:75%;">ชื่อศูนย์บริการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $index = 1;
                                @endphp
                                @foreach ($sub_group as $index_2 => $sub_item)
                                    <tr style="line-height: 13px;">
                                        <td class="text-center">{{ $index }}</td>
                                        <td class="text-center">{{ $sub_item['license_plate'] }}</td>
                                        <td>{{ $sub_item['center'] }}</td>
                                    </tr>
                                    @php
                                    $index++;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
@endsection
