@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('garages.page_title'))
@section('block_options_1')
    <div class="block-options">
        <div class="block-options-item">
            @can(Actions::Manage . '_' . Resources::Garage)
                <x-btns.add-new btn-text="{{ __('garages.add_new') }}" route-create="{{ route('admin.garages.create') }}" />
            @endcan
        </div>
    </div>
@endsection
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
                            <x-forms.select-option :value="null" id="garage" :list="$garage_list" :label="__('garages.garage')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="null" id="garage_type" :list="$garage_type_list" :label="__('garages.garage_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="province_id" :value="$province_id" :list="$province_list" :label="__('creditors.province')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('garages.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_option_id' => '_1',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 25%;">@sortablelink('name', __('garages.garage'))</th>
                            <th style="width: 25%;">@sortablelink('cradle_type', __('garages.garage_type'))</th>
                            <th style="width: 25%;">@sortablelink('cradle_tel', __('garages.tel'))</th>
                            <th style="width: 25%;">@sortablelink('Province.name_th', __('garages.province_district'))</th>
                            <th style="width: 25%;">@sortablelink('cradle_types', __('garages.car_type'))</th>
                            <th style="width: 25%;">@sortablelink('onsite_install_service', __('garages.onsite_install_service'))</th>
                            <th style="width: 20%;" class="text-center">@sortablelink('status', __('garages.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ __('garages.garage_type_' . $d->cradle_type) }}</td>
                                <td>{{ $d->cradle_tel }}</td>
                                <td>{{ $d->Province && $d->Province->name_th ? $d->Province->name_th : null }}
                                    {{ $d->District && $d->District->name_th ? $d->District->name_th : null }}</td>
                                <td>{{ $d->cradle_types }}</td>
                                <td class="text-center">
                                    @if ($d->is_onsite_service)
                                        <i class="fa fa-circle-check text-primary">
                                        @else
                                            <i class="fa fa-circle-xmark text-secondary"></i>
                                    @endif
                                    </i>
                                </td>
                                <td class="text-center">{!! badge_render(__('garages.class_job_' . $d->status), __('garages.status_job_' . $d->status), 'w-25') !!} </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.garages.show', ['garage' => $d]),
                                        'edit_route' => route('admin.garages.edit', ['garage' => $d]),
                                        'delete_route' => route('admin.garages.destroy', ['garage' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::Garage,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Garage,
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
