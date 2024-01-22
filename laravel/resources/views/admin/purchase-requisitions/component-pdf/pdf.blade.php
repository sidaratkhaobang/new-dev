@extends('admin.layouts.pdf.pdf-layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
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

        .content {
            /* margin-top: 200px; */
            /* background-color: #386072; */
            padding: 0px;
            margin: 0px;
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

        .right-p {
            font-size: 15px;
            text-align: right;
            margin: 2px;
            line-height: 12px;
        }

        .right-span {
            font-size: 13px;
            text-align: left;
            padding: 3px;
        }

        table {
            width: 100%;
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

        footer {
            position: fixed;
            bottom: -100px;
            left: 0px;
            right: 0px;
            height: 50px;
            color: rgb(0, 0, 0);
            line-height: 20px;
        }

        .separated {
            border-bottom: 1px solid black;
            padding-top: 50px;
        }
    </style>
@endpush
@if (strcmp($print_type, 'RFQ') == 0)
    @include('admin.layouts.pdf.header', [
        'worksheet_no' => $purchase_requisition->pr_no,
        'rental_refer' => $purchase_requisition->rental_refer,
        'request_date' => $purchase_requisition->request_date,
        'require_date' => $purchase_requisition->require_date,
        'title_header' => 'ใบคำขอเสนอราคา',
    ])
@else
    @include('admin.layouts.pdf.header', [
        'worksheet_no' => $purchase_requisition->pr_no,
        'rental_refer' => $purchase_requisition->rental_refer,
        'request_date' => $purchase_requisition->request_date,
        'require_date' => $purchase_requisition->require_date,
        'title_header' => 'ใบขอซื้อรถยนต์',
    ])
@endif



@section('content')
    <div class="content">
        @php
            $total_car = 0;
        @endphp
        @include('admin.purchase-requisitions.component-pdf.page-pdf-1')

        {{-- @include('admin.purchase-requisitions.component-pdf.page-pdf-2') --}}

        {{-- @include('admin.purchase-requisitions.component-pdf.page-pdf-3') --}}
    </div>
@endsection
