@extends('admin.layouts.pdf.pdf-layout')

@push('custom_styles')
    <style>
         header {
            position: static;
            top: 0px;
            height: 250px;
        }

        @page {
            margin: 20px 25px 0px 25px;
        }

        footer {
            bottom: 100px;
        }

        .table-border {
            border-collapse: collapse;
        }

        .table-page {
            page-break-after: always;
        }

        .border-tr {
            border-top: 1px solid #010101 !important;
            border-bottom: 1px solid #010101 !important;
        }

        .border-tr-bottom {
            border-bottom: 1px solid #010101 !important;
        }

        .text-left th,
        td{
            text-align: left;
        }

        .header-text-l {
            position: fixed;
            float: left;
            line-height: 4px;
            max-width: 50%;
        }

        .header-text-r {
            text-align: center;
            float: right;
            margin-right: 20px;
            line-height: 5px;
            max-width: 80%;
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

        .vertical-top {
            vertical-align: top;
        }

    </style>
@endpush

@section('page_title', $page_title)

@include('admin.receipts.component-pdf.header', [
        'branch_tax_no' => $branch_tax_no,
        'branch_name' => $branch_name,
        'branch_address' => $branch_address,
        'receipt' => $d,
        'title_header' => 'ใบเสร็จรับเงิน / ใบกํากับภาษี',
    ])

@section('content')
    <div class="content">
        @include('admin.receipts.component-pdf.section-1')
        @include('admin.receipts.component-pdf.section-2')
        @include('admin.receipts.component-pdf.section-3')
    </div>
@endsection

