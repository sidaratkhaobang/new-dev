@extends('admin.layouts.pdf.pdf-layout')
@section('page_title', 'attorney')
@push('custom_styles')
    <style>
        .page-break {
            page-break-before: always;
        }

        @page {
            margin: 40px 60px 40px 60px;
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

        .table-collapse-modify {
            border-collapse: collapse;
            border-top: none !important;
        }

        .table-collapse-modify td,
        .table-collapse-modify th {
            border-right: 1px solid black;
            border-left: 1px solid black;
            border-bottom: 1px solid black;
        }

        .table-collapse-modify .grid_border {
            border-bottom-color: #FFFFFF;
        }

        .table-collapse-modify .lastborder {
            border-bottom: black;
        }

        .thead-class {
            display: table-header-group;
        }

        table.table-collapse th {
            line-height: 0.8;
        }

        table.table-collapse td {
            line-height: 0.7;
        }
    </style>
@endpush
@include('admin.ownership-transfers.component-pdf.header')
@section('content')
    <div class="content">
        {{-- <div class="page-break"></div> --}}
        @include('admin.ownership-transfers.component-pdf.transfer-detail', [
            'car_arr' => $car_arr,
            'leasing_name' => $leasing_name,
        ])
    </div>
@endsection
