@extends('admin.layouts.layout')
@section('page_title',$page_title)
@push('styles')
    <style>
        .block-bg-total-car{
            border-radius: 6px;
            background: var(--neutral-bg-01, #F6F8FC);
            height: 118px;
        }
        .block-loss-ratio-danger{
            border-radius: 6px;
            background: rgba(216, 50, 50, 0.07);
            height: 43px;
        }
        .block-loss-ratio-success{
            border-radius: 6px;
            background: rgba(65, 158, 106, 0.07);
            height: 43px;
        }
        .total-car-font-size{
            font-size: 28px;
        }
        .total-number-font{
            text-align: center;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            text-transform: capitalize;
        }
    </style>

@endpush
@section('content')
    @include('admin.insurance-loss-ratios.sections.index-search')
    @include('admin.insurance-loss-ratios.sections.index-loss-ratio')
    @include('admin.insurance-loss-ratios.sections.index-table')
    <x-forms.hidden id="search-type" :value="true"/>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'policy_number',
    'url' => route('admin.util.select2-insurance-deduct.insurance-policy-reference-list'),
    'parent_id' => 'search-type'
])
@include('admin.components.select2-ajax', [
    'id' => 'license_plate_chassis_no',
    'url' => route('admin.util.select2-insurance-deduct.license-plates'),
    'parent_id' => 'search-type'
])
@include('admin.components.select2-ajax', [
    'id' => 'insurance_company',
    'url' => route('admin.util.select2-insurance-deduct.insurance-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'customer',
    'url' => route('admin.util.select2-insurance.customer'),
])
@include('admin.components.select2-ajax', [
    'id' => 'customer_group',
    'url' => route('admin.util.select2-insurance.customer-group'),
])

