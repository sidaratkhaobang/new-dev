@extends('admin.layouts.pdf.pdf-layout')

@push('custom_styles')
    <style>
        header {
            position: fixed;
            top: -270px;
            height: 200px;
        }

        @page {
            margin: 290px 25px 40px 25px;
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

        .thead-class {
            display: table-header-group;
        }

        .text-left th,
        td {
            text-align: left;
        }

        .header-text-l {
            position: fixed;
            float: left;
            line-height: 5px;
            width: 50%;
        }

        .header-text-r {
            text-align: center;
            float: right;
            margin-right: 20px;
            line-height: 5px;
            width: 50%;
        }

        .left-p {
            font-size: 18px;
            margin-bottom: 2px;
        }

        hr {
            border: 0.5px solid;
        }

        .ml-50 {
            margin-left: 50px;
            margin-top: 5px;
            margin-bottom: 8px;
        }
        .mg-footer {
            margin-left: 25px;
            margin-top: -5px;
            margin-bottom: 0px;
        }

        .display-left {
            display: flex;
            text-align: left;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .pd-r10 {
            padding-right: 10px;
        }

        .pd-l10 {
            padding-left: 10px;
        }

        th,
        td {
            text-align: center;
        }

        .right {
            position: absolute;
            right: 0px;
            width: 250px;
            background: #FFFFFF;
            border: 1px solid #C2C2C2;
            bottom: -10px;
        }

        .table-payment {
            /* border-collapse: none !important; */
            background: #FFFFFF;
            border: 1px solid black;
            margin-top: 35px;
        }

        .checkbox-style {
            margin: 0px 4px 0px 4px;
            /* padding-top: -400px; */
        }

        .separated {
            border-bottom: 1px solid black;
            padding-top: 20px;
            margin: 10px;
        }

        .font-line {
            font-weight: bold;
            line-height: 5px;
        }

        .font-mt {
            font-weight: bold;
            margin-top: 5px;
        }

        .line-mt {
            line-height: 14px;
            margin-top: -8px;
        }

        .p-line {
            line-height: 10px;
        }

        .table-no-border {
            border-top: none !important;
            border-bottom: none !important;
        }

        .table-collapse tbody tr:last-child {
            border-bottom: 1px solid #000 !important;
        }
    </style>
@endpush

@section('page_title', $page_title)

@include('admin.quotations.component-pdf.header', [
    'customer_name' => $d->customer_name,
    'customer_address' => $d->customer_address,
    'customer_tel' => $d->customer_tel,
    'created_at' => $d->created_at,
    'qt_no' => $d->qt_no,
    'service_type_name' => null,
])

@section('content')
    <div class="content">
        <div style="text-align: left; margin-bottom: 0px;">
            <p> บริษัท ทรู ลีสซิ่ง จํากัด
                มีความยินดีเสนอราคาค่าเช่าให้ท่านพิจารณาดังต่อไปนี้</p>
        </div>
        @include('admin.quotations.long-term-rental-pdf.section-1')
        @include('admin.quotations.long-term-rental-pdf.section-2')

        @include('admin.quotations.component-pdf.section-3', [
            'user_name' => $d->user_name,
            'user_tel' => $d->user_tel,
            'user_email' => $d->user_email,
        ])
    </div>
@endsection

