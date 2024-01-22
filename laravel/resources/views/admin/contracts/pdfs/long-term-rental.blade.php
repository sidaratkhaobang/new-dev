@extends('admin.layouts.pdf.pdf-layout')
@push('custom_styles')
    <style>
        @page {
            margin: 100px 25px 10px 25px;
        }

        header {
            top: -80px;
            height: 100px;
            bottom: 0px;
        }

        .table-border {
            border-collapse: collapse;
        }

        .table-page {
            page-break-after: always;
        }

        .border-all td {
            border: 1px solid #010101 !important;
        }

        .border-tr {
            border-top: 1px solid #010101 !important;
            border-bottom: 1px solid #010101 !important;
        }

        .border-tr-bottom {
            border-bottom: 1px solid #010101 !important;
        }

        .border-right {
            border-right: 1px solid #010101 !important;
        }

        .content {
            top: 20px !important;
        }

        .border-top-0 td {
            border-top: 0px solid #010101 !important;
        }

        /* .right-p {
                font-size: 15px;
                text-align: right;
                margin: 2px;
                line-height: 12px;
            }

            .right-span {
                font-size: 13px;
                text-align: left;
                padding: 3px;
            } */

        table {
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        /*
            .text-left {
                text-align: left;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            } */

        tr {
            line-height: 16px;
        }

        .lh-30 {
            line-height: 30px;
        }

        /* .separated {
                border-bottom: 1px solid black;
                padding-top: 50px;
            } */

        .mb-1 {
            margin-bottom: 1rem;
        }

        .table-border td {
            font-size: 18px;
            padding-left: 5px;
            padding-bottom: 2rem;
        }

        .table-clean tr td {
            border: 0px solid black !important;
            padding: 0px;
            line-height: 9px;
        }
        p {
            line-height: 2px
        }
        .mr-1 {
            margin-left: 1rem;
        }
        .header-text-l {
            /* top: 0; */
            text-align: left;
            line-height: 3px;
        }

        .header-text-r {
            text-align: right;
            margin-right: 20px;
            margin: 0px;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .main-table {
            padding: 0 1rem;
        }

    </style>
@endpush
<header>
    <div class="header-text-l" style="">
        <img src="{{ base_path('storage/logo-pdf/true.jpg') }}" style="float: left;" alt="">
        <p style="font-size: 20px; padding:3px;">บริษัท ทรู ลีสซิ่ง จํากัด </p>
        <p style="font-size: 15px;">18 อาคารทรูทาวเวอร์ ถนนรัชดาภิเษก แขวงห้วยขวาง กรุงเทพฯ 10310</p>
    </div>
    <div class="header-text-r" style="">
        <p style="font-size: 15px;">สัญญาเลขที่ {{ $contract->worksheet_no }}</p>
    </div>
</header>

@section('content')
    <div class="content" style="">
        @include('admin.contracts.pdfs.components.long-term-rental.main')
        @include('admin.contracts.pdfs.components.long-term-rental.approve')
        @include('admin.contracts.pdfs.components.long-term-rental.car-list')
    </div>
@endsection

