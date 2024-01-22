@extends('admin.layouts.layout')
@section('page_title', __('auction_reject_reasons.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::AuctionRejectReason)
            <x-btns.add-new btn-text="{{ __('auction_reject_reasons.add_new') }}" route-create="{{ route('admin.auction-reject-reasons.create') }}" />
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                @include('admin.components.forms.simple-search')
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 2px;">#</th>
                            <th>@sortablelink('name', __('auction_reject_reasons.name'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $index + $list->firstItem() }}</td>
                                <td>{{ $d->name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action',[
                                        'view_route' => route('admin.auction-reject-reasons.show', ['auction_reject_reason' => $d]),
                                        'edit_route' => route('admin.auction-reject-reasons.edit', ['auction_reject_reason' => $d]),
                                        'delete_route' => route('admin.auction-reject-reasons.destroy', ['auction_reject_reason' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::AuctionRejectReason,
                                        'manage_permission' => Actions::Manage . '_' . Resources::AuctionRejectReason,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
