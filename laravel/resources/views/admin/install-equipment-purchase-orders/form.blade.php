@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('page_title_no', $d->worksheet_no)
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    {!! badge_render(__('install_equipment_pos.class_' . $d->status), __('install_equipment_pos.status_' . $d->status)) !!}
@endsection
@push('styles')
    <style>
        .block-content-full {
            background-color: #FFF8E6;
        }

        .block-bordered-custom {
            border: 1px solid #EFB008 !important;
        }


        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            /* position: absolute; */
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* width: 70%; */
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }
    </style>
@endpush
@section('btn-nav')
    <a target="_blank" href="{{ route('admin.install-equipment-purchase-orders.pdf', ['install_equipment_po_id' => $d->id]) }}" class="btn btn-purple">
       <i class="icon-printer me-1"></i> {{ __('install_equipment_pos.print') }}
    </a>
@endsection
@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::EQUIPMENT_ORDER" :id="$d->id" :model="get_class($d)" />
    <form id="save-form">
        @include('admin.install-equipment-purchase-orders.sections.info')
        @include('admin.install-equipment-purchase-orders.sections.accessory')
        <x-forms.hidden id="id" :value="$d->id" />
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.install-equipment-purchase-orders.index',
                    'view' => empty($view_only) ? null : $view_only,
                    'manage_permisssion' => Actions::Manage . '_' . Resources::InstallEquipmentPO,
                ]" />
            </div>
        </div>
    </form>
    @include('admin.components.transaction-modal')
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')

@include('admin.components.form-save', [
    'store_uri' => $store_uri,
])

@include('admin.install-equipment-purchase-orders.scripts.accessory-script')
@include('admin.install-equipment-purchase-orders.scripts.accessory-select2')

@push('scripts')
    <script>
        const view_only =
            @if (isset($view_only))
                @json($view_only)
            @else
                false
            @endif ;
        if (view_only) {
            $('#time_of_delivery').prop('disabled', true);
            $('#payment_term').prop('disabled', true);
            $('#contact').prop('disabled', true);
            $('#car_user').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#quotation_remark').prop('disabled', true);
        }

        function viewHistory() {
            $('#approve-history-modal').modal('show');
        }
    </script>
@endpush
