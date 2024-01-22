@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('page_title_no')
{{$d?->worksheet_no ?? null}}
@endsection
@section('page_title_sub')
@if($d?->status)
{!! badge_render(
__('finance_contract.status_' . $d?->status . '_class'),
__('finance_contract.status_' . $d?->status),
null,
) !!}
@endif
@endsection
@section('content')
@include('admin.components.creator')
<form id="save-form">
    <input type="hidden" name="bill_slip_id" value="{{$d?->id}}">
    @include('admin.repair-bills.sections.repair-bill-detail')
    @include('admin.repair-bills.sections.repair-bill-list')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.submit-group :optionals="['url' => 'admin.repair-bills.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::RepairBill]" />
        </div>
    </div>
</form>
@endsection
@include('admin.components.form-save', [
'store_uri' => route('admin.repair-bills.store'),
])
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.repair-bills.scripts.script-repair-bill-list')
@include('admin.components.select2-ajax', [
'id' => 'bill_recipient',
'url' => route('admin.util.select2-repair.bill-recipient'),
])
@include('admin.components.select2-ajax', [
'id' => 'center_id',
'url' => route('admin.util.select2-repair.creditor-services'),
])
@push('scripts')
<script>
    @if(isset($view))
    $('#center_id').prop('disabled', true)
    $('#bill_recipient').prop('disabled', true)
    $('#billing_date').prop('disabled', true)
    $('#receive_money_date').prop('disabled', true)
    $('.worksheet_no').prop('disabled', true)
    $('.total_document').prop('disabled', true)
    $('.repair_bill_price').prop('disabled', true)
    $('.remark').prop('disabled', true)
    @endif
</script>
@endpush