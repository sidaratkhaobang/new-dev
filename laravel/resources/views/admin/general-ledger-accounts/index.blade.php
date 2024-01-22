@extends('admin.layouts.layout')
@section('page_title', __('general_ledger_accounts.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('general_ledger_accounts.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::GlAccouunt)
                    <x-btns.add-new btn-text="{{ __('general_ledger_accounts.add_new') }}"
                        route-create="{{ route('admin.general-ledger-accounts.create') }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                @include('admin.components.forms.simple-search')
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 30%;">@sortablelink('name', __('general_ledger_accounts.name'))</th>
                            <th style="width: 20%;">@sortablelink('description', __('general_ledger_accounts.description'))</th>
                            <th style="width: 20%;">@sortablelink('account', __('general_ledger_accounts.account'))</th>
                            {{-- <th style="width: 20%;">@sortablelink('name', __('general_ledger_accounts.branch'))</th> --}}
                            <th style="width: 20%;">@sortablelink('customer_group_name', __('general_ledger_accounts.customer_group'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->description }}</td>
                                <td>{{ $d->account }}</td>
                                {{-- <td>{{ $d->branch_name }}</td> --}}
                                <td>{{ $d->customer_group_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.general-ledger-accounts.show', [
                                            'general_ledger_account' => $d,
                                        ]),
                                        'edit_route' => route('admin.general-ledger-accounts.edit', [
                                            'general_ledger_account' => $d,
                                        ]),
                                        'delete_route' => route('admin.general-ledger-accounts.destroy', [
                                            'general_ledger_account' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::GlAccouunt,
                                        'manage_permission' => Actions::Manage . '_' . Resources::GlAccouunt,  
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
@include('admin.components.select2-default')
