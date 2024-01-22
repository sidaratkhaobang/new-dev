@extends('admin.layouts.pdf.pdf-layout')

@push('custom_styles')
    <style>
                .table-collapse td,
                .table-collapse th {
                    border: 1px solid black;
                }
                .table-collapse {
                    border-collapse: collapse;
                }

    </style>
@endpush


@section('page_title', $page_title)




    <div class="content">
        <div style="text-align: left; margin-bottom: 0px;">
            <h1 class="text-center">Loss Ratio ของแต่ละบริษัทประกันภัย</h1>
        </div>
        @include('admin.insurance-loss-ratios.insurance-loss-ratios-pdf.pdf-table')
    </div>


