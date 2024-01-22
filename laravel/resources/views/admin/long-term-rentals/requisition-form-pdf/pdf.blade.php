@extends('admin.layouts.pdf.pdf-layout')

@push('custom_styles')
    <style>
        header {
            position: static;
            top: 0px;
            height: 250px;
        }

        @page {
            margin: 15px 25px 0px 25px;
        }

        .header-text-r {
            /* text-align: center; */
            float: right;
            margin-right: 0px;
            line-height: 5px;
            width: 20%;
        }

        .header-text-l {
            position: fixed;
            float: left;
            line-height: 5px;
            width: 50%;
        }

        .table-collapse {
            border-collapse: collapse;
        }

        .table-collapse td,
        .table-collapse th {
            border: 1px solid black;
        }

        th,
        td {
            text-align: center;
        }

        td {
            padding-left: 5px;
        }

        .right {
            position: absolute;
            right: 0px;
            width: 300px;
            background: #FFFFFF;
            border: 1px solid #C2C2C2;
            bottom: 10px;
        }

        .underline {
            border-bottom: 1px solid black;
        }

        .p-10 {
            padding-left: 10px;
        }
    </style>
@endpush

@include('admin.long-term-rentals.requisition-form-pdf.header')


@section('content')
    <div class="content">
        @include('admin.long-term-rentals.requisition-form-pdf.section-1')

        @include('admin.long-term-rentals.requisition-form-pdf.section-2')
    </div>
@endsection
