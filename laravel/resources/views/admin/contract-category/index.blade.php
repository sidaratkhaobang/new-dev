@extends('admin.layouts.layout')
@section('page_title', __('contract_category.index.page_title'))
@section('block_options_1')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::ContractMasterDataCategory)
            <x-btns.add-new btn-text="{{ __('contract_category.index.search.btn-create-page') }}" route-create="{{route('admin.contract-category.create')}}" />
        @endcan
    </div>
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search')    ,
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="category_name" :value="$category_name" :label="__('contract_category.index.search.label')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.total_items') ,
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_1',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('name', __('contract_category.index.table.contract-category'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.contract-category.show', ['contract_category' => $item]),
                                        'edit_route' => route('admin.contract-category.edit' , ['contract_category' => $item]),
                                        'delete_route' => route('admin.contract-category.destroy' , ['contract_category' => $item]),
                                        'view_permission' => Actions::View . '_' . Resources::ContractMasterDataCategory,
                                        'manage_permission' => Actions::Manage . '_' . Resources::ContractMasterDataCategory,
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

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
