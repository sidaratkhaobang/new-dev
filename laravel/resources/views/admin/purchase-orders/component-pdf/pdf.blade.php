@extends('admin.layouts.pdf.pdf-layout')

@section('page_title', $page_title)

@include('admin.layouts.pdf.header', [
    'worksheet_no' => $purchase_order->po_no,
    'rental_refer' => '',
    'request_date' => $purchase_order->request_date,
    'require_date' => $purchase_order->require_date,
    'title_header' => 'ใบสั่ังซื้อ',
])

@section('content')
    <div class="content">
        @include('admin.purchase-orders.component-pdf.content-pdf')
        {{-- @include('admin.purchase-orders.component-pdf.content-pdf-2') --}}
    </div>
@endsection
