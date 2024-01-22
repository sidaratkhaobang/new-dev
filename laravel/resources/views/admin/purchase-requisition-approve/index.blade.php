@extends('admin.layouts.layout')
@section('page_title', __('purchase_requisitions.page_title_approve'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
     'text' =>   __('lang.search')    ,
    'block_icon_class' => 'icon-search',
       'is_toggle' => true
])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('purchase_requisitions.total_items') }}</h3>--}}
        {{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                   placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="pr_no" :value="$pr_no" :list="$pr_list"
                                                   :label="__('purchase_requisitions.pr_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_type" :value="$rental_type" :list="$rental_type_list"
                                                   :label="__('purchase_requisitions.rental_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :label="__('purchase_requisitions.status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('purchase_requisitions.total_items')  ,
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>@sortablelink('pr_no', __('purchase_requisitions.pr_no_car'))</th>
                        <th>@sortablelink('rental_type', __('purchase_requisitions.rental_type'))</th>
                        <th>@sortablelink('request_date', __('purchase_requisitions.request_date'))</th>
                        <th>@sortablelink('require_date', __('purchase_requisitions.require_date'))</th>
                        <th class="text-center">@sortablelink('status', __('purchase_requisitions.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->pr_no }}</td>
                                {{-- <td>-</td> --}}
                                {{-- <td>-</td> --}}
                                {{-- <td>-</td> --}}
                                <td>{{ __('purchase_requisitions.rental_type_' . $d->rental_type) }}</td>
                                <td>{{ get_thai_date_format($d->request_date, 'd/m/Y') }}</td>
                                <td>{{ get_thai_date_format($d->require_date, 'd/m/Y') }}</td>
                                <td class="text-center">
                                    {!! badge_render(
                                        __('purchase_requisitions.status_' . $d->status . '_class'),
                                        __('purchase_requisitions.status_' . $d->status . '_text'),
                                        null,
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.purchase-requisition-approve.show', [
                                            'purchase_requisition_approve' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::PurchaseRequisitionApprove,
                                        'manage_permission' => Actions::Manage . '_' . Resources::PurchaseRequisitionApprove,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
    </div>
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@push('scripts')
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        jQuery(function () {
            Dashmix.helpers(['js-flatpickr', 'js-datepicker']);
        });
    </script>
@endpush
