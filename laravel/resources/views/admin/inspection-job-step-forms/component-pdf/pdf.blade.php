@extends('admin.layouts.pdf.pdf-layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        @page {
            margin: 140px 25px 10px 25px;
        }

        header {
            top: -110px;
            height: 130px;
            bottom: 0px;
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

        .content {
            top: 100px;
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
            /* position: fixed;
                bottom: -100px;
                left: 0px;
                right: 0px;
                height: 50px;
                color: rgb(0, 0, 0);
                line-height: 20px; */
            /* position: fixed;
                    left: 0px;
                    right: 0px;
                    height: 150px;
                    bottom: 0px;
                    margin-bottom: -150px; */
        }

        .separated {
            border-bottom: 1px solid black;
            padding-top: 50px;
        }
    </style>
@endpush

@include('admin.layouts.pdf.header2', [])




@section('content')
    <div class="content" style="">
        @include('admin.inspection-job-step-forms.component-pdf.page-pdf-1')
    </div>
@endsection
