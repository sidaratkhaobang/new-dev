@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="name" :value="$name" :list="$name_list" :label="__('auction_places.name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="contact_name" :value="$contact_name" :list="$contact_list"
                                :label="__('auction_places.contact_name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
    @section('block_options_btn')
        @can(Actions::Manage . '_' . Resources::AuctionPlace)
            <x-btns.add-new btn-text="{{ __('lang.add') . __('auction_places.page_title') }}"
                route-create="{{ route('admin.auction-places.create') }}" />
        @endcan
    @endsection
    @include('admin.components.block-header', [
        'text' => __('transfer_cars.total_items'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_btn',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">@sortablelink('name', __('auction_places.name'))</th>
                        <th style="width: 25%;">@sortablelink('name', __('auction_places.contact_name'))</th>
                        <th style="width: 30%;">@sortablelink('name', __('auction_places.address'))</th>
                        <th class="text-center" style="width: 10%;">@sortablelink('name', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->contact_name }}</td>
                                <td style="white-space: normal;">
                                    <x-forms.tooltip :title="$d->address" :limit="65">
                                        </x-forms.tooltip>
                                </td>
                                <td class="text-center" style="width: 100px;">
                                    {!! badge_render(__('auction_places.class_' . $d->status), __('auction_places.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.auction-places.show', [
                                            'auction_place' => $d,
                                        ]),
                                        'edit_route' => route('admin.auction-places.edit', [
                                            'auction_place' => $d,
                                        ]),
                                        'delete_route' => route('admin.auction-places.destroy', [
                                            'auction_place' => $d,
                                        ]),
                                        'manage_permission' => Actions::Manage . '_' . Resources::AuctionPlace,
                                        'view_permission' => Actions::View . '_' . Resources::AuctionPlace,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">" {{ __('lang.no_list') }} "</td>
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
