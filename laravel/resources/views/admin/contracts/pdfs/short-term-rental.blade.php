@extends('admin.layouts.pdf.pdf-layout')
{{-- @section('page_title', $page_title) --}}
@push('custom_styles')
    <style>
        @page {
            margin: 10px 25px 10px 25px;
        }

        header {
            position: relative;
            top: 0px;
            height: 100px; 
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
            top: 0px !important;
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
            line-height: 12px;
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
            padding-left: 5px;
        }

        .table-clean tr td {
            border: 0px solid black !important;
            padding: 0px;
            line-height: 9px;
        }
        p {
            line-height: 1.5px
        }
        .mr-1 {
            margin-left: 1rem;
        }

        .table-document  td:nth-child(2) {
            width: 80px !important; 
        }
    </style>
@endpush
<header>
    <div class="header-text-center" style="">
        <p style="font-size: 16px;font-weight:bold">True Leasing Co., Ltd.</p>
        <p style="font-size: 14px;">Auto Rental Service</p>
        <p style="font-size: 14px;">www.trueleasing.co.th</p>        
    </div>
    <div class="header-text-r" style="">
        <p style="font-size: 14px;font-weight:bold">Document Number / หมายเลขเอกสาร</p>
        <p style="font-size: 14px;">{{ $contract->worksheet_no }}</p>        
    </div>
</header>

@section('content')
    <div class="content" style="">
        @include('admin.contracts.pdfs.components.short-term-rental.branch')
        @include('admin.contracts.pdfs.components.short-term-rental.customer-type')
        @include('admin.contracts.pdfs.components.short-term-rental.customer')
        @include('admin.contracts.pdfs.components.short-term-rental.duration')
        @include('admin.contracts.pdfs.components.short-term-rental.rental')
        @include('admin.contracts.pdfs.components.short-term-rental.payment')
        @include('admin.contracts.pdfs.components.short-term-rental.document')
        @include('admin.contracts.pdfs.components.short-term-rental.approval')
    </div>
@endsection

