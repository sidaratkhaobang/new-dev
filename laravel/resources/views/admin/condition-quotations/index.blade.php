@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('block_options_list')
    <div class="block-options-item">
        @canany([Actions::Manage . '_' . Resources::ShortTermConditionQuotation, Actions::Manage . '_' . Resources::LongTermConditionQuotation])
        <x-btns.add-new btn-text="{{ __('condition_quotations.add_new') }}"
            route-create="{{ route($create_route) }}" />
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
                            <th style="width: 5%;"></th>
                            <th style="width: 90%;">{{ __('condition_quotations.condition_type') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr class="{{ $loop->iteration % 2 == 0 ? 'table-active' : '' }}">
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                                <td>{{ $d->condition_type_name }}</td>
                            </tr>
                            <tr style="display: none;">
                                <td></td>
                                <td class="td-table" colspan="2">
                                    <table class="table table-striped">
                                        <thead class="bg-body-dark">
                                            <th style="width: 50px">#</th>
                                            <th style="width: 90%"> {{ __('condition_quotations.condition_name') }}</th>
                                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                                        </thead>
                                        <tbody>
                                            @if (sizeof($d->child_list) > 0)
                                                @foreach ($d->child_list as $index => $item)
                                                    <tr>
                                                        <td style="width: 50px">{{ $index + 1 }}</td>
                                                        <td style="width: 90%">{{ $item->name }}</td>
                                                        <td class="sticky-col text-center">
                                                            @include('admin.components.dropdown-action', [
                                                                'view_route' => route($show_route, [$param => $item]),
                                                                'edit_route' => route($edit_route, [$param => $item]),
                                                                'delete_route' => route($delete_route, [$param => $item]),
                                                                'view_permission' => $view_permission,
                                                                'manage_permission' => $manage_permission,
                                                            ])
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="3">" {{ __('lang.no_list') }} "</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
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

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });
    </script>
@endpush
