@extends('admin.layouts.layout')
@section('page_title', __('operations.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
'text' =>   __('lang.search')     ,
'block_icon_class' => 'icon-search',
 'is_toggle' => true,
])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('pdpas.total_items') }}</h3>--}}
        {{--            <div class="block-options">--}}
        {{--                <div class="block-options-item">--}}
        {{--                </div>--}}
        {{--            </div>--}}
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
                            <x-forms.select-option id="branch_id" :value="$branch_id" :list="$branch_lists"
                                                   :label="__('operations.branch')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="$worksheet_lists"
                                                   :label="__('operations.rental_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="$customer_id" :list="$customer_lists"
                                                   :label="__('operations.customer')"/>
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.select-option id="service_type_id" :value="$service_type_id"
                                                   :list="$service_type_lists"
                                                   :label="__('operations.rental_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status_id" :list="$status_list"
                                                   :label="__('short_term_rentals.status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
'text' =>   __('lang.total_list')     ,
'block_icon_class' => 'icon-document',
])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th style="width: 15%;">@sortablelink('worksheet_no', __('operations.rental_no'))</th>
                        <th style="width: 15%;">@sortablelink('branch_name', __('operations.branch'))</th>
                        <th style="width: 15%;">@sortablelink('customer_name', __('operations.customer'))</th>
                        <th style="width: 15%;">@sortablelink('service_type_name', __('operations.rental_type'))</th>
                        <th style="width: 15%;">@sortablelink('created_at', __('operations.rantal_date'))</th>
                        <th class="text-center" style="width: 15%;">@sortablelink('status', __('operations.status'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->branch_name }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ $d->service_type_name }}</td>
                                <td>{{ get_thai_date_format($d->created_at, 'd/m/Y') }}</td>
                                <td class="text-center">
                                    {!! badge_render(__('short_term_rentals.class_' . $d->status), __('short_term_rentals.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'edit_route' => route('admin.operations.edit', ['operation' => $d]),
                                        'view_route' => route('admin.operations.show', ['operation' => $d]),
                                        'manage_permission' => Actions::Manage . '_' . Resources::Operation,
                                        'view_permission' => Actions::View . '_' . Resources::Operation,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif

                    </tbody>
                </table>
            </div>
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')

@include('admin.components.select2-ajax', [
    'id' => 'name',
    'url' => route('admin.util.select2-customer.customers'),
])
