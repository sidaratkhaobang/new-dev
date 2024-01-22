@extends('admin.layouts.layout')
@section('page_title', __('asset_cars.page_title'))

@section('block_options_list')
    @can(Actions::Manage . '_' . Resources::Asset)
        <div class="block-options">
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" style="" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ __('asset_cars.download_excel') }}</button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"
                            onclick="openModalPrint('{{ AssetCarTypeEnum::COST_CENTER }}')">{{ __('asset_cars.cost_center') }}</button>
                    </li>
                    <li><button class="dropdown-item"
                            onclick="openModalPrint('{{ AssetCarTypeEnum::ASSET_MASTER_CAR }}')">{{ __('asset_cars.asset_master_car') }}</button>
                    </li>
                    <li><button class="dropdown-item"
                            onclick="openModalPrint('{{ AssetCarTypeEnum::ASSET_MASTER_SUB_CAR }}')">{{ __('asset_cars.asset_master_sub_car') }}</button>
                    </li>
                    <li><button class="dropdown-item"
                            onclick="openModalPrint('{{ AssetCarTypeEnum::POST_VALUE_CAR }}')">{{ __('asset_cars.post_value_car') }}</button>
                    </li>
                    <li><button class="dropdown-item"
                            onclick="openModalPrint('{{ AssetCarTypeEnum::POST_VALUE_SUB_CAR }}')">{{ __('asset_cars.post_value_sub_car') }}</button>
                    </li>
                </ul>
            </div>
        </div>
    @endcan
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <x-forms.select-option id="lot_id" :value="$lot_id" :list="null" :label="__('asset_cars.lot_no')"
                                :optionals="[
                                    'placeholder' => __('lang.search_placeholder'),
                                    'ajax' => true,
                                    'default_option_label' => $lot_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_class_id" :value="$car_class_id" :list="null" :label="__('asset_cars.car_class')"
                                :optionals="[
                                    'placeholder' => __('lang.search_placeholder'),
                                    'ajax' => true,
                                    'default_option_label' => $car_class_name,
                                ]" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('asset_cars.car_detail')"
                                :optionals="[
                                    'placeholder' => __('lang.search_placeholder'),
                                    'ajax' => true,
                                    'default_option_label' => $car_name,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group row push">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.asset-cars.modals.excel-modal')
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>{{ __('asset_cars.lot_no') }} </th>
                            <th>{{ __('asset_cars.car_class') }} </th>
                            <th>{{ __('asset_cars.car_detail') }} </th>
                            <th class="text-center">{{ __('lang.status') }} </th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $d)
                                <tr>
                                    <td>{{ $d->lot_no }}</td>
                                    <td>{{ $d->car_class_name }}</td>
                                    <td>{{ $d->car_detail }}</td>
                                    <td class="text-center">{!! badge_render(__('asset_cars.class_' . $d->status), __('asset_cars.status_' . $d->status)) !!}</td>
                                    <td>
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.asset-cars.show', [
                                                'asset_car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::Asset,
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
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
@include('admin.components.date-input-script')
@include('admin.asset-cars.scripts.excel-script')
@include('admin.components.select2-ajax', [
    'id' => 'lot_id',
    'url' => route('admin.util.select2-finance.get-lot'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2.car-class'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])
@include('admin.components.select2-ajax', [
    'id' => 'temp_lot_id',
    'url' => route('admin.util.select2-finance.get-lot'),
    'modal' => '#asset-car-excel-modal',
])
@include('admin.components.select2-ajax', [
    'id' => 'temp_car_class_id',
    'url' => route('admin.util.select2.car-class'),
    'modal' => '#asset-car-excel-modal',
])
@include('admin.components.select2-ajax', [
    'id' => 'temp_car_id',
    'url' => route('admin.util.select2.car-license-plate'),
    'modal' => '#asset-car-excel-modal',
])

@push('scripts')
    <script>
        function openModalPrint(data) {
            assetCarExcelVue.clearData();
            clearFilter();
            $('#excel_type').val(data);
            $('#asset-car-excel-modal').modal('show');
        }
    </script>
@endpush
