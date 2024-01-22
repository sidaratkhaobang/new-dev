@extends('admin.layouts.layout')
@section('page_title', __('borrow_car_lists.page_title'))
@push('styles')
    <style>
        .bg-lease {
            background-color: #824DF3;
        }

        .bg-sold-out {
            background-color: #F37500;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        {{-- <div class="block-header">
            <h3 class="block-title">{{ __('cars.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                </div>
            </div>
        </div> --}}
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])


        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="$license_plate_engine_chassis_list" :label="__('inspection_cars.license_plate_chassis_engine')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_brand" id="car_brand" :list="$brand_list" :label="__('borrow_car_lists.car_brand')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_class" id="car_class" :list="$class_list" :label="__('borrow_car_lists.car_class')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_borrow" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
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
        'block_icon_class' => 'icon-document',
    ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            {{-- <th>@sortablelink('code', __('cars.code'))</th> --}}
                            <th>@sortablelink('license_plate', __('cars.license_plate'))</th>
                            <th>@sortablelink('engine_no', __('cars.engine_no'))</th>
                            <th>@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                            <th>@sortablelink('brand', __('cars.brand'))</th>
                            {{-- <th>@sortablelink('color', __('purchase_requisitions.car_color'))</th> --}}
                            <th>@sortablelink('rental_type', __('cars.rental_type'))</th>
                            <th>@sortablelink('car_storage_age', __('cars.car_storage_age'))</th>
                            <th>@sortablelink('slot', __('cars.slot'))</th>
                            <th>@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                        <tr>
                            <td>{{ $list->firstItem() + $index }}</td>
                            {{-- <td>{{ $d->code }}</td> --}}
                            @if ($d->license_plate)
                                <td>{{ $d->license_plate }}</td>
                            @elseif($d->chassis_no)
                                <td>{{ $d->chassis_no }}</td>
                            @else
                                <td></td>
                            @endif
                            <td>{{ $d->engine_no }}</td>
                            <td>{{ $d->chassis_no }}</td>
                            <td style="width: 30%; white-space: normal;">{{ $d->class_name }} -
                                {{ $d->car_class_name }}</td>
                            {{-- <td>{{ $d->car_color_name }}</td> --}}
                            <td>{{ __('cars.rental_type_' . $d->rental_type) }}</td>
                            <td>{{ $d->car_age_start }}</td>
                            <td>{{ $d->slot }}</td>
                            <td>{!! badge_render(
                                __('borrow_car_lists.class_' . $d->borrow_type),
                                __('borrow_car_lists.status_' . $d->borrow_type),
                            ) !!} </td>
                            <td class="sticky-col text-center">
                                @include('admin.components.dropdown-action', [
                                    'view_route' => route('admin.borrow-car-lists.show', [
                                        'borrow_car_list' => $d,
                                    ]),
                                    'view_permission' => Actions::View . '_' . Resources::BorrowCarList,
                                    'manage_permission' => Actions::Manage . '_' . Resources::BorrowCarList,
                                ])
                            </td>
                        </tr>
                    @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
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


{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
]) --}}
{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_type_id',
    'url' => route('admin.util.select2.car-type'),
    'parent_id' => 'car_brand_id',
]) --}}

@push('scripts')
    <script>
        // $("#car_id").select2({
        //     placeholder: "{{ __('lang.select_option') }}",
        //     allowClear: true,
        //     ajax: {
        //         delay: 250,
        //         url: function(params) {
        //             return "{{ route('admin.util.select2.car-license-plate') }}";
        //         },
        //         type: 'GET',
        //         data: function(params) {
        //             parent_id = $("#car_type").val();
        //             parent_id_2 = $("#job_id").val();
        //             parent_type = $("#job_type").val();
        //             return {
        //                 parent_id: parent_id,
        //                 parent_id_2: parent_id_2,
        //                 parent_type: parent_type,
        //                 s: params.term
        //             }
        //         },
        //         processResults: function(data) {
        //             return {
        //                 results: data
        //             };
        //         },
        //     }
        // });
    </script>
@endpush
