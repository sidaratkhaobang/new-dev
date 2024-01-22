@extends('admin.layouts.layout')
@section('page_title', __('install_equipment_pos.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
    'text' =>   __('lang.search')    ,
   'block_icon_class' => 'icon-search',
      'is_toggle' => true
])
{{--        <div class="block-header">--}}
{{--            <h3 class="block-title">{{ __('locations.total_items') }}</h3>--}}
{{--            <div class="block-options">--}}
{{--                <div class="block-options-item">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="install_equipment_po_no" :value="$install_equipment_po_no" :list="$install_equipment_po_no_list" label="เลขที่ใบสั่งซื้อ" />
                        </div>
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="install_equipment_no" :value="$install_equipment_no" :list="$install_equipment_no_list" label="เลขที่ใบขอติดตั้งอุปกรณ์" />
                        </div>
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="supplier_id" :value="$supplier_id" :list="$supplier_list" label="Supplier" />
                        </div>
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="status_id" :value="$status_id" :list="$status_list" label="สถานะ" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" label="หมายเลขตัวถัง" />
                        </div>
                        <div class="col-sm-4 col-xl-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list" label="ทะเบียนรถ" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('locations.total_items') ,

        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th style="width: 25%;">@sortablelink('worksheet_no', __('install_equipment_pos.worksheet_no'))</th>
                        <th style="width: 25%;">@sortablelink('ie_worksheet_no', __('install_equipment_pos.ie_worksheet_no'))</th>
                        <th style="width: 20%;">@sortablelink('supplier_name', __('install_equipments.supplier_en'))</th>
                        <th style="width: 20%;">{{ __('install_equipments.chasis_no') }}</th>
                        <th style="width: 20%;">{{ __('install_equipments.license_plate') }}</th>
                        <th style="width: 20%;">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($list as $index => $d)
                        <tr>
                            <td>{{ $list->firstItem() + $index }}</td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ ($d->ie_worksheet_no) }}</td>
                            <td>{{ ($d->supplier_name) }}</td>
                            <td>{{ ($d->chassis_no) }}</td>
                            <td>{{ ($d->license_plate) }}</td>
                            <td class="text-center">{!! badge_render(__('install_equipment_pos.class_' . $d->status), __('install_equipment_pos.status_' . $d->status)) !!} </td>
                            <td class="sticky-col text-center">
                                @include('admin.install-equipment-purchase-orders.sections.dropdown-actions')
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
