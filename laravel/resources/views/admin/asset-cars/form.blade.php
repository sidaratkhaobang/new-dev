@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@push('custom_styles')
    <style>
        .grey-text {
            color: #858585;
            padding-left: 15px;
        }

        .size-text {
            font-size: 16px;
            font-weight: normal;
            color: #000000;
        }
    </style>
@endpush

@section('content')
    <form id="save-form">
        <x-blocks.block :title="__('asset_cars.data_car')">
            @if (sizeof($asset_accessory) > 0)
                <x-slot name="btn_option">
                    <button type="button" class="btn btn-primary" onclick="openAccessoryModal()">ข้อมูลอุปกรณ์เสริม</button>
                    @include('admin.asset-cars.modals.accessory-modal')
                </x-slot>
            @endif
            @include('admin.asset-cars.sections.car')
        </x-blocks.block>

        <x-blocks.block :title="__('asset_cars.data_leasing')">
            @include('admin.asset-cars.sections.leasing')
        </x-blocks.block>

        <x-blocks.block :title="__('asset_cars.data_cost_center')">
            @include('admin.asset-cars.sections.cost-center')
        </x-blocks.block>

        <x-blocks.block :title="__('asset_cars.data_asset_car')">
            @include('admin.asset-cars.sections.asset-car')
        </x-blocks.block>

        @if (sizeof($asset_accessory) > 0)
            <x-blocks.block :title="__('asset_cars.data_asset_sub_car')">
                @include('admin.asset-cars.sections.asset-sub-car')
            </x-blocks.block>
        @endif

        <x-blocks.block :title="__('asset_cars.data_post_value_car')">
            @include('admin.asset-cars.sections.post-value-car')
        </x-blocks.block>

        @if (sizeof($asset_accessory) > 0)
            <x-blocks.block :title="__('asset_cars.data_post_value_sub_car')">
                @include('admin.asset-cars.sections.post-value-sub-car')
            </x-blocks.block>
        @endif

        <x-blocks.block>
            <x-forms.hidden id="id" :value="$d->id" />
            <div class="row">
                <div class="col-sm-12 text-end">
                    <a class="btn btn-secondary" href="{{ route('admin.asset-cars.index') }}">{{ __('lang.back') }}</a>
                </div>
            </div>
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.sweetalert')
@push('scripts')
    <script>
        function openAccessoryModal() {
            $('#accessory-modal').modal('show');
        }
    </script>
@endpush
