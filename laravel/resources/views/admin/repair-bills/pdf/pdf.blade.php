@extends('admin.layouts.pdf.pdf-layout')
@section('page_title', 'Billing')
@push('custom_styles')
<style>
    .font-header {
        font-size: 60px;
        text-underline: black;
    }

    .font-sub-header {
        font-size: 30px;
        text-underline: black;
    }

    .page-break {
        page-break-before: always;
    }

    @page {
        margin: 40px 25px 40px 25px;
    }

    tr {
        page-break-inside: avoid;
    }

    /*header {*/
    /*    !*background: red;*!*/
    /*    !*line-height: 15px;*!*/
    /*    height: 0px;*/
    /*}*/

    .fixed-header {
        /*position: fixed;*/
        /*top: 100%;*/
        /*left: 0;*/
        /*right: 0;*/
        /*background-color: #bde3f5;*/
        /*color: rgb(0, 0, 0);*/
        /*text-align: center;*/
        /*line-height: 15px;*/
    }
</style>
@endpush
@include('admin.repair-bills.component-pdf.header')
@section('content')
<div class="content">
    @include('admin.repair-bills.component-pdf.repair-bill-acceptance-first')
    <div class="page-break"></div>
    @include('admin.repair-bills.component-pdf.repair-bill-acceptance-second')
</div>
@endsection