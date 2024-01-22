@extends('admin.layouts.layout')
@section('page_title',__('insurances.page_title'))
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
     'text' =>   __('lang.search')     ,
    'block_icon_class' => 'icon-search',
       'is_toggle' => true
])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="row">
                        <div class="col-sm-6">
                            <x-forms.select-option id="insurance_name" :value="$s" :list="$insurance_list"
                                                   :label="__('insurances.insurance_name')"
                                                   :optionals="['required' => false]"/>
                        </div>
                        <div class="col-sm-6">

                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
            {{--            {!! $lists->appends(\Request::except('page'))->render() !!}--}}
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_1')
            <x-btns.add-new btn-text="{{ __('lang.add_data') }}"
                            route-create="{{ route('admin.insurances-companies.create') }}"/>
        @endsection
        @include('admin.components.block-header',[
 'text' =>   __('transfer_cars.total_items')     ,
'block_icon_class' => 'icon-document',
'block_option_id' => '_1',
])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('code', __('insurances.insurance_id'))</th>
                        <th>@sortablelink('insurance_name_th', __('insurances.insurance_name'))</th>
                        <th>@sortablelink('contact_name', __('insurances.insurance_agent'))</th>
                        <th>@sortablelink('contact_email', __('insurances.insurance_email'))</th>
                        <th style="">@sortablelink('contact_tel', __('insurances.insurance_phone'))</th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{(!empty($d->code))?$d->code:"-"}}</td>
                                <td>{{(!empty($d->insurance_name_th))?$d->insurance_name_th:"-"}}</td>
                                <td>{{(!empty($d->contact_name))?$d->contact_name:"-"}}</td>
                                <td>{{(!empty($d->contact_email))?$d->contact_email:"-"}}</td>
                                <td>{{(!empty($d->contact_tel))?$d->contact_tel:"-"}}</td>
                                <td class="sticky-col text-center">

                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.insurances-companies.show', ['insurances_company' => $d]),
                                       'edit_route' => route('admin.insurances-companies.edit', ['insurances_company' => $d]),
                                        'delete_route' => route('admin.insurances-companies.destroy', ['insurances_company' => $d]),
                                        'view_permission' =>
                                            Actions::View . '_' . Resources::InsuranceCompanies,
                                        'manage_permission' =>
                                            Actions::Manage . '_' . Resources::InsuranceCompanies,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="12">" {{__('lang.no_list')}} "</td>
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
@include('admin.components.date-input-script')


