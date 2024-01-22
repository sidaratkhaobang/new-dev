@extends('admin.layouts.layout')
@section('page_title',$page_title)
@section('content')
   @include('admin.insurance-deducts.sections.index-search')
    @include('admin.insurance-deducts.sections.index-table')

@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'license_plate_chassis_no',
    'url' => route('admin.util.select2-insurance-deduct.license-plates'),
])
@include('admin.components.select2-ajax', [
    'id' => 'insurance_company',
    'url' => route('admin.util.select2-insurance-deduct.insurance-list'),
])

@include('admin.components.select2-ajax', [
    'id' => 'policy_number',
    'url' => route('admin.util.select2-insurance-deduct.insurance-policy-reference-list'),
])





