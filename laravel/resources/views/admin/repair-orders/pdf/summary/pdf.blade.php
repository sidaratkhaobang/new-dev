@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 140px 25px 10px 25px;
        }

        header {
            top: -100px;
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
    <div class="header-text-l">
        <p style="font-size: 20px; padding:3px;" class="text-center ">สรุปข้อมูลใบเสนอราคา (ประจำวัน)</p>
        <div class="first-half">
            <p style="font-size: 20px; padding:3px; margin-left:180px;">ใบควบคุมเอกสารภายนอก (ใบเสนอรายการซ่อม)</p>
        </div>
        <div class="second-half">
            <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: left; width: 70%;" alt="">
        </div>
    </div>
</header>

@section('content')
    <div class="contet">
        <table class="table-collapse">
            <thead>
                <tr style="line-height: 14px;">
                    <th style="width:5%;">ลำดับ</th>
                    <th style="width:15%;">วันที่และเวลา</th>
                    <th style="width:15%;">ทะเบียน</th>
                    <th style="width:15%;">ศูนย์บริการ</th>
                    <th style="width:35%;">รายการซ่อม</th>
                    <th style="width:15%;">ชื่อผู้ทำเอกสาร</th>
                </tr>
            </thead>
            <tbody>
                @if ($data)
                    @foreach ($data as $index => $item)
                        <tr style="line-height: 13px;">
                            <td style="vertical-align: top;" class="text-center">{{ $index + 1 }}</td>
                            <td style="vertical-align: top;" class="text-center">
                                {{ get_thai_date_format($item->created_at, 'd/m/Y H:i') }}</td>
                            <td style="vertical-align: top;" class="text-center">{{ $item->license_plate }}</td>
                            <td style="vertical-align: top;">{{ $item->center }}</td>
                            <td style="vertical-align: top;">
                                @foreach ($item->sub_line as $item_line)
                                    {{ $item_line->code_name }} <br>
                                @endforeach
                            </td>
                            <td style="vertical-align: top;" class="text-center">{{ $item->user_name }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="6">
                            "{{ __('lang.no_list') }} "
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>
@endsection
